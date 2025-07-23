<?php

namespace App\Http\Requests\Tickets;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical',
            'category' => 'required|in:bug,feature,support,other',
            'assignee_email' => 'nullable|email|exists:users,email',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => __('validation.required', ['attribute' => 'title']),
            'description.required' => __('validation.required', ['attribute' => 'description']),
            'priority.required' => __('validation.required', ['attribute' => 'priority']),
            'category.required' => __('validation.required', ['attribute' => 'category']),
            'assignee_email.email' => __('validation.email', ['attribute' => 'email']),
            'assignee_email.exists' => __('validation.exists', ['attribute' => 'usuario']),
        ];
    }
}
