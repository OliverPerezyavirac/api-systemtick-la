<?php

namespace App\Http\Requests\Comments;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Tickets\Ticket;

class StoreCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $ticket = Ticket::find($this->ticket);
        if (!$ticket) {
            return false;
        }

        // Si el comentario es privado, solo el creador y asignado pueden crear
        if ($this->visibility === 'private') {
            return $ticket->creator_id === Auth::id() || $ticket->assignee_id === Auth::id();
        }

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
            'content' => 'required|string',
            'visibility' => 'required|in:public,private',
        ];
    }

    public function messages()
    {
        return [
            'content.required' => __('validation.required', ['attribute' => 'content']),
            'visibility.required' => __('validation.required', ['attribute' => 'visibility']),
        ];
    }
}
