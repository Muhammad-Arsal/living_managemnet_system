<?php

namespace Tests\Unit\Tickets;

use App\Mail\TemplateMailable;
use App\Models\Staff;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketType;
use App\Services\Tickets\TicketMailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TicketMailServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_created_email_through_template_mailable(): void
    {
        Mail::fake();

        $staff = Staff::query()->create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'password' => 'password',
            'is_active' => true,
        ]);
        $type = TicketType::query()->create([
            'name' => 'General',
            'slug' => 'general',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $priority = TicketPriority::query()->create([
            'name' => 'High',
            'slug' => 'high',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $ticket = Ticket::query()->create([
            'reference' => 'TKT-000001',
            'subject' => 'Help',
            'body' => 'Body',
            'ticket_type_id' => $type->id,
            'ticket_priority_id' => $priority->id,
            'status' => 'open',
            'creator_type' => 'staff',
            'creator_id' => $staff->id,
            'assignee_type' => 'staff',
            'assignee_id' => $staff->id,
        ]);
        $ticket->setRelation('type', $type);
        $ticket->setRelation('priority', $priority);

        app(TicketMailService::class)->sendCreated($staff, $ticket, $staff);

        Mail::assertSent(TemplateMailable::class, function (TemplateMailable $mail) use ($staff) {
            return $mail->hasTo($staff->email);
        });
    }
}
