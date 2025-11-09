<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user') ?? $this->route('id');

        $rules = [
            'name' => 'required|string|max:255',
            'role' => 'required|string',
            'avatar' => 'nullable|file|image|max:2048',
        ];

        if ($userId) {
            $rules['email'] = "required|email|unique:users,email,{$userId}";

            // Only require old_password when password is provided
            if ($this->has('password') && !empty($this->password)) {
                $rules['old_password'] = 'required';
                $rules['password'] = 'required|min:6';
            } else {
                $rules['password'] = 'nullable|min:6';
            }
        } else {
            $rules['email'] = 'required|email|unique:users,email';
            $rules['password'] = 'required|min:6';
        }

        return $rules;
    }

    // Add custom validation messages (optional but recommended)
    public function messages(): array
    {
        return [
            'old_password.required' => 'The old password is required when changing password.',
            'old_password.required_with' => 'The old password is required when setting a new password.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}
