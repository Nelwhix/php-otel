<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\StoreOrderRequest;
use App\Jobs\ShipOrder;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request): JsonResponse {
        $validated = $request->validated();
        $productIds = array_column($validated['items'], 'product_id');
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $amount = $this->calculateOrderAmount($validated, $products);
        $order = null;
        DB::transaction(function () use ($validated, $amount, $products, &$order) {
           $order = Order::create([
               'status' => OrderStatus::Processing,
               'amount' => $amount,
           ]);

           foreach ($validated['items'] as $item) {
               $product = $products[$item['product_id']];

               $order->items()->create([
                  'product_id' => $item['product_id'],
                  'quantity' => $item['quantity'],
                   'unit_price' => $product->price,
                   'total_price' => $product->price * $item['quantity'],
               ]);
           }
        });
        ShipOrder::dispatch($order);

        return response()->json([
            'message' => 'Order created.',
        ], Response::HTTP_CREATED);
    }

    private function calculateOrderAmount(array $validated, Collection $products): int {
        $total = 0;

        foreach ($validated['items'] as $item) {
            $product = $products[$item['product_id']];
            $total += $product->price * $item['quantity'];
        }

        return $total;
    }
}
