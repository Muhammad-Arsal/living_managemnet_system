<?php

namespace Tests\Feature\Tickets;

use App\Models\Admin;
use App\Models\Council;
use App\Models\Staff;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketType;
use App\Notifications\TicketActivityNotification;
use Database\Seeders\EmailTemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketFlowTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected Staff $staff;

    protected Council $council;

    protected TicketType $type;

    protected TicketPriority $priority;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(EmailTemplateSeeder::class);

        $this->admin = $this->makeAdmin('admin@example.com');
        $this->staff = $this->makeStaff('staff@example.com');
        $this->council = $this->makeCouncil('council@example.com');
        $this->type = TicketType::query()->create([
            'name' => 'General',
            'slug' => 'general',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $this->priority = TicketPriority::query()->create([
            'name' => 'Medium',
            'slug' => 'medium',
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    public function test_council_creates_ticket_notifies_staff_not_admin(): void
    {
        $response = $this->actingAs($this->council, 'council')->post(route('council.tickets.store'), $this->ticketPayload([
            'assignee_id' => $this->staff->id,
        ]));

        $ticket = Ticket::query()->first();
        $this->assertNotNull($ticket);
        $response->assertRedirect(route('council.tickets.show', $ticket));

        $this->assertSame('staff', $ticket->assignee_type);
        $this->assertSame($this->staff->id, $ticket->assignee_id);
        $this->assertSame($this->type->id, $ticket->ticket_type_id);
        $this->assertSame($this->priority->id, $ticket->ticket_priority_id);

        $this->assertDatabaseCount('notifications', 1);
        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => 'staff',
            'notifiable_id' => $this->staff->id,
        ]);
        $this->assertDatabaseMissing('notifications', [
            'notifiable_type' => 'admin',
            'notifiable_id' => $this->admin->id,
        ]);
    }

    public function test_admin_reply_subscribes_admin_and_later_replies_skip_sender(): void
    {
        $this->actingAs($this->council, 'council')->post(route('council.tickets.store'), $this->ticketPayload([
            'assignee_id' => $this->staff->id,
        ]));

        $ticket = Ticket::query()->firstOrFail();

        $this->actingAs($this->admin, 'admin')->post(route('admin.tickets.replies.store', $ticket), [
            'body' => 'Admin joining the thread.',
        ])->assertRedirect(route('admin.tickets.show', $ticket));

        $this->assertTrue($ticket->fresh()->hasParticipant($this->admin));

        Notification::fake();

        $this->actingAs($this->staff, 'staff')->post(route('staff.tickets.replies.store', $ticket), [
            'body' => 'Staff follow-up.',
        ])->assertRedirect(route('staff.tickets.show', $ticket));

        Notification::assertSentTo($this->admin, TicketActivityNotification::class);
        Notification::assertSentTo($this->council, TicketActivityNotification::class);
        Notification::assertNotSentTo($this->staff, TicketActivityNotification::class);
        Notification::assertCount(2);
    }

    public function test_staff_creates_ticket_for_admin(): void
    {
        $this->actingAs($this->staff, 'staff')->post(route('staff.tickets.store'), $this->ticketPayload([
            'assignee_id' => $this->admin->id,
        ]))->assertRedirect();

        $ticket = Ticket::query()->firstOrFail();
        $this->assertSame('admin', $ticket->assignee_type);
        $this->assertSame($this->admin->id, $ticket->assignee_id);

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => 'admin',
            'notifiable_id' => $this->admin->id,
        ]);
        $this->assertDatabaseMissing('notifications', [
            'notifiable_type' => 'staff',
            'notifiable_id' => $this->staff->id,
        ]);
    }

    public function test_council_cannot_view_another_councils_ticket(): void
    {
        $this->actingAs($this->council, 'council')->post(route('council.tickets.store'), $this->ticketPayload([
            'assignee_id' => $this->staff->id,
        ]));

        $ticket = Ticket::query()->firstOrFail();
        $other = $this->makeCouncil('other-council@example.com');

        $this->actingAs($other, 'council')
            ->get(route('council.tickets.show', $ticket))
            ->assertForbidden();
    }

    public function test_admin_can_view_all_tickets_without_being_a_participant(): void
    {
        $this->actingAs($this->council, 'council')->post(route('council.tickets.store'), $this->ticketPayload([
            'assignee_id' => $this->staff->id,
        ]));

        $ticket = Ticket::query()->firstOrFail();

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.tickets.show', $ticket))
            ->assertOk();

        $this->assertFalse($ticket->fresh()->hasParticipant($this->admin));
    }

    public function test_disabled_type_cannot_be_used_on_new_tickets_but_existing_reference_remains(): void
    {
        $this->actingAs($this->council, 'council')->post(route('council.tickets.store'), $this->ticketPayload([
            'assignee_id' => $this->staff->id,
        ]));

        $ticket = Ticket::query()->firstOrFail();
        $this->type->update(['is_active' => false]);

        $this->assertSame($this->type->id, $ticket->fresh()->ticket_type_id);

        $this->actingAs($this->council, 'council')->post(route('council.tickets.store'), $this->ticketPayload([
            'assignee_id' => $this->staff->id,
            'ticket_type_id' => $this->type->id,
        ]))->assertSessionHasErrors('ticket_type_id');
    }

    public function test_admin_can_manage_ticket_types(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.settings.ticket-types.store'), [
                'name' => 'Access',
                'is_active' => 1,
                'sort_order' => 4,
            ])
            ->assertRedirect(route('admin.settings.ticket-types.index'));

        $this->assertDatabaseHas('ticket_types', ['name' => 'Access', 'is_active' => 1]);
    }

    public function test_support_tickets_index_filters_by_search_status_and_priority(): void
    {
        $otherPriority = TicketPriority::query()->create([
            'name' => 'Urgent',
            'slug' => 'urgent',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $this->actingAs($this->council, 'council')->post(route('council.tickets.store'), $this->ticketPayload([
            'subject' => 'Broken heater',
            'assignee_id' => $this->staff->id,
        ]));
        $this->actingAs($this->council, 'council')->post(route('council.tickets.store'), $this->ticketPayload([
            'subject' => 'Invoice query',
            'assignee_id' => $this->staff->id,
            'ticket_priority_id' => $otherPriority->id,
        ]));

        $response = $this->actingAs($this->staff, 'staff')
            ->get(route('staff.tickets.index', [
                'search' => 'heater',
                'status' => 'open',
                'ticket_priority_id' => $this->priority->id,
            ]));

        $response->assertOk()
            ->assertSee('Support tickets')
            ->assertSee('Subject or reference')
            ->assertSee('Broken heater')
            ->assertViewHas('tickets', function ($tickets) {
                return $tickets->count() === 1 && $tickets->first()?->subject === 'Broken heater';
            });
    }

    public function test_creating_a_ticket_stores_attachments(): void
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->image('damage.jpg');

        $this->actingAs($this->council, 'council')
            ->get(route('council.tickets.create'))
            ->assertOk()
            ->assertSee('Attachments');

        $this->actingAs($this->council, 'council')->post(route('council.tickets.store'), $this->ticketPayload([
            'assignee_id' => $this->staff->id,
            'attachments' => [$file],
        ]))->assertRedirect();

        $ticket = Ticket::query()->firstOrFail();

        $this->assertDatabaseHas('ticket_attachments', [
            'ticket_id' => $ticket->id,
            'original_name' => 'damage.jpg',
            'ticket_reply_id' => null,
        ]);

        $attachment = $ticket->openingAttachments()->firstOrFail();
        Storage::disk('local')->assertExists($attachment->path);

        $this->actingAs($this->staff, 'staff')
            ->get(route('staff.tickets.attachments.download', [$ticket, $attachment]))
            ->assertOk();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function ticketPayload(array $overrides = []): array
    {
        return array_merge([
            'subject' => 'Need assistance',
            'body' => 'Please help with this request.',
            'ticket_type_id' => $this->type->id,
            'ticket_priority_id' => $this->priority->id,
        ], $overrides);
    }

    private function makeAdmin(string $email): Admin
    {
        $admin = Admin::query()->create([
            'name' => 'Admin User',
            'email' => $email,
            'password' => 'password',
            'is_active' => true,
        ]);
        $admin->profile()->create([]);

        return $admin;
    }

    private function makeStaff(string $email): Staff
    {
        $staff = Staff::query()->create([
            'name' => 'Staff User',
            'email' => $email,
            'password' => 'password',
            'is_active' => true,
        ]);
        $staff->profile()->create([]);

        return $staff;
    }

    private function makeCouncil(string $email): Council
    {
        $council = Council::query()->create([
            'name' => 'Council User',
            'email' => $email,
            'password' => 'password',
            'is_active' => true,
        ]);
        $council->profile()->create([]);

        return $council;
    }
}
