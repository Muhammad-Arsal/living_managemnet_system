<?php

namespace App\Http\Requests\Backend\Tickets;

use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends FormRequest
{
    use ValidatesTicketAttachments;

    public function authorize(): bool
    {
        return $this->user()?->can('create', Ticket::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $portal = $this->user()?->portalKey();
        $assignable = $portal ? config('tickets.assignable.'.$portal) : null;
        $table = is_array($assignable) ? (new $assignable['model'])->getTable() : 'staff';

        return [
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:10000'],
            'ticket_type_id' => [
                'required',
                Rule::exists('ticket_types', 'id')->where('is_active', true),
            ],
            'ticket_priority_id' => [
                'required',
                Rule::exists('ticket_priorities', 'id')->where('is_active', true),
            ],
            'assignee_id' => [
                'required',
                Rule::exists($table, 'id')->where('is_active', true),
            ],
            ...$this->attachmentRules(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'ticket_type_id' => 'ticket type',
            'ticket_priority_id' => 'ticket priority',
            'assignee_id' => 'assignee',
            'attachments' => 'attachments',
            'attachments.*' => 'attachment',
        ];
    }
}
