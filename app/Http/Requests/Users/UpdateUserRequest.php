<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $this->route('id'),
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'avatar_url' => 'nullable|url',
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => __('validation.required', ['attribute' => 'first name']),
            'last_name.required' => __('validation.required', ['attribute' => 'last name']),
            'email.required' => __('validation.required', ['attribute' => 'email']),
            'email.unique' => __('validation.unique', ['attribute' => 'email']),
            'password.confirmed' => __('validation.confirmed', ['attribute' => 'password']),
        ];
    }
}
