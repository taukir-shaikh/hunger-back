<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LearningController extends Controller
{
    public function test(){
        return response()->json([
            "msg"=>"wokrs",
            "yo"=>"sdk  "
        ]);
    }
    public function postTest(Request $request){
        return response()->json($request->all());
    }
}
