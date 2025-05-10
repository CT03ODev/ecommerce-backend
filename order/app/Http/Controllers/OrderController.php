<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $orders = Order::with(['orderItems'])
            ->where('user_id', $userId)
            ->latest()
            ->get();
        
        return response()->json($orders);
    }

    /**
     * Store a newly created order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required|exists:addresses,id',
            'variant_ids' => 'required|array',
            'variant_ids.*' => 'required|exists:product_variants,id',
            'quantities' => 'required|array',
            'quantities.*' => 'required|integer|min:1',
            'payment_method' => ['required', new Enum(PaymentMethod::class)],
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            // Tính tổng tiền từ các variants
            $subtotal = 0;
            $variantsData = [];

            foreach ($request->variant_ids as $index => $variantId) {
                $variant = ProductVariant::findOrFail($variantId);
                $quantity = $request->quantities[$index] ?? 1;

                // Kiểm tra số lượng tồn kho
                if ($variant->stock_quantity < $quantity) {
                    throw new \Exception("Insufficient stock for variant ID: {$variantId}");
                }

                $itemTotal = $variant->price * $quantity;
                $subtotal += $itemTotal;

                $variantsData[] = [
                    'variant' => $variant,
                    'quantity' => $quantity
                ];
            }

            // Tính các khoản phí
            $tax_amount = $subtotal * 0.05; // 5% tax
            $shipping_amount = 10; // $10 shipping fee
            $discount_amount = 0; // No discount for now
            $total_amount = $subtotal + $tax_amount + $shipping_amount - $discount_amount;

            // Chuyển đổi sang VND cho VNPAY (1 USD = 24,500 VND)
            $total_amount_vnd = round($total_amount * 24500);

            DB::beginTransaction();
            try {
                // Tạo order với trạng thái pending_payment cho VNPAY
                $order = new Order();
                $order->order_number = 'ORD-' . strtoupper(Str::random(10));
                $order->user_id = $request->user()->id;
                $order->address_id = $request->address_id;
                $order->total_amount = $total_amount;
                $order->tax_amount = $tax_amount;
                $order->shipping_amount = $shipping_amount;
                $order->discount_amount = $discount_amount;
                $order->status = $request->payment_method === PaymentMethod::VNPAY->value 
                    ? OrderStatus::PENDING_PAYMENT->value 
                    : OrderStatus::PENDING->value;
                $order->notes = $request->notes;
                $order->created_by = $request->user()->id;
                $order->save();

                // Tạo order items
                foreach ($variantsData as $data) {
                    $variant = $data['variant'];
                    $quantity = $data['quantity'];
                    $subtotal = $variant->price * $quantity;

                    $orderItem = new OrderItem([
                        'order_id' => $order->id,
                        'product_id' => $variant->product_id,
                        'product_variant_id' => $variant->id,
                        'quantity' => $quantity,
                        'unit_price' => $variant->price,
                        'total_price' => $variant->price * $quantity,
                        'subtotal' => $subtotal
                    ]);
                    $orderItem->save();
                }

                // Nếu thanh toán qua VNPAY
                if ($request->payment_method === PaymentMethod::VNPAY->value) {
                    $vnpayUrl = $this->createVnpayPaymentUrl($order->id, $total_amount_vnd, $order->order_number);
                    DB::commit();
                    return response()->json([
                        'payment_url' => $vnpayUrl,
                        'order_id' => $order->id
                    ], 200);
                }

                // Cập nhật số lượng tồn kho cho các phương thức thanh toán khác
                foreach ($variantsData as $data) {
                    $variant = $data['variant'];
                    $quantity = $data['quantity'];
                    $variant->stock_quantity -= $quantity;
                    $variant->save();
                }

                // Tạo transaction cho COD
                $transaction = new Transaction([
                    'order_id' => $order->id,
                    'transaction_type' => 'payment',
                    'payment_method' => $request->payment_method,
                    'gateway_transaction_id' => 'TXN-' . strtoupper(Str::random(12)) . '-' . time(),
                    'amount' => $total_amount,
                    'currency' => 'USD',
                    'status' => 'pending'
                ]);
                $transaction->save();

                DB::commit();
                return response()->json($order->load('orderItems'), 201);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => $e->getMessage()], 422);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    private function createVnpayPaymentUrl($orderId, $amount, $txnRef)
    {
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_ReturnUrl = config('app.url') . "/api/vnpay/callback?orderId=$orderId";
        $vnp_TmnCode = config('services.vnpay.tmn_code');
        $vnp_HashSecret = config('services.vnpay.hash_secret');

        $vnp_OrderInfo = "Payment for order";
        $vnp_OrderType = "billpayment";
        $vnp_Amount = $amount * 100; // VNPAY yêu cầu số tiền * 100
        $vnp_Locale = "vn";
        $vnp_IpAddr = request()->ip();
        $vnp_CreateDate = date('YmdHis');

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $vnp_CreateDate,
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $txnRef,
        );

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

        return $vnp_Url;
    }

    /**
     * Display the specified order.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $userId = $request->user()->id;
        
        $order = Order::with(['address', 'orderItems', 'payments'])
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
        
        if (!$order) {
            abort(404);
        }

        return response()->json($order);
    }

    /**
     * Update the order address and notes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $userId = $request->user()->id;
        
        $order = Order::where('id', $id)
            ->where('user_id', $userId)
            ->first();
        
        if (!$order) {
            abort(404);
        }
        
        // Only allow updates if order is in pending status
        if ($order->status !== OrderStatus::PENDING->value) {
            return response()->json([
                'error' => 'Only pending orders can be updated'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'address_id' => 'nullable|exists:addresses,id',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->has('address_id')) {
            $order->address_id = $request->address_id;
        }
        
        if ($request->has('notes')) {
            $order->notes = $request->notes;
        }
        
        $order->updated_by = $userId;
        $order->save();

        return response()->json($order);
    }

    /**
     * Cancel the specified order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request, $id)
    {
        $userId = $request->user()->id;
        
        $order = Order::where('id', $id)
            ->where('user_id', $userId)
            ->first();
        
        if (!$order) {
            abort(404);
        }
        
        // Only allow cancellation if order is in pending or processing status
        if (!in_array($order->status, [OrderStatus::PENDING->value, OrderStatus::PROCESSING->value])) {
            return response()->json([
                'error' => 'Only pending or processing orders can be cancelled'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'cancellation_reason' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $order->status = OrderStatus::CANCELLED->value;
        
        if ($request->has('cancellation_reason')) {
            $order->notes = $order->notes 
                ? $order->notes . "\n\nCancellation reason: " . $request->cancellation_reason
                : "Cancellation reason: " . $request->cancellation_reason;
        }
        
        $order->updated_by = $userId;
        $order->save();

        return response()->json($order);
    }

    /**
     * Get orders for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function myOrders(Request $request)
    {
        $userId = $request->user()->id;
        $orders = Order::with(['orderItems', 'payments'])
            ->where('user_id', $userId)
            ->latest()
            ->get();
        
        return response()->json($orders);
    }
}