<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    public function test()
    {
        var_dump(config('auth.guards.api.provider'));
        var_dump(request()->user()->email);
    }
}