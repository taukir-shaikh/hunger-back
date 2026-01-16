<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;

class RestaurantOrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $service = new OrderService();
        return $service->getOrdersForRestaurant($user->restaurant_id, $request->all());
    }
        public function acceptOrder($id, OrderService $service)
    {
        return $service->restaurantUpdateStatus($id, 'ACCEPTED', auth()->user());
    }

    public function rejectOrder($id, OrderService $service)
    {
        return $service->restaurantUpdateStatus($id, 'RESTAURANT_REJECTED', auth()->user());
    }

    public function preparingOrder($id, OrderService $service)
    {
        return $service->restaurantUpdateStatus($id, 'PREPARING', auth()->user());
    }

    public function readyOrder($id, OrderService $service)
    {
        return $service->restaurantUpdateStatus($id, 'READY_FOR_PICKUP', auth()->user());
    }
}
