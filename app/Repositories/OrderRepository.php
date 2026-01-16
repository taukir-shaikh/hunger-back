<?php

namespace App\Repositories;

use App\Models\TbRestaurants;
use App\Models\TbUsers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class OrderRepository
{
    public function createOrder(array $data, $userId)
    {
        $orderId = DB::table('tb_orders')->insertGetId([
            'restaurant_id' => $data['restaurant_id'],
            'user_id' => $userId['id'],
            // 'order_number' => $data['order_number'],
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'total_amount' => $data['total_amount'],
            'delivery_address' => $data['delivery_address'],
            'status' => $data['status'],
            'payment_status' => $data['payment_status'],
            'payment_method' => $data['payment_method'],
            'ordered_at' => $data['ordered_at'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        return $orderId;

    }

    public function createOrderItems(int $orderId, array $items)
    {
        foreach ($items as $item) {
            DB::table('tb_order_items')->insert([
                'order_id' => $orderId,
                'food_id' => $item['item_id'],
                'food_name_snapshot' => $item['name'],
                'price_snapshot' => $item['price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['price'] * $item['quantity'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }

    public function logOrderStatus(int $orderId, ?string $old_status, string $new_status, string $changedBy)
    {
        DB::table('tb_order_status_history')->insert([
            'order_id' => $orderId,
            'old_status' => $old_status ?? 'created',
            'new_status' => $new_status,
            'changed_by' => $changedBy,
            'changed_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    public function cancelOrder(int $orderId, string $status = 'USER_CANCELLED')
    {
        $res = DB::table('tb_orders')->where('order_id', $orderId)->update(['status' => $status]);
        return $res;
    }

    public function getOrderTimeline(int $orderId)
    {
        return DB::table('tb_order_status_history')
            ->where('order_id', $orderId)
            ->orderBy('created_at', 'asc')
            ->get([
                'old_status',
                'new_status',
                'changed_by',
                'created_at'
            ]);
    }
}