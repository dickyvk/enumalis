<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Validator;
use App\Models\Profile;
use App\Models\Notification;

class ZeusController extends Controller
{
    public function setProfile(Request $request, Profile $profile = NULL)
    {
        if(!$profile)
        {
            $validator = Validator::make($request->all(),[
                'name' => 'required|string|max:255',
                'place_of_birth' => 'nullable|string|max:255',
                'date_of_birth' => 'required|date_format:Y-m-d',
                'gender' => 'nullable|integer|between:1,2',
                'blood_type' => 'nullable|integer|between:1,4',
                'identity_type' => 'nullable|integer|between:1,2',
                'identity_number' => 'nullable|sometimes|string|max:16|unique:zeus.profiles',
            ]);

            if($validator->fails()){
                return response()->json(['message' => $validator->errors()->first()], 400);
            }

            $token = PersonalAccessToken::findToken($request->bearerToken());
            $user = $token->tokenable;
            $profile = Profile::create(array_merge($request->all(), ['users_id' => $user->id]));

            return response()->json($profile, 201);
        }
        else
        {
            $validator = Validator::make($request->all(),[
                'name' => 'required|string|max:255',
                'place_of_birth' => 'nullable|string|max:255',
                'date_of_birth' => 'required|date_format:Y-m-d',
                'gender' => 'nullable|integer|between:1,2',
                'blood_type' => 'nullable|integer|between:1,4',
                'identity_type' => 'nullable|integer|between:1,2',
                'identity_number' => 'nullable|sometimes|string|max:16|unique:zeus.profiles',
            ]);

            if($validator->fails()){
                return response()->json(['message' => $validator->errors()->first()], 400);
            }
            
            $profile->update($request->all());

            return response()->json($profile, 200);
        }
    }

    public function getProfile(Request $request)
    {
        $token = PersonalAccessToken::findToken($request->bearerToken());
        $user = $token->tokenable;
        $profile = Profile::where('users_id', $user->id)->get();

        return response()->json($profile, 200);
    }

    public function deleteProfile(Profile $profile)
    {
        $profile->delete();

        return response()->json(null, 204);
    }

    public function sendNotification(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'profiles_id' => 'required|integer',
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $notification = Notification::create($request->all());

        return response()->json($notification, 201);
    }

    public function blastNotification(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $data = [];
        $profiles = Profile::get();
        foreach($profiles as $profile)
        {
            $data[] = [
                'profiles_id' => $profile->id,
                'title' => $request->title,
                'body' => $request->body,
                'opened' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach(array_chunk($data, 1000) as $chunk)
        {
            Notification::insert($chunk);
        }

        return response()->json(null, 201);
    }

    public function getNotification(Request $request)
    {
        $token = PersonalAccessToken::findToken($request->bearerToken());
        $user = $token->tokenable;
        $notification = Notification::where('profiles_id', $user->id)->orderBy('created_at', 'DESC')->get();

        return response()
            ->json($notification);
    }

    public function showNotification(Notification $notification)
    {
        return $notification;
    }

    public function updateNotification(Request $request, Notification $notification)
    {
        $notification->update($request->all());

        return response()->json($notification, 200);
    }

    public function deleteNotification(Notification $notification)
    {
        $notification->delete();

        return response()->json(null, 204);
    }
}
