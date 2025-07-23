<?php

namespace App\Http\Requests\Workspaces;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkspaceRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:255|unique:workspaces,name,' . $this->workspace,
            'description' => 'nullable|string',
            'logo_url' => 'nullable|url',
            'department_id' => 'sometimes|required|exists:departments,id'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('validation.required', ['attribute' => 'name']),
            'name.unique' => __('validation.unique', ['attribute' => 'name']),
            'department_id.required' => __('validation.required', ['attribute' => 'department']),
            'department_id.exists' => __('validation.exists', ['attribute' => 'department'])
        ];
    }
}
