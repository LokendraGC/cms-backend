<?php

namespace App\Http\Controllers\Api;

use App\Services\Api\ResponseService;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /*
        Name: login
        Description: login api
    */
    protected $response_service;
    public function __construct(ResponseService $response_service)
    {
        $this->response_service = $response_service;
    }

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

            $token = $user->createToken('myToken')->plainTextToken;

            return $this->response_service->successMessage(
                data: ['user' => $user],
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


    public function profile() // Inject the service
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->response_service->errorMessage(
                    message: 'Unauthorized.',
                    code: 401
                );
            }

            return $this->response_service->successMessage(
                data: [
                    'user' => $user,
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
}
