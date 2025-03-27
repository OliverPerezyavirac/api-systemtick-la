<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
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
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'priority' => 'sometimes|required|in:low,medium,high,critical',
            'status' => 'sometimes|required|in:new,open,in_progress,resolved,closed',
            'assignee_id' => 'sometimes|nullable|exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => __('validation.required', ['attribute' => 'title']),
            'description.required' => __('validation.required', ['attribute' => 'description']),
            'priority.required' => __('validation.required', ['attribute' => 'priority']),
            'status.required' => __('validation.required', ['attribute' => 'status']),
            'assignee_id.exists' => __('validation.exists', ['attribute' => 'assignee']),
        ];
    }
}
