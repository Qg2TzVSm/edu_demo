<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

class AdminBackendController extends Controller
{
    public function audit($id)
    {
        return response()->json(['status'=>1,'message'=>'操作成功！']);
    }
}