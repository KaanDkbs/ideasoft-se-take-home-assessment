<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Products;
use App\Models\Customers;
use App\Models\Orders;
use App\Models\OrderItems;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $orders = Orders::with([
            'items' => function ($query) {
                $query->select('order_id', 'product_id as productId', 'quantity', 'unit_price as unitPrice', 'total');
            }
        ])->select('id', 'customer_id as customerId', 'total')->get();

        return response()->json($orders, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $requestData = $request->only(['customerId', 'items']);

        $validator = Validator::make($requestData, [
            'customerId' => 'required|integer|min:1',
            'items' => 'required|array|min:1',
            'items.*.productId' => 'required|integer|min:1',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'invalid', 'message' => $validator->errors()->all()], 400);
        }

        $customer = Customers::find($requestData['customerId']);
        if (!$customer) {
            return response()->json(['status' => 'invalid', 'message' => 'Customer not found. customerId: ' . $requestData['customerId']], 400);
        }

        $totalPrice = 0;
        $orderItems = [];
        foreach ($requestData['items'] as $item) {
            $product = Products::find($item['productId']);
            if (!$product) {
                return response()->json(['status' => 'invalid', 'message' => 'Product not found. productId: ' . $item['productId']], 400);
            }
            if ($item['quantity'] > $product->stock) {
                return response()->json(['status' => 'invalid', 'message' => 'Insufficient stock. productId: ' . $item['productId']], 400);
            }
            $totalPrice += $product->price * $item['quantity'];
            $orderItems[] = [
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'unit_price' => $product->price,
                'total' => $product->price * $item['quantity'],
            ];
        }

        $orderNumber = 'ORD-' . date('YmdHis') . rand(1000, 9999);

        $create = Orders::create([
            'user_id' => Auth::user()->id,
            'customer_id' => $requestData['customerId'],
            'order_number' => $orderNumber,
            'total' => $totalPrice,
        ]);

        if ($create->id) {
            foreach ($orderItems as &$item) {
                $item['order_id'] = $create->id;
                OrderItems::create($item);
            }
        } else {
            return response()->json(['status' => 'invalid', 'message' => 'Order creation failed'], 400);
        }

        return response()->json(['status' => 'success', 'message' => 'Order created successfully', 'data' => ['orderNumber' => $orderNumber, 'orderId' => $create->id]], 200);

    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Orders::with([
            'items' => function ($query) {
                $query->select('order_id', 'product_id as productId', 'quantity', 'unit_price as unitPrice', 'total');
            }
        ])->select('id', 'customer_id as customerId', 'total')->find($id);

        if (!$order) {
            return response()->json(['status' => 'invalid', 'message' => 'Order not found. orderId: ' . $id], 404);
        }

        return response()->json($order, 200);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Orders::find($id);

        if (!$order) {
            return response()->json(['status' => 'invalid', 'message' => 'Order not found. orderId: ' . $id], 404);
        }

        $order->delete();

        return response()->json(['status' => 'success', 'message' => 'Order deleted successfully'], 200);
    }
}
