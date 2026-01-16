<?php

namespace App\Services;

use App\Models\TbOrders;
use App\Models\TbRestaurants;
use App\Repositories\OrderRepository;
use DB;

class OrderService
{
    protected $orderRepository;

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

    public function cancelOrder($orderId)
    {
        // Logic to cancel an order
        return $this->orderRepository->cancelOrder($orderId);
    }

    public function assignDeliveryPartner($orderId, $deliveryPartnerId)
    {
        // Logic to assign a delivery partner
        return $this->orderRepository->assignDeliveryPartner($orderId, $deliveryPartnerId);
    }
}