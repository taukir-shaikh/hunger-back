<?php

namespace App\Http\Controllers;

use App\Models\TbRestaurants;
use App\Models\TbUsers;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        return response()->json(['message' => 'Welcome to the Dashboard!'], 200);
    }

    public function index()
    {
        $users = TbUsers::with(['userLevel', 'orders'])->get();
        $restaurants = TbRestaurants::all();
        return response()->json([
            'user' => UserResource::collection($users),
            'restaurants' => $restaurants
        ]);
    }

    public function test()
    {
        $data = DB::select("Select * from tb_users where is_active = ?",[!true]);
        return $data;
        // return response()->json(['message' => 'Test method called successfully!'], 200);
    }
}
