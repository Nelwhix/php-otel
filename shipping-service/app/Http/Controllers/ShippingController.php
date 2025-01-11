<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShippingController extends Controller
{
    public function store(Order $order) {
        if ($order->status !== OrderStatus::Processing) {
            return response()->json([
                'message' => 'Order has not been processed.'
            ], Response::HTTP_BAD_REQUEST);
        }

        $order->update([
            'status' => OrderStatus::Shipped,
        ]);

        return response()->json([
            'message' => 'Order has been shipped.',
        ], Response::HTTP_OK);
    }
}
