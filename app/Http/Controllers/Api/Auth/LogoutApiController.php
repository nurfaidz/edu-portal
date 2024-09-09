<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutApiController extends Controller
{
    public function __invoke()
    {
        auth()->user()->tokens()->delete();

        return response()->apiSuccess([]);
    }
}
