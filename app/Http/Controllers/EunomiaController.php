<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Validator;
use Auth;
use App\Models\User;
use App\Models\Rule;

class EunomiaController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'uid' => 'required|string|max:255|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'nullable|sometimes|email|max:255|unique:users',
            'phone' => 'nullable|sometimes|string|max:255|unique:users',
        ]);

        if($validator->fails()){
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $user = User::create($request->only('uid', 'name', 'email', 'phone'));
        $rule = Rule::create(['users_id' => $user->id]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer', ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'uid' => 'required|string|max:255',
        ]);

        if($validator->fails()){
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $user = User::where('uid', $request['uid'])->first();

        if(!$user)
        {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer', ], 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json(null, 204);
    }

    public function show(Request $request)
    {
        $token = PersonalAccessToken::findToken($request->bearerToken());
        $user = $token->tokenable;

        return response()->json($user, 200);
    }

    public function update(Request $request, User $user = NULL)
    {
        if(!$user)
        {
            $validator = Validator::make($request->all(),[
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|sometimes|email|max:255|unique:users',
                'phone' => 'nullable|sometimes|string|max:255|unique:users',
            ]);

            if($validator->fails()){
                return response()->json(['message' => $validator->errors()->first()], 400);
            }

            $token = PersonalAccessToken::findToken($request->bearerToken());
            $user = $token->tokenable;
            $user->update($request->only('name', 'email', 'phone'));
        }
        else
        {
            $validator = Validator::make($request->all(),[
                'uid' => 'nullable|string|max:255|unique:users',
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|sometimes|email|max:255|unique:users',
                'phone' => 'nullable|sometimes|string|max:255|unique:users',
                'type' => 'nullable|integer',
            ]);

            if($validator->fails()){
                return response()->json(['message' => $validator->errors()->first()], 400);
            }
            
            $user->update($request->all());
        }

        return response()->json($user, 200);
    }

    public function rule(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'terms' => 'nullable|boolean',
            'policy' => 'nullable|boolean',
        ]);

        if($validator->fails()){
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $token = PersonalAccessToken::findToken($request->bearerToken());
        $user = $token->tokenable;
        $rule = Rule::where('users_id', $user->id)->first();
        $rule->update($request->only('terms', 'policy'));

        return response()->json($rule, 200);
    }

    public function showAll()
    {
        return User::all();
    }

    public function delete(User $user)
    {
        $user->delete();

        return response()->json(null, 204);
    }
}
