<?php

namespace App\Http\Requests\Workspaces;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkspaceUserRequest extends FormRequest
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
            'role' => 'required|in:guest,member,manager,admin',
        ];
    }

    public function messages()
    {
        return [
            'role.required' => __('validation.required', ['attribute' => 'role']),
            'role.in' => __('validation.in', ['attribute' => 'role']),
        ];
    }
}
