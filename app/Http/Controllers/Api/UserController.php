<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController
{
    public function get(Request $request)
    {
        return $request->user();
    }

    public function refresh(Request $request)
    {
        $token = Str::random(60);

        $request->user()->forceFill([
            'api_token' => $token,
        ])->save();

        return ['token' => $token];
    }
}
