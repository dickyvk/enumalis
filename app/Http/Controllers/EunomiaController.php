<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Validator;
use Auth;
use App\Models\User;

class EunomiaController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function show(User $user)
    {
        return $user;
    }

    public function store(Request $request)
    {
        $user = User::create($request->all());

        return response()->json($user, 201);
    }

    public function update(Request $request, User $user)
    {
        $user->update($request->all());

        return response()->json($user, 200);
    }

    public function delete(User $user)
    {
        $user->delete();

        return response()->json(null, 204);
    }

	public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'uid' => 'required|string|max:255|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'nullable|sometimes|email|max:255|unique:users',
            'phone' => 'nullable|sometimes|email|max:255|unique:users',
        ]);

        if($validator->fails()){
			return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $user = User::create($request->all());

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer', ], 201);

    }

    public function login(Request $request)
    {
    	if(!Auth::attempt($request->only('uid')))
        {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::where('uid', $request['uid'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer', ], 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json(null, 204);
    }
}
