<?php

namespace App\Http\Requests\Backend\Tickets;

use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;

class StoreTicketReplyRequest extends FormRequest
{
    use ValidatesTicketAttachments;

    public function authorize(): bool
    {
        /** @var Ticket $ticket */
        $ticket = $this->route('ticket');

        return $this->user()?->can('reply', $ticket) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:10000'],
            ...$this->attachmentRules(),
        ];
    }
}
