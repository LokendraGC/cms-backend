<?php

namespace App\Http\Controllers\Api;

use App\Services\Api\ResponseService;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    protected $response_service;
    public function __construct(ResponseService $response_service)
    {
        $this->response_service = $response_service;
    }

    /*
        Name: me
        Description: me route api
    */

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'message' => 'User fetched successfully',
            'user' => $user,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ]);
    }

    /*
        Name: login
        Description: login api
    */
    public function login(AuthRequest $request)
    {
        try {
            $validated = $request->validated();

            $user = User::where('email', $validated['email'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return $this->response_service->errorMessage(
                    message: 'Invalid credentias.',
                    code: 401
                );
            }

            $rememberMe = $request->boolean('remember_me', false);
            $token = $user->createToken('myToken')->plainTextToken;
            $expiresAt = $rememberMe ? now()->addDays(30) : now()->addHours(2);


            return $this->response_service->successMessage(
                data: [
                    'user' => $user,
                    'expires_at' => $expiresAt->toDateTimeString()
                ],
                message: 'Login successful.',
                code: 200,
                token: $token
            );
        } catch (\Exception $e) {
            return $this->response_service->errorMessage(
                message: 'Failed to login: ' . $e->getMessage(),
                code: 500
            );
        }
    }

    /*
        Name: logout
        Description: logout api
    */
    public function logout()
    {

        try {
            $user = auth('sanctum')->user();

            if (!$user) {
                return $this->response_service->errorMessage(
                    message: 'Unauthorized.',
                    code: 401
                );
            }

            // Revoke the user's token
            $user->currentAccessToken()->delete();

            return $this->response_service->successMessage(
                message: 'User logged out successfully.',
                code: 200
            );
        } catch (\Exception $e) {
            return $this->response_service->errorMessage(
                message: 'Failed logout: ' . $e->getMessage(),
                code: 500
            );
        }
    }
    /*
        Name: profile
        Description: profile api
    */
    public function profile() // Inject the service
    {
        try {
            $user = Auth::user();

            $roles = $user->getRoleNames();

            if (!$user) {
                return $this->response_service->errorMessage(
                    message: 'Unauthorized.',
                    code: 401
                );
            }

            return $this->response_service->successMessage(
                data: [
                    'user' => $user,
                    'role' => $roles
                ],
                message: 'User profile retrieved successfully.',
                code: 200
            );
        } catch (\Exception $e) {
            return $this->response_service->errorMessage(
                message: 'Failed get user: ' . $e->getMessage(),
                code: 500
            );
        }
    }

    /*
        Name:update profile
        Description: profile api
    */

    public function updateProfile(UpdateProfileRequest $request)
    {
        try {

            $user = auth('sanctum')->user();

            if (!$user) {
                return $this->response_service->errorMessage(
                    message: 'Unauthorize',
                    code: 401
                );
            }
            $validated = $request->validated();
            $user->name = $validated['name'] ?? $user->name;
            $user->email = $validated['email'] ?? $user->email;

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            if ($request->hasFile('avatar')) {
                if ($user->avatar && \Storage::exists($user->avatar)) {
                    \Storage::delete($user->avatar);
                }

                if (!empty($validated['password'])) {
                    if (empty($validated['old_password'])) {
                        return $this->response_service->errorMessage(
                            message: 'Old password is required to set a new password.',
                            code: 422
                        );
                    }

                    if (!Hash::check($validated['old_password'], $user->password)) {
                        return $this->response_service->errorMessage(
                            message: 'Old password is incorrect.',
                            code: 422
                        );
                    }

                    $user->password = Hash::make($validated['password']);
                }

                // Store new avatar
                $path = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = $path;
            }


            $user->save();

            return $this->response_service->successMessage(
                data: [
                    'user' => $user->fresh(),
                ],
                message: 'Profile updated successfully.',
                code: 200
            );
        } catch (\Exception $e) {
            return $this->response_service->errorMessage(
                message: 'Failed to update profile: ' . $e->getMessage(),
                code: 500
            );
        }
    }
}
