<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkspaceUserRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:guest,member,manager,admin',
        ];
    }

    public function messages()
    {
        return [
            'workspace_id.required' => __('validation.required', ['attribute' => 'workspace']),
            'workspace_id.exists' => __('validation.exists', ['attribute' => 'workspace']),
            'user_id.required' => __('validation.required', ['attribute' => 'user']),
            'user_id.exists' => __('validation.exists', ['attribute' => 'user']),
            'role.required' => __('validation.required', ['attribute' => 'role']),
            'role.in' => __('validation.in', ['attribute' => 'role']),
        ];
    }
}
