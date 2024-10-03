<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;
use Validator;
use Auth;
use App\Models\User;
use App\Models\Rule;
use Exception;

class EunomiaController extends Controller
{
    public function register(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(),[
            'uid' => 'required|string|max:255|unique:eunomia.users',
            'email' => 'nullable|sometimes|email|max:255|unique:eunomia.users',
            'phone' => 'nullable|sometimes|string|max:255|unique:eunomia.users',
        ]);

        // Return validation errors
        if($validator->fails()){
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        // Database transaction ensures atomicity
        DB::beginTransaction();
        try {
            // Create the user
            $user = User::create($request->only('uid', 'email', 'phone'));

            // Create a rule linked to the user
            $rule = Rule::create(['users_id' => $user->id]);

            // Generate token with optional scopes (adjust scopes as needed)
            $token = $user->createToken('auth_token', ['user:basic'])->plainTextToken;

            // Commit the transaction
            DB::commit();

            // Return success response
            return response()->json(['data' => $user, 'access_token' => $token], 201);

        } catch (Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollBack();

            // Return error response
            return response()->json(['message' => 'Registration failed. Please try again.'], 500);
        }
    }

    public function login(Request $request)
    {
        // Validate that 'uid' is provided
        $validator = Validator::make($request->all(), [
            'uid' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        try {
            // Assuming Supabase has already authenticated the user externally
            $user = User::where('uid', $request->uid)->first();

            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            // Generate token (if needed for your app logic)
            $token = $user->createToken('auth_token', ['user:basic'])->plainTextToken;

            return response()->json(['message' => 'Login successful', 'access_token' => $token], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Login failed. Please try again.'], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user) {
                // Revoke the user's current token
                $request->user()->currentAccessToken()->delete();
                return response()->json(['message' => 'Logged out successfully'], 200);
            }

            return response()->json(['message' => 'User not authenticated'], 401);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to log out. Please try again.'], 500);
        }
    }

    public function getUserDetails(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            return response()->json(['data' => $user], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to retrieve user details.'], 500);
        }
    }

    public function updateSelf(Request $request)
    {
        // Validate input for the authenticated user
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|sometimes|email|max:255|unique:eunomia.users,email,' . Auth::id(),
            'phone' => 'nullable|sometimes|string|max:255|unique:eunomia.users,phone,' . Auth::id(),
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        try {
            // Get the authenticated user
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            // Update the user's own information (only email and phone allowed)
            $user->update($request->only('email', 'phone'));

            return response()->json(['message' => 'User information updated successfully', 'data' => $user], 200);

        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update user information.'], 500);
        }
    }

    public function updateUser(Request $request, $userId)
    {
        // Validate input for the specified user
        $validator = Validator::make($request->all(), [
            'uid' => 'nullable|sometimes|string|max:255|unique:eunomia.users,uid,' . $userId, // Allow masters to change uid
            'email' => 'nullable|sometimes|email|max:255|unique:eunomia.users,email,' . $userId,
            'phone' => 'nullable|sometimes|string|max:255|unique:eunomia.users,phone,' . $userId,
            'type' => 'nullable|sometimes|integer', // Allow masters to change type
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        try {
            // Find the user by their ID
            $user = User::find($userId);

            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            // Master user can update any field
            $user->update($request->all());

            return response()->json(['message' => 'User information updated successfully', 'data' => $user], 200);

        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update user information.'], 500);
        }
    }

    public function deleteUserAccount(Request $request, User $user)
    {
        DB::beginTransaction(); // Start the transaction

        try {
            // Attempt to delete the user
            $user->delete();

            // Optionally, delete related data here (e.g., user posts, comments)
            // Rule::where('users_id', $user->id)->delete(); // Example of deleting related records

            DB::commit(); // Commit the transaction if everything went well

            return response()->json(['message' => 'User account deleted successfully.'], 200);

        } catch (Exception $e) {
            DB::rollBack(); // Roll back the transaction on failure
            return response()->json(['message' => 'Failed to delete user account.'], 500);
        }
    }

    public function getAllUsers(Request $request)
    {
        try {
            // Retrieve all users
            $users = User::all();

            return response()->json(['message' => 'User data retrieved successfully.', 'data' => $users], 200);

        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to retrieve user data.'], 500);
        }
    }

    public function updateRule(Request $request)
    {
        // Validate input for the authenticated user
        $validator = Validator::make($request->all(), [
            'terms' => 'nullable|boolean',
            'policy' => 'nullable|boolean',
            'pagination' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        try {
            // Get the authenticated user
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            //$rule = Rule::where('users_id', $user->id)->first();
            $rule = $user->rule;

            if (!$rule) {
                return response()->json(['message' => 'Rule not found.'], 404);
            }

            $rule->update($request->only('terms', 'policy', 'pagination'));

            return response()->json(['message' => 'Rules updated successfully', 'data' => $rule], 200);

        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update rules.'], 500);
        }
    }
}
