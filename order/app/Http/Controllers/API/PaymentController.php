<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payments = Payment::with('order')->latest()->get();
        
        return response()->json($payments);
    }

    /**
     * Store a newly created payment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'status' => 'required|in:pending,completed,failed,refunded',
            'payment_details' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Check if order exists and is not already paid
        $order = Order::find($request->order_id);
        $existingPayment = Payment::where('order_id', $request->order_id)
            ->where('status', 'completed')
            ->first();
            
        if ($existingPayment) {
            return response()->json(['error' => 'This order has already been paid'], 422);
        }

        // Create payment record
        $payment = new Payment();
        $payment->order_id = $request->order_id;
        $payment->payment_method = $request->payment_method;
        $payment->amount = $request->amount;
        $payment->currency = $request->currency ?? 'USD';
        $payment->status = $request->status;
        $payment->payment_details = $request->payment_details;
        $payment->created_by = $request->user()->id ?? null;
        $payment->save();

        // Create transaction record
        $transaction = new Transaction();
        $transaction->payment_id = $payment->id;
        $transaction->transaction_type = 'payment';
        $transaction->gateway_transaction_id = 'TXN-' . strtoupper(Str::random(12)) . '-' . time();
        $transaction->amount = $request->amount;
        $transaction->currency = $request->currency ?? 'USD';
        $transaction->status = $request->status;
        $transaction->gateway_response = $request->payment_details;
        $transaction->created_by = $request->user()->id ?? null;
        $transaction->save();

        // Update payment with transaction ID reference
        $payment->transaction_id = $transaction->gateway_transaction_id;
        $payment->save();

        // Update order status if payment is completed
        if ($request->status === 'completed' && $order) {
            $order->status = 'processing';
            $order->save();
        }

        return response()->json($payment, 201);
    }

    /**
     * Get payments for a specific order.
     *
     * @param  int  $orderId
     * @return \Illuminate\Http\Response
     */
    public function getOrderPayments($orderId)
    {
        $order = Order::find($orderId);
        
        if (!$order) {
            abort(404);
        }

        $payments = Payment::where('order_id', $orderId)->get();

        return response()->json($payments);
    }
}