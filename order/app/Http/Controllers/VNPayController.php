<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class VNPayController extends Controller
{
    public function callback(Request $request)
    {
        $orderId = $request->orderId;
        if (!$orderId) {
            abort(404);
        }

        // Lấy thông tin từ VNPay callback
        $vnp_ResponseCode = $request->vnp_ResponseCode;
        $vnp_TxnRef = $request->vnp_TxnRef;
        $vnp_Amount = $request->vnp_Amount;
        $vnp_TransactionNo = $request->vnp_TransactionNo;
        $vnp_BankCode = $request->vnp_BankCode;
        $vnp_PayDate = $request->vnp_PayDate;

        try {
            // Tìm đơn hàng
            $order = Order::with('orderItems.productVariant')->findOrFail($orderId);
            
            // Kiểm tra trạng thái thanh toán
            if ($vnp_ResponseCode !== '00') {
                DB::beginTransaction();
                try {
                    // Xóa order items trước
                    $order->orderItems()->delete();
                    // Xóa order
                    $order->delete();
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Failed to delete order: ' . $e->getMessage());
                }
                return redirect(config('services.vnpay.frontend_failed_url') . '?error=payment_failed');
            }

            DB::beginTransaction();

            // Cập nhật số lượng tồn kho
            foreach ($order->orderItems as $orderItem) {
                $productVariant = $orderItem->productVariant;
                $productVariant->stock_quantity -= $orderItem->quantity;
                $productVariant->save();
            }

            // Cập nhật trạng thái đơn hàng
            $order->status = OrderStatus::PROCESSING->value;
            $order->save();

            // Tạo transaction record
            $transaction = new Transaction([
                'order_id' => $order->id,
                'transaction_type' => 'payment',
                'payment_method' => PaymentMethod::VNPAY->value,
                'gateway_transaction_id' => $vnp_TransactionNo,
                'amount' => $order->total_amount,
                'currency' => 'VND',
                'status' => 'completed',
                'payment_details' => json_encode([
                    'bank_code' => $vnp_BankCode,
                    'payment_date' => $vnp_PayDate,
                    'vnp_txn_ref' => $vnp_TxnRef
                ])
            ]);
            $transaction->save();

            DB::commit();
            return redirect(config('services.vnpay.frontend_success_url') . '?order_id=' . $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('VNPay callback error: ' . $e->getMessage());
            
            if (isset($order)) {
                try {
                    DB::beginTransaction();
                    // Xóa order items trước
                    $order->orderItems()->delete();
                    // Xóa order
                    $order->delete();
                    DB::commit();
                } catch (\Exception $deleteError) {
                    DB::rollBack();
                    Log::error('Failed to delete order: ' . $deleteError->getMessage());
                }
            }
            
            return redirect(config('services.vnpay.frontend_failed_url') . '?error=payment_failed');
        }
    }
}