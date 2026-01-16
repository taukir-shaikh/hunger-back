<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function orderStore(Request $request, OrderService $orderService)
    {
        try {
            $data = $request->all();
            $order = $orderService->createOrder($data, auth()->user()->toArray());
            return response()->json(['order' => $order, 'message' => 'Order created successfully'], 201);
        } catch (\Exception $e) {
            Log::error('Order creation failed', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all(),
            ]);
            return response()->json(['message' => 'Order creation failed', 'error' => $e->getMessage()], 500);
        }
    }
}
