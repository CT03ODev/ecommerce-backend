<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderItemController extends Controller
{
    /**
     * Store a newly created order item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'options' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Check if product variant exists and belongs to the product
        if ($request->has('product_variant_id') && $request->product_variant_id) {
            $variant = ProductVariant::where('id', $request->product_variant_id)
                ->where('product_id', $request->product_id)
                ->first();
                
            if (!$variant) {
                return response()->json(['error' => 'Product variant does not belong to the specified product'], 422);
            }
        }

        // Calculate subtotal
        $subtotal = $request->unit_price * $request->quantity;

        $orderItem = new OrderItem();
        $orderItem->order_id = $request->order_id;
        $orderItem->product_id = $request->product_id;
        $orderItem->product_variant_id = $request->product_variant_id;
        $orderItem->quantity = $request->quantity;
        $orderItem->unit_price = $request->unit_price;
        $orderItem->subtotal = $subtotal;
        $orderItem->options = $request->options;
        $orderItem->created_by = $request->user()->id ?? null;
        $orderItem->save();

        // Update order total
        $this->updateOrderTotal($request->order_id);

        return response()->json($orderItem, 201);
    }

    /**
     * Update the specified order item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $orderItem = OrderItem::find($id);
        
        if (!$orderItem) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'nullable|integer|min:1',
            'options' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->has('quantity')) {
            $orderItem->quantity = $request->quantity;
            $orderItem->subtotal = $orderItem->unit_price * $request->quantity;
        }
        
        if ($request->has('options')) {
            $orderItem->options = $request->options;
        }
        
        $orderItem->updated_by = $request->user()->id ?? null;
        $orderItem->save();

        // Update order total
        $this->updateOrderTotal($orderItem->order_id);

        return response()->json($orderItem);
    }

    /**
     * Remove the specified order item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $orderItem = OrderItem::find($id);
        
        if (!$orderItem) {
            abort(404);
        }

        $orderId = $orderItem->order_id;
        
        $orderItem->deleted_by = $request->user()->id ?? null;
        $orderItem->save();
        $orderItem->delete();

        // Update order total
        $this->updateOrderTotal($orderId);

        return response()->json(null, 204);
    }

    /**
     * Update the order total amount based on order items.
     *
     * @param  int  $orderId
     * @return void
     */
    private function updateOrderTotal($orderId)
    {
        $order = Order::find($orderId);
        
        if ($order) {
            $subtotal = OrderItem::where('order_id', $orderId)->sum('subtotal');
            $order->total_amount = $subtotal + $order->tax_amount + $order->shipping_amount - $order->discount_amount;
            $order->save();
        }
    }
}