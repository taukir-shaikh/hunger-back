<?php

namespace App\Services;

use App\Models\TbOrders;
use App\Models\TbRestaurants;
use App\Repositories\OrderRepository;
use DB;

class OrderService
{
    protected $orderRepository;

    private array $allowedTransitions = [
        'CREATED' => ['ACCEPTED', 'USER_CANCELLED'],
        'ACCEPTED' => ['PREPARING', 'RESTAURANT_REJECTED'],
        'PREPARING' => ['READY_FOR_PICKUP'],
        'READY_FOR_PICKUP' => ['OUT_FOR_DELIVERY'],
        'OUT_FOR_DELIVERY' => ['DELIVERED', 'DELIVERY_FAILED'],
    ];

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
    }

    public function createOrder(array $data, $user)
    {
        // Logic to create an order
        $restaurant = TbRestaurants::where('id', $data['restaurant_id'])
            ->where('is_approved', 1)
            ->where('is_active', 1)
            ->first();
        if (!$restaurant) {
            return null;
        }
        $orderId = $this->orderRepository->createOrder($data, $user);
        $this->orderRepository->createOrderItems((int) $orderId, $data['items']);
        if (!$orderId) {
            return null;
        } else {
            $this->updateStatus((int) $orderId, null, 'PLACED', $user);
        }
        return $orderId;
    }

    public function acceptOrder($orderId)
    {
        // Logic to accept an order
        return $this->orderRepository->acceptOrder($orderId);
    }

    public function rejectOrder($orderId)
    {
        // Logic to reject an order
        return $this->orderRepository->rejectOrder($orderId);
    }

    public function updateStatus(int $orderId, ?string $oldStatus, string $newStatus, $user)
    {
        $order = TbOrders::find($orderId);
        // Fetch user_level_code
        $userLevel = DB::table('tb_user_levels')->where('user_level_id', $user['user_level_id'])->first();
        $user['user_level_code'] = $userLevel ? $userLevel->level_code : null;
        // Fetch restaurant_id if user is a restaurant
        if ($user['user_level_code'] === 'RESTAURANT') {
            $restaurant = DB::table('tb_restaurants')->where('created_by', $user['id'])->first();
            $user['restaurant_id'] = $restaurant ? $restaurant->id : null;
        }
        // Access control for different user roles
        if ($user['user_level_code'] === 'RESTAURANT' && $order->restaurant_id !== $user['restaurant_id']) {
            throw new \Exception('You are not allowed to update this order');
        }
        if ($user['user_level_code'] === 'USER' && $order->user_id !== $user['id']) {
            throw new \Exception('You are not allowed to update this order');
        }
        if ($user['user_level_code'] === 'DELIVERY' && isset($order->delivery_person_id) && $order->delivery_person_id !== $user['id']) {
            throw new \Exception('You are not allowed to update this order');
        }
        // log history
        $order = $this->orderRepository->logOrderStatus(
            $orderId,
            $oldStatus,
            $newStatus,
            $user['name']
        );

        return $order;
    }


    public function cancelOrder($orderId, $user)
    {
        if (!$orderId) {
            return false;
        }
        $order = TbOrders::find($orderId);
        if (in_array($order->status, ['CREATED', 'PLACED', 'ACCEPTED', 'PREPARING'])) {
            $this->updateStatus((int) $orderId, $order->status, 'USER_CANCELLED', $user);
            return $this->orderRepository->cancelOrder($orderId, status: 'USER_CANCELLED');
        }
        throw new \Exception("Order cannot be cancelled at this stage");
    }

    public function assignDeliveryPartner($orderId, $deliveryPartnerId, $user)
    {
        // Logic to assign a delivery partner
        if ($user->user_level_id !== 3) { //3 is ADMIN Role
            throw new \Exception('Only admin can assign delivery partner');
        }
        $order = TbOrders::find($orderId);
        if (in_array($order->status, ['OUT_FOR_DELIVERY', 'DELIVERED'])) {
            throw new \Exception('Delivery partner cannot be reassigned after pickup');
        }
        if ($order->status === 'READY_FOR_PICKUP') {
            DB::transaction(function () use ($order, $deliveryPartnerId, $user) {

                // save assignment
                DB::table('tb_delivery_assignments')->insert([
                    'order_id' => $order->id,
                    'delivery_partner_id' => $deliveryPartnerId,
                    'assigned_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // update order
                $oldStatus = $order->status;
                $order->update([
                    'delivery_partner_id' => $deliveryPartnerId,
                    'status' => 'OUT_FOR_DELIVERY'
                ]);

                // log history
                $this->orderRepository->logOrderStatus(
                    $order->id,
                    $oldStatus,
                    'OUT_FOR_DELIVERY',
                    'ADMIN.' . $user->name
                );
            });
            $oldStatus = $order->status;
            $this->updateStatus((int) $orderId, $oldStatus, 'OUT_FOR_DELIVERY', $user->toArray());
        } else {
            throw new \Exception('Delivery partner can be assigned only when order is READY_FOR_PICKUP');
        }
        return $this->orderRepository->assignDeliveryPartner($orderId, $deliveryPartnerId);
    }

    public function getOrderTimeline($orderId, $user)
    {
        $order = DB::table('tb_orders')->find($orderId);

        if (!$order) {
            throw new \Exception('Order not found');
        }

        // 🔒 Access rules
        if (
            $user->user_level_code === 'USER' &&
            $order->user_id !== $user->id
        ) {
            throw new \Exception('Unauthorized');
        }

        if (
            $user->user_level_code === 'RESTAURANT' &&
            $order->restaurant_id !== $user->restaurant_id
        ) {
            throw new \Exception('Unauthorized');
        }
        $this->orderRepository = new OrderRepository();
        return $this->orderRepository->getOrderTimeline($orderId);
    }

    public function restaurantUpdateStatus($orderId, $newStatus, $user)
    {
        $order = TbOrders::findOrFail($orderId);

        // Ownership check
        if ($order->restaurant_id !== $user->restaurant_id) {
            throw new \Exception('Unauthorized');
        }

        $allowed = [
            'ACCEPTED' => ['PAID'],
            'RESTAURANT_REJECTED' => ['PAID'],
            'PREPARING' => ['ACCEPTED'],
            'READY_FOR_PICKUP' => ['PREPARING'],
        ];

        if (!in_array($order->status, $allowed[$newStatus])) {
            throw new \Exception('Invalid status change');
        }

        $this->updateStatus($orderId, $order->status, $newStatus, $user->toArray());
        $this->orderRepository->logOrderStatus(
            $orderId,
            $order->status,
            $newStatus,
            'RESTAURANT'
        );

        return true;
    }

    public function getOrdersForRestaurant($restaurantId, $filters = [])
    {
        $query = DB::table('tb_orders')->where('restaurant_id', $restaurantId);

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['date_from'])) {
            $query->whereDate('ordered_at', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->whereDate('ordered_at', '<=', $filters['date_to']);
        }

        return $query->get();
    }

}