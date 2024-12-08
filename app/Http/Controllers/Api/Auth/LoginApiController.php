<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginApiController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'password' => 'required',
        ]);

        $password = $request->password;

        $user = User::where('name', $request->name)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response([
                'message' => ['Kredensial yang kamu masukkan salah.'],
            ], 404);
        }

        return response()->apiSuccess(
            'Success',
            [
                'token' => $user->createToken('api_token')->plainTextToken,
                'user' => $user
            ]
        );
    }
}
