<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\Discounts;

class DiscountsController
{

    public function index($orderId)
    {
        $order = Orders::with('items')->with('items.product')->find($orderId);

        if (!$order) {
            return response()->json(['status' => 'invalid', 'message' => 'Order not found. orderId: ' . $orderId], 404);
        }

        $categoryCount = [];

        foreach ($order->items as $item) {
            if (!isset($categoryCount[$item->product->category])) {
                $categoryCount[$item->product->category]['quantity'] = $item->quantity;
                $categoryCount[$item->product->category]['price'] = $item->unit_price;
            } else {
                $categoryCount[$item->product->category]['quantity'] += $item->quantity;
                if ($categoryCount[$item->product->category]['price'] > $item->unit_price) {
                    $categoryCount[$item->product->category]['price'] = $item->unit_price;
                }
            }
        }

        $discounts = Discounts::All();

        $data = array(
            'orderId' => $order->id,
            'discounts' => [],
            'totalDiscount' => 0.00,
            'discountedTotal' => $order->total
        );

        foreach ($discounts as $discount) {
            if ($discount->condition_type == 'total_order_amount' && $order->total >= $discount->condition_value) {
                if ($discount->discount_type == 'percentage') {
                    $discountPrice = number_format($order->total * $discount->discount_value / 100, 2, '.', '');
                    $subTotal = number_format($order->total - $discountPrice, 2, '.', '');
                    $data['discounts'][] = [
                        'discountReason' => $discount->name,
                        'discountAmount' => $discountPrice,
                        'subtotal' => $subTotal
                    ];
                    $data['totalDiscount'] += $discountPrice;
                    $data['discountedTotal'] -= $discountPrice;
                }
            }
            if ($discount->condition_type == 'category_id') {

                if ($discount->discount_type == 'product_amount') {
                    foreach ($order->items as $item) {
                        if ($item->product->category == $discount->category_id && $item->quantity >= $discount->condition_value) {
                            $discountPrice = number_format($discount->free_product_count * $item->unit_price, 2, '.', '');
                            $subTotal = number_format($order->total - $discountPrice, 2, '.', '');
                            $data['discounts'][] = [
                                'discountReason' => $discount->name,
                                'discountAmount' => $discountPrice,
                                'subtotal' => $subTotal
                            ];
                            $data['totalDiscount'] += $discountPrice;
                            $data['discountedTotal'] -= $discountPrice;
                        }
                    }
                }

                if ($discount->discount_type == 'cheapest_product_percentage') {
                    $cheapestProductPrice = 0;
                    if ($categoryCount[$discount->category_id]['quantity'] >= $discount->condition_value) {
                        if ($categoryCount[$discount->category_id]['price'] > $cheapestProductPrice) {
                            $cheapestProductPrice = $categoryCount[$discount->category_id]['price'];
                            $discountPrice = number_format($cheapestProductPrice * $discount->discount_value / 100, 2, '.', '');
                            $subTotal = number_format($order->total - $discountPrice, 2, '.', '');
                            $data['discounts'][] = [
                                'discountReason' => $discount->name,
                                'discountAmount' => $discountPrice,
                                'subtotal' => $subTotal
                            ];
                            $data['totalDiscount'] += $discountPrice;
                            $data['discountedTotal'] -= $discountPrice;
                        }
                    }
                }

            }
        }
        $data['discountedTotal'] = number_format($data['discountedTotal'], 2, '.', '');
        $data['totalDiscount'] = number_format($data['totalDiscount'], 2, '.', '');
        return response()->json($data, 200);

    }

}
