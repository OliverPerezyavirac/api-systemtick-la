<?php

namespace App\Http\Requests\Workspaces;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkspaceRequest extends FormRequest
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
            'name' => 'required|string|unique:workspaces|max:255',
            'description' => 'nullable|string',
            'logo_url' => 'nullable|url',
            'department_id' => 'required|exists:departments,id'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('validation.required', ['attribute' => 'name']),
            'name.unique' => __('validation.unique', ['attribute' => 'name']),
            'owner_id.exists' => __('validation.exists', ['attribute' => 'owner']),
            'department_id.required' => __('validation.required', ['attribute' => 'department']),
            'department_id.exists' => __('validation.exists', ['attribute' => 'department'])
        ];
    }
}
