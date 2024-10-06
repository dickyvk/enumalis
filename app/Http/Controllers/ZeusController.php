<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Profile;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class ZeusController extends Controller
{
    /**
     * Set or update the user's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Profile|null  $profile
     * @return \Illuminate\Http\JsonResponse
     */
    public function setProfile(Request $request, Profile $profile = null)
    {
        $user = Auth::user(); // Get the authenticated user

        // Define validation rules for profile data
        $validationRules = [
            'name' => 'required|string|max:255',
            'place_of_birth' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date_format:Y-m-d',
            'gender' => 'nullable|integer|between:1,2',
            'blood_type' => 'nullable|integer|between:1,4',
            'identity_type' => 'nullable|integer|between:1,2',
            'identity_number' => 'nullable|string|max:16|unique:zeus.profiles' . ($profile ? ',identity_number,' . $profile->id : ''),
        ];

        // Validate the incoming request data
        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        // If profile is not provided, create a new one
        if (is_null($profile)) {
            $profileData = array_merge($request->all(), ['users_id' => $user->id]);
            $profile = Profile::create($profileData);
            return response()->json(['message' => 'Profile created successfully.', 'data' => $profile], 201);
        }

        // Update the existing profile
        $profile->update($request->all());
        return response()->json(['message' => 'Profile updated successfully.', 'data' => $profile], 200);
    }

    /**
     * Retrieve the authenticated user's profiles.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfile(): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user(); // Get the authenticated user
        $profiles = Profile::where('users_id', $user->id)->get();

        return response()->json(['message' => 'Profiles retrieved successfully.', 'data' => $profiles], 200);
    }

    /**
     * Delete a specific profile.
     *
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteProfile(Profile $profile): \Illuminate\Http\JsonResponse
    {
        $profile->delete();
        return response()->json(['message' => 'Profile deleted successfully.'], 200);
    }

    /**
     * Send a notification to a specific profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendNotification(Request $request): \Illuminate\Http\JsonResponse
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'profiles_id' => 'required|integer|exists:zeus.profiles,id',
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        // Create the notification
        $notification = Notification::create($request->all());
        return response()->json(['message' => 'Notification sent successfully.', 'data' => $notification], 201);
    }

    /**
     * Blast a notification to all profiles.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function blastNotification(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'user_ids' => 'sometimes|array', // Validate user_ids as an optional array
        ]);

        // Retrieve the users to blast notifications to
        if ($request->has('user_ids')) {
            // If user_ids are provided, only notify those users
            $users = User::whereIn('id', $request->user_ids)->get();
        } else {
            // If no user_ids are provided, notify all users (or use your existing logic)
            $users = User::all();
        }

        foreach ($users as $user) {
            foreach ($user->profiles as $profile) {
                Notification::create([
                    'profiles_id' => $profile->id,
                    'title' => $request->input('title'),
                    'body' => $request->input('body'),
                    'opened' => 0,
                ]);
            }
        }

        return response()->json(['message' => 'Blast notification sent'], 201);
    }

    /**
     * Retrieve notifications for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNotification(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user(); // Get the authenticated user
        $profileIds = Profile::where('users_id', $user->id)->pluck('id');
        $notifications = Notification::whereIn('profiles_id', $profileIds)
            ->orderBy('created_at', 'DESC')
            ->get();

        return response()->json(['message' => 'Notifications retrieved successfully.', 'data' => $notifications], 200);
    }

    /**
     * Show a specific notification.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function showNotification(Notification $notification): \Illuminate\Http\JsonResponse
    {
        return response()->json(['message' => 'Notification retrieved successfully.', 'data' => $notification], 200);
    }

    /**
     * Mark a notification as read or update its status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function readNotification(Request $request, Notification $notification): \Illuminate\Http\JsonResponse
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'opened' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        // Update the notification status
        $notification->update($request->only('opened'));
        return response()->json(['message' => 'Notification updated successfully.', 'data' => $notification], 200);
    }

    /**
     * Delete a specific notification.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteNotification(Notification $notification): \Illuminate\Http\JsonResponse
    {
        $notification->delete();
        return response()->json(['message' => 'Notification deleted successfully.'], 200);
    }
}
