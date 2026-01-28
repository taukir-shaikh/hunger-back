<?php

namespace App\Http\Controllers;

use App\Models\TbRestaurants;
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
        $data = DB::table('tb_users as tu')
            ->leftJoin('tb_user_levels as tl', 'tl.user_level_id', '=', 'tu.user_level_id')
            ->join('tb_orders as or', 'or.user_id', '=', 'tu.id')
            ->select('tu.*', 'tl.*', 'or.*')
            ->get();
        $restaurants = TbRestaurants::all();
        return response()->json([
            'user' => $data,
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
