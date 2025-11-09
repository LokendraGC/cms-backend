<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\Api\ResponseService;

class UserController extends Controller
{

    protected $response_service;

    public function __construct(ResponseService $response_service)
    {
        $this->response_service = $response_service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $users = User::with('roles')->get();

            return $this->response_service->successMessage(
                [
                    'users' => $users,
                ],
                message: 'Users fetched successfully',
                code: 200
            );
        } catch (\Exception $e) {
            return $this->response_service->errorMessage(
                message: 'Failed to fetch users: ' . $e->getMessage(),
                code: 500
            );
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {

        try {
            $validated = $request->validated();

            if ($request->hasFile('avatar')) {
                $path = $request->file('avatar')->store('avatars', 'public');
                $validated['avatar'] = $path;
            }

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'avatar' => $validated['avatar'] ?? null,
            ]);

            if (!empty($validated['role'])) {
                $user->assignRole($validated['role']);
            }

            return $this->response_service->successMessage(
                data: [
                    'user' => $user->load('roles'),
                ],
                message: 'User created successfully.',
                code: 201
            );
        } catch (\Exception $e) {
            return $this->response_service->errorMessage(
                message: 'Failed to create user: ' . $e->getMessage(),
                code: 500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = User::with('roles')->findOrFail($id);

            return $this->response_service->successMessage(
                data: ['user' => $user],
                message: 'User details fetched successfully.',
                code: 200
            );
        } catch (\Exception $e) {
            return $this->response_service->errorMessage(
                message: 'Failed to fetch user: ' . $e->getMessage(),
                code: 404
            );
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $user = User::with('roles')->findOrFail($id);

            return $this->response_service->successMessage(
                data: ['user' => $user],
                message: 'User details fetched successfully.',
                code: 200
            );
        } catch (\Exception $e) {
            return $this->response_service->errorMessage(
                message: 'Failed to fetch user: ' . $e->getMessage(),
                code: 404
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, string $id)
    {
        try {
            $user = User::findOrFail($id);
            $validated = $request->validated();

            // Verify old password if new password is provided
            if (!empty($validated['password'])) {
                if (empty($validated['old_password'])) {
                    return $this->response_service->errorMessage(
                        message: 'Old password is required to set a new password',
                        code: 422
                    );
                }

                if (!Hash::check($validated['old_password'], $user->password)) {
                    return $this->response_service->errorMessage(
                        message: 'The provided old password is incorrect',
                        code: 422
                    );
                }

                $user->password = Hash::make($validated['password']);
            }

            // Handle avatar
            if ($request->hasFile('avatar')) {
                if ($user->avatar && \Storage::exists($user->avatar)) {
                    \Storage::delete($user->avatar);
                }

                $path = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = $path;
            }

            $user->name = $validated['name'] ?? $user->name;
            $user->email = $validated['email'] ?? $user->email;
            $user->save();

            if (!empty($validated['role'])) {
                $user->syncRoles([$validated['role']]);
            }

            return $this->response_service->successMessage(
                data: ['user' => $user->fresh()->load('roles')],
                message: 'User Updated successfully',
                code: 200
            );
        } catch (\Exception $e) {
            return $this->response_service->errorMessage(
                message: 'Failed to update user: ' . $e->getMessage(),
                code: 404
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user) {
                if ($user->avatar && \Storage::exists($user->avatar)) {
                    \Storage::delete($user->avatar);
                }

                $user->delete();
            }

            return $this->response_service->successMessage(
                message: 'User deleted successfully',
                code: 200
            );
        } catch (\Exception $e) {
            return $this->response_service->errorMessage(
                message: 'Unable to delete user',
                code: 500
            );
        }
    }
}
