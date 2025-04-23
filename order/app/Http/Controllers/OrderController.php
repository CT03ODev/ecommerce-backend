<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
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
        $orders = Order::with(['orderItems', 'payments'])
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
            'user_id' => 'required|exists:users,id',
            'address_id' => 'required|exists:addresses,id',
            'variant_ids' => 'required|array',
            'variant_ids.*' => 'required|exists:product_variants,id',
            'quantities' => 'required|array',
            'quantities.*' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            DB::beginTransaction();

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

            // Tạo order
            $order = new Order();
            $order->order_number = 'ORD-' . Str::random(10);
            $order->user_id = $request->user_id;
            $order->address_id = $request->address_id;
            $order->total_amount = $total_amount;
            $order->tax_amount = $tax_amount;
            $order->shipping_amount = $shipping_amount;
            $order->discount_amount = $discount_amount;
            $order->status = OrderStatus::PENDING->value;
            $order->notes = $request->notes;
            $order->created_by = $request->user()->id ?? null;
            $order->save();

            // Tạo order items và cập nhật tồn kho
            foreach ($variantsData as $data) {
                $variant = $data['variant'];
                $quantity = $data['quantity'];

                // Tạo order item
                $orderItem = new OrderItem([
                    'order_id' => $order->id,
                    'product_id' => $variant->product_id,
                    'variant_id' => $variant->id,
                    'quantity' => $quantity,
                    'unit_price' => $variant->price,
                    'total_price' => $variant->price * $quantity,
                ]);
                $orderItem->save();

                // Cập nhật số lượng tồn kho
                $variant->stock_quantity -= $quantity;
                $variant->save();
            }

            DB::commit();
            return response()->json($order->load('orderItems'), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 422);
        }
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