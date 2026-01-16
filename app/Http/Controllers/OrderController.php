<?php

namespace App\Http\Controllers;

use App\Models\TbOrders;
use App\Repositories\OrderRepository;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    private $user = null;
    public function __construct()
    {
        $this->user = auth()->user()->toArray();
    }
    public function orderStore(Request $request, OrderService $orderService)
    {
        try {
            $data = $request->all();
            $order = $orderService->createOrder($data, $this->user);
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

    public function cancelOrder($id, OrderService $orderService)
    {
        try {
            $result = $orderService->cancelOrder((int) $id, $this->user);
            if ($result) {
                return response()->json(['message' => 'Order cancelled successfully'], 200);
            } else {
                return response()->json(['message' => 'Order cancellation failed'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Order cancellation failed', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'order_id' => $id,
            ]);
            return response()->json(['message' => 'Order cancellation failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function assignDelivery($id, Request $request, OrderService $orderService)
    {
        try {
            $request->validate([
                'delivery_partner_id' => 'required|integer'
            ]);
            $data = $request->all();
            $result = $orderService->assignDeliveryPartner((int) $id, $data['delivery_partner_id'], $this->user);
            if ($result) {
                return response()->json(['message' => 'Delivery person assigned successfully'], 200);
            } else {
                return response()->json(['message' => 'Assigning delivery person failed'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Assigning delivery person failed', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'order_id' => $id,
                'payload' => $request->all(),
            ]);
            return response()->json(['message' => 'Assigning delivery person failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function pickup($id, OrderService $service, OrderRepository $repository)
    {
        $repository->logOrderStatus(
            $id,
            'READY_FOR_PICKUP',
            'OUT_FOR_DELIVERY',
            'DELIVERY'
        );
        return response()->json(
            $service->updateStatus(
                $id,
                'OUT_FOR_DELIVERY',
                auth()->user()
            )
        );
    }

    public function deliver($id, OrderService $service, OrderRepository $repository)
    {
        $order = TbOrders::findOrFail($id);

        if ($order->delivery_partner_id !== auth()->user()->id) {
            throw new \Exception('Not your order');
        }

        if ($order->status !== 'OUT_FOR_DELIVERY') {
            throw new \Exception('Invalid state');
        }
        $repository->logOrderStatus(
            $id,
            'OUT_FOR_DELIVERY',
            'DELIVERED',
            'DELIVERY'
        );
        return response()->json(
            $service->updateStatus(
                $id,
                'DELIVERED',
                auth()->user()
            )
        );
    }

    public function getOrderTimeline($orderId, $request, OrderService $orderService)
    {
        $timeline = $orderService->getOrderTimeline(
            $orderId,
            $request->user()
        );

        return response()->json([
            'order_id' => $orderId,
            'timeline' => $timeline
        ]);
    }

}
