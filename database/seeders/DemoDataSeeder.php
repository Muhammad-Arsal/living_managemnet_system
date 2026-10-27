<?php

namespace Database\Seeders;

use App\Enums\RentalMode;
use App\Enums\TicketParticipantRole;
use App\Enums\TicketStatus;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Room;
use App\Models\Tenancy;
use App\Models\Tenant;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketType;
use App\Repositories\Contracts\CouncilRepositoryInterface;
use App\Repositories\Contracts\StaffRepositoryInterface;
use App\Repositories\Contracts\TicketRepositoryInterface;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    /**
     * Realistic UK demo data covering occupancy scenarios built so far:
     * whole occupied / vacant / past tenancy, per-room full / partial / vacant,
     * current + past + unassigned tenants, multiple councils & staff, sample tickets.
     */
    public function run(): void
    {
        if (Tenant::query()->where('email', 'alice.harper@example.com')->exists()) {
            $this->command?->warn('Demo data already present — skipping DemoDataSeeder.');

            return;
        }

        $types = PropertyType::query()->where('is_active', true)->orderBy('sort_order')->get()->keyBy('slug');

        if ($types->isEmpty()) {
            $this->command?->error('Property types missing. Run PropertyTypeSeeder first.');

            return;
        }

        DB::transaction(function () use ($types) {
            $this->seedExtraStaff();
            $councils = $this->seedCouncils();
            $tenants = $this->seedTenants();
            $properties = $this->seedProperties($types);
            $this->seedTenancies($tenants, $properties);
            $this->seedSampleTickets($councils);
        });

        $this->command?->info('Demo data seeded: councils, staff, properties, rooms, tenants, tenancies, tickets.');
    }

    private function seedExtraStaff(): void
    {
        $staff = app(StaffRepositoryInterface::class);

        $rows = [
            [
                'email' => 'sarah.mitchell@example.com',
                'name' => 'Sarah Mitchell',
                'profile' => ['job_title' => 'Housing Officer', 'phone' => '0113 555 0101'],
            ],
            [
                'email' => 'james.okonkwo@example.com',
                'name' => 'James Okonkwo',
                'profile' => ['job_title' => 'Maintenance Coordinator', 'phone' => '0113 555 0102'],
            ],
            [
                'email' => 'priya.sharma@example.com',
                'name' => 'Priya Sharma',
                'profile' => ['job_title' => 'Tenancy Support', 'phone' => '0113 555 0103'],
            ],
            [
                'email' => 'inactive.staff@example.com',
                'name' => 'Inactive Staff',
                'is_active' => false,
                'profile' => ['job_title' => 'Former Officer', 'phone' => '0113 555 0199'],
            ],
        ];

        foreach ($rows as $row) {
            if ($staff->findByEmail($row['email'])) {
                continue;
            }

            $staff->createWithProfile([
                'name' => $row['name'],
                'email' => $row['email'],
                'password' => 'password',
                'email_verified_at' => now(),
                'is_active' => $row['is_active'] ?? true,
            ], $row['profile']);
        }
    }

    /**
     * @return array<string, \App\Models\Council>
     */
    private function seedCouncils(): array
    {
        $repo = app(CouncilRepositoryInterface::class);
        $created = [];

        $rows = [
            [
                'key' => 'leeds',
                'name' => 'Leeds City Council',
                'email' => 'housing.leeds@example.com',
                'profile' => [
                    'organization' => 'Leeds City Council Housing',
                    'phone' => '0113 222 4444',
                    'bio' => 'Primary housing referrals partner for Leeds.',
                ],
            ],
            [
                'key' => 'bradford',
                'name' => 'Bradford Metropolitan Council',
                'email' => 'housing.bradford@example.com',
                'profile' => [
                    'organization' => 'Bradford MDC',
                    'phone' => '01274 431000',
                    'bio' => 'Supports temporary and supported accommodation placements.',
                ],
            ],
            [
                'key' => 'york',
                'name' => 'City of York Council',
                'email' => 'housing.york@example.com',
                'profile' => [
                    'organization' => 'City of York Council',
                    'phone' => '01904 551550',
                    'bio' => 'Works with providers on homeless prevention schemes.',
                ],
            ],
            [
                'key' => 'wakefield',
                'name' => 'Wakefield Council',
                'email' => 'housing.wakefield@example.com',
                'profile' => [
                    'organization' => 'Wakefield Council Housing Options',
                    'phone' => '01924 302080',
                    'bio' => 'Referral partner for single adults and families.',
                ],
            ],
            [
                'key' => 'kirklees',
                'name' => 'Kirklees Council',
                'email' => 'housing.kirklees@example.com',
                'profile' => [
                    'organization' => 'Kirklees Council',
                    'phone' => '01484 221000',
                    'bio' => 'Covers Huddersfield and surrounding areas.',
                ],
            ],
            [
                'key' => 'inactive',
                'name' => 'Former Partner Council',
                'email' => 'inactive.council@example.com',
                'is_active' => false,
                'verified' => false,
                'profile' => [
                    'organization' => 'Archived Partner',
                    'phone' => '0161 000 0000',
                    'bio' => 'Inactive account kept for historical ticket access.',
                ],
            ],
        ];

        foreach ($rows as $row) {
            $existing = $repo->findByEmail($row['email']);

            if ($existing) {
                $created[$row['key']] = $existing;

                continue;
            }

            $created[$row['key']] = $repo->createWithProfile([
                'name' => $row['name'],
                'email' => $row['email'],
                'password' => 'password',
                'email_verified_at' => ($row['verified'] ?? true) ? now() : null,
                'is_active' => $row['is_active'] ?? true,
            ], $row['profile']);
        }

        return $created;
    }

    /**
     * @return array<string, Tenant>
     */
    private function seedTenants(): array
    {
        $rows = [
            // Current — whole property
            ['key' => 'alice', 'first_name' => 'Alice', 'last_name' => 'Harper', 'mobile' => '07700 900101', 'email' => 'alice.harper@example.com', 'city' => 'Leeds'],
            ['key' => 'ben', 'first_name' => 'Ben', 'last_name' => 'Foster', 'mobile' => '07700 900102', 'email' => 'ben.foster@example.com', 'city' => 'Leeds'],
            // Current — per-room (Cedar full)
            ['key' => 'chloe', 'first_name' => 'Chloe', 'last_name' => 'Nguyen', 'mobile' => '07700 900103', 'email' => 'chloe.nguyen@example.com', 'city' => 'Bradford'],
            ['key' => 'daniel', 'first_name' => 'Daniel', 'last_name' => 'Price', 'mobile' => '07700 900104', 'email' => 'daniel.price@example.com', 'city' => 'Bradford'],
            ['key' => 'emma', 'first_name' => 'Emma', 'last_name' => 'Walsh', 'mobile' => '07700 900105', 'email' => 'emma.walsh@example.com', 'city' => 'Bradford'],
            ['key' => 'farah', 'first_name' => 'Farah', 'last_name' => 'Khan', 'mobile' => '07700 900106', 'email' => 'farah.khan@example.com', 'city' => 'Bradford'],
            // Current — per-room (Birch partial)
            ['key' => 'george', 'first_name' => 'George', 'last_name' => 'Patel', 'mobile' => '07700 900107', 'email' => 'george.patel@example.com', 'city' => 'York'],
            ['key' => 'hannah', 'first_name' => 'Hannah', 'last_name' => 'Brooks', 'mobile' => '07700 900108', 'email' => 'hannah.brooks@example.com', 'city' => 'York'],
            // Current — per-room (Ash partial, 1 room)
            ['key' => 'imran', 'first_name' => 'Imran', 'last_name' => 'Ali', 'mobile' => '07700 900109', 'email' => 'imran.ali@example.com', 'city' => 'Wakefield'],
            // Past tenants
            ['key' => 'past_jordan', 'first_name' => 'Jordan', 'last_name' => 'Ellis', 'mobile' => '07700 900201', 'email' => 'jordan.ellis@example.com', 'city' => 'Leeds'],
            ['key' => 'past_katie', 'first_name' => 'Katie', 'last_name' => 'Morris', 'mobile' => '07700 900202', 'email' => 'katie.morris@example.com', 'city' => 'Huddersfield'],
            // Unassigned (available to place)
            ['key' => 'liam', 'first_name' => 'Liam', 'last_name' => 'O\'Connor', 'mobile' => '07700 900301', 'email' => 'liam.oconnor@example.com', 'city' => 'Leeds'],
            ['key' => 'maya', 'first_name' => 'Maya', 'last_name' => 'Singh', 'mobile' => '07700 900302', 'email' => 'maya.singh@example.com', 'city' => 'Bradford'],
            ['key' => 'noah', 'first_name' => 'Noah', 'last_name' => 'Bennett', 'mobile' => '07700 900303', 'email' => 'noah.bennett@example.com', 'city' => 'York'],
            ['key' => 'olivia', 'first_name' => 'Olivia', 'last_name' => 'Grant', 'mobile' => '07700 900304', 'email' => 'olivia.grant@example.com', 'city' => 'Wakefield'],
        ];

        $tenants = [];

        foreach ($rows as $row) {
            $tenants[$row['key']] = Tenant::query()->create([
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'mobile_number' => $row['mobile'],
                'email' => $row['email'],
                'city' => $row['city'],
                'country' => 'United Kingdom',
            ]);
        }

        return $tenants;
    }

    /**
     * @param  \Illuminate\Support\Collection<string, PropertyType>  $types
     * @return array<string, Property>
     */
    private function seedProperties($types): array
    {
        $typeId = fn (string $slug, string $fallback) => ($types->get($slug) ?? $types->get($fallback) ?? $types->first())->id;

        $definitions = [
            // Whole — occupied
            [
                'key' => 'oak',
                'rental_mode' => RentalMode::Whole,
                'property_type_id' => $typeId('semi-detached-house', 'house'),
                'name' => 'Oak House',
                'address_line_1' => '14 Oak Avenue',
                'city' => 'Leeds',
                'county' => 'West Yorkshire',
                'postcode' => 'LS6 1AA',
                'rooms' => [],
            ],
            // Whole — vacant (never rented)
            [
                'key' => 'willow',
                'rental_mode' => RentalMode::Whole,
                'property_type_id' => $typeId('bungalow', 'house'),
                'name' => 'Willow Cottage',
                'address_line_1' => '3 Willow Lane',
                'city' => 'Leeds',
                'county' => 'West Yorkshire',
                'postcode' => 'LS8 2BB',
                'rooms' => [],
            ],
            // Whole — vacant with past tenancy
            [
                'key' => 'maple',
                'rental_mode' => RentalMode::Whole,
                'property_type_id' => $typeId('flat', 'flat'),
                'name' => 'Maple Flat',
                'address_line_1' => '22 Maple Road',
                'address_line_2' => 'Flat 2',
                'city' => 'Bradford',
                'county' => 'West Yorkshire',
                'postcode' => 'BD1 3CC',
                'rooms' => [],
            ],
            // Whole — second occupied
            [
                'key' => 'pine',
                'rental_mode' => RentalMode::Whole,
                'property_type_id' => $typeId('terraced-house', 'house'),
                'name' => 'Pine View',
                'address_line_1' => '9 Pine Street',
                'city' => 'Wakefield',
                'county' => 'West Yorkshire',
                'postcode' => 'WF1 4DD',
                'rooms' => [],
            ],
            // Per-room — fully occupied (4/4)
            [
                'key' => 'cedar',
                'rental_mode' => RentalMode::PerRoom,
                'property_type_id' => $typeId('house', 'house'),
                'name' => 'Cedar HMO',
                'address_line_1' => '48 Cedar Road',
                'city' => 'Bradford',
                'county' => 'West Yorkshire',
                'postcode' => 'BD7 5EE',
                'rooms' => ['Room 1', 'Room 2', 'Room 3', 'Room 4'],
            ],
            // Per-room — partially occupied (2/4)
            [
                'key' => 'birch',
                'rental_mode' => RentalMode::PerRoom,
                'property_type_id' => $typeId('maisonette', 'flat'),
                'name' => 'Birch Residences',
                'address_line_1' => '17 Birch Court',
                'city' => 'York',
                'county' => 'North Yorkshire',
                'postcode' => 'YO10 5FF',
                'rooms' => ['Room A', 'Room B', 'Room C', 'Room D'],
            ],
            // Per-room — vacant (0/3)
            [
                'key' => 'elm',
                'rental_mode' => RentalMode::PerRoom,
                'property_type_id' => $typeId('detached-house', 'house'),
                'name' => 'Elm Court',
                'address_line_1' => '5 Elm Drive',
                'city' => 'Huddersfield',
                'county' => 'West Yorkshire',
                'postcode' => 'HD1 6GG',
                'rooms' => ['Front Room', 'Back Room', 'Attic Room'],
            ],
            // Per-room — lightly filled (1/5)
            [
                'key' => 'ash',
                'rental_mode' => RentalMode::PerRoom,
                'property_type_id' => $typeId('house', 'house'),
                'name' => 'Ash Lodge',
                'address_line_1' => '31 Ash Grove',
                'city' => 'Wakefield',
                'county' => 'West Yorkshire',
                'postcode' => 'WF2 7HH',
                'rooms' => ['Room 1', 'Room 2', 'Room 3', 'Room 4', 'Room 5'],
            ],
        ];

        $properties = [];

        foreach ($definitions as $def) {
            $property = Property::query()->create([
                'property_type_id' => $def['property_type_id'],
                'rental_mode' => $def['rental_mode']->value,
                'name' => $def['name'],
                'address_line_1' => $def['address_line_1'],
                'address_line_2' => $def['address_line_2'] ?? null,
                'city' => $def['city'],
                'county' => $def['county'],
                'postcode' => $def['postcode'],
                'country' => 'United Kingdom',
            ]);

            foreach ($def['rooms'] as $index => $roomName) {
                Room::query()->create([
                    'property_id' => $property->id,
                    'name' => $roomName,
                    'sort_order' => $index + 1,
                ]);
            }

            $properties[$def['key']] = $property->load('rooms');
        }

        return $properties;
    }

    /**
     * @param  array<string, Tenant>  $tenants
     * @param  array<string, Property>  $properties
     */
    private function seedTenancies(array $tenants, array $properties): void
    {
        // Whole — current
        $this->createTenancy($tenants['alice'], $properties['oak'], '2025-01-15');
        $this->createTenancy($tenants['ben'], $properties['pine'], '2025-06-01');

        // Whole — past on Maple (now vacant)
        $this->createTenancy($tenants['past_jordan'], $properties['maple'], '2024-03-01', '2025-09-30');

        // Per-room — Cedar fully occupied
        $cedarRooms = $properties['cedar']->rooms->values();
        $this->createTenancy($tenants['chloe'], $properties['cedar'], '2025-02-01', null, $cedarRooms[0]);
        $this->createTenancy($tenants['daniel'], $properties['cedar'], '2025-03-10', null, $cedarRooms[1]);
        $this->createTenancy($tenants['emma'], $properties['cedar'], '2025-04-05', null, $cedarRooms[2]);
        $this->createTenancy($tenants['farah'], $properties['cedar'], '2025-05-20', null, $cedarRooms[3]);

        // Per-room — Birch partial (2 of 4)
        $birchRooms = $properties['birch']->rooms->values();
        $this->createTenancy($tenants['george'], $properties['birch'], '2025-07-01', null, $birchRooms[0]);
        $this->createTenancy($tenants['hannah'], $properties['birch'], '2025-08-12', null, $birchRooms[1]);

        // Per-room — Ash light partial (1 of 5)
        $ashRooms = $properties['ash']->rooms->values();
        $this->createTenancy($tenants['imran'], $properties['ash'], '2025-09-01', null, $ashRooms[0]);

        // Past room tenancy on Elm (property still vacant overall)
        $elmRooms = $properties['elm']->rooms->values();
        $this->createTenancy($tenants['past_katie'], $properties['elm'], '2024-06-01', '2025-08-15', $elmRooms[0]);
    }

    private function createTenancy(
        Tenant $tenant,
        Property $property,
        string $startedOn,
        ?string $endedOn = null,
        ?Room $room = null,
    ): Tenancy {
        return Tenancy::query()->create([
            'tenant_id' => $tenant->id,
            'property_id' => $property->id,
            'room_id' => $room?->id,
            'started_on' => $startedOn,
            'ended_on' => $endedOn,
        ]);
    }

    /**
     * @param  array<string, \App\Models\Council>  $councils
     */
    private function seedSampleTickets(array $councils): void
    {
        $type = TicketType::query()->where('slug', 'maintenance')->first()
            ?? TicketType::query()->first();
        $priorityMedium = TicketPriority::query()->where('slug', 'medium')->first()
            ?? TicketPriority::query()->first();
        $priorityHigh = TicketPriority::query()->where('slug', 'high')->first()
            ?? $priorityMedium;

        $staff = app(StaffRepositoryInterface::class)->findByEmail('staff@example.com')
            ?? app(StaffRepositoryInterface::class)->findByEmail('sarah.mitchell@example.com');

        if ($type === null || $priorityMedium === null || $staff === null) {
            return;
        }

        $tickets = app(TicketRepositoryInterface::class);
        $leeds = $councils['leeds'] ?? null;
        $bradford = $councils['bradford'] ?? null;

        if ($leeds !== null) {
            $open = $tickets->create([
                'reference' => 'TMP-DEMO-1',
                'subject' => 'Boiler issue at Oak House',
                'body' => 'Tenant reports no hot water since yesterday evening. Please arrange an engineer visit.',
                'ticket_type_id' => $type->id,
                'ticket_priority_id' => $priorityHigh?->id ?? $priorityMedium->id,
                'status' => TicketStatus::Open->value,
                'creator_type' => $leeds->getMorphClass(),
                'creator_id' => $leeds->getKey(),
                'assignee_type' => $staff->getMorphClass(),
                'assignee_id' => $staff->getKey(),
            ]);
            $tickets->addParticipant($open, $leeds, TicketParticipantRole::Creator->value);
            $tickets->addParticipant($open, $staff, TicketParticipantRole::Assignee->value);
        }

        if ($bradford !== null) {
            $closed = $tickets->create([
                'reference' => 'TMP-DEMO-2',
                'subject' => 'Broken lock on Cedar HMO Room 2',
                'body' => 'Front door lock to Room 2 was sticking. Locksmith attended and replaced the cylinder.',
                'ticket_type_id' => $type->id,
                'ticket_priority_id' => $priorityMedium->id,
                'status' => TicketStatus::Closed->value,
                'creator_type' => $bradford->getMorphClass(),
                'creator_id' => $bradford->getKey(),
                'assignee_type' => $staff->getMorphClass(),
                'assignee_id' => $staff->getKey(),
            ]);
            $tickets->addParticipant($closed, $bradford, TicketParticipantRole::Creator->value);
            $tickets->addParticipant($closed, $staff, TicketParticipantRole::Assignee->value);
            $tickets->addReply($closed, $staff, 'Lock replaced. Tenant confirmed access is fine. Closing ticket.');
        }
    }
}
