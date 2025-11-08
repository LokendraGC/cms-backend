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

        // Email rules
        if ($userId) {
            // Update - email is required but can be the same as current
            $rules['email'] = "required|email|unique:users,email,{$userId}";
            $rules['password'] = 'nullable|min:6';
        } else {
            // Create - email must be unique
            $rules['email'] = 'required|email|unique:users,email';
            $rules['password'] = 'required|min:6';
        }

        return $rules;
    }
}
