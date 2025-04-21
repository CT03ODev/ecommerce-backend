<?php

namespace App\Http\Controllers\API;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
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
            'total_amount' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'shipping_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $order = new Order();
        $order->order_number = 'ORD-' . Str::random(10);
        $order->user_id = $request->user_id;
        $order->address_id = $request->address_id;
        $order->total_amount = $request->total_amount;
        $order->tax_amount = $request->tax_amount ?? 0;
        $order->shipping_amount = $request->shipping_amount ?? 0;
        $order->discount_amount = $request->discount_amount ?? 0;
        $order->status = OrderStatus::PENDING->value;
        $order->notes = $request->notes;
        $order->created_by = $request->user()->id ?? null;
        $order->save();

        return response()->json($order, 201);
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