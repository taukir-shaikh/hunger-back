<?php

namespace App\Http\Controllers;

use App\Models\TbRestaurants;
use App\Services\RestaurantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicRestaurantController extends Controller
{
   public function index(RestaurantService $service)
   {
      $data = $service->getAllRestaurants();
      return response()->json([
         'msg' => 'Restautant fetched successfully',
         'restaurants' => $data
      ]);
   }

    public function show($id)
    {
        // $data = TbRestaurants::find($id);
        $data = DB::table('tb_restaurants')->where('id',$id)->first();
         return response()->json([
            'msg'=>'Restautant details fetched successfully',
            'restaurant'=> $data
       ]);
    }
}
