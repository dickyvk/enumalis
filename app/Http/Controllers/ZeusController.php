<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Validator;
use App\Models\Profile;

class ZeusController extends Controller
{
    public function addProfile(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'place_of_birth' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date_format:Y-m-d',
            'gender' => 'nullable|integer|between:1,2',
            'blood_type' => 'nullable|integer|between:1,4',
            'identity_type' => 'nullable|integer|between:1,2',
            'identity_number' => 'nullable|sometimes|integer|max_digits:16|unique:profiles',
        ]);

        if($validator->fails()){
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $token = PersonalAccessToken::findToken($request->bearerToken());
        $user = $token->tokenable;
        $profile = Profile::create(array_merge($request->all(), ['users_id' => $user->id]));

        return response()->json($profile, 201);
    }

    public function getProfile(Request $request)
    {
        $token = PersonalAccessToken::findToken($request->bearerToken());
        $user = $token->tokenable;
        $profile = Profile::where('users_id', $user->id)->get();

        return response()->json($profile, 200);
    }
}
