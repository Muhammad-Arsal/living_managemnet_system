<?php

namespace Tests\Feature\Admin;

use App\Enums\RentalMode;
use App\Models\Admin;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Room;
use App\Models\Tenancy;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomTenancyTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected PropertyType $propertyType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->makeAdmin();
        $this->propertyType = PropertyType::query()->create([
            'name' => 'House',
            'slug' => 'house',
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    public function test_create_whole_property_has_no_rooms(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.store'), $this->propertyPayload())
            ->assertRedirect(route('admin.properties.index'));

        $property = Property::query()->first();
        $this->assertNotNull($property);
        $this->assertSame(RentalMode::Whole, $property->rental_mode);
        $this->assertSame(0, $property->rooms()->count());
    }

    public function test_create_per_room_without_rooms_fails(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->from(route('admin.properties.create'))
            ->post(route('admin.properties.store'), $this->propertyPayload([
                'rental_mode' => 'per_room',
            ]))
            ->assertRedirect()
            ->assertSessionHasErrors('rooms');
    }

    public function test_create_per_room_with_rooms_persists_rooms(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.store'), $this->propertyPayload([
                'rental_mode' => 'per_room',
                'rooms' => [
                    ['name' => 'Room 1'],
                    ['name' => 'Room 2'],
                ],
            ]))
            ->assertRedirect(route('admin.properties.index'));

        $property = Property::query()->first();
        $this->assertNotNull($property);
        $this->assertSame(RentalMode::PerRoom, $property->rental_mode);
        $this->assertSame(2, $property->rooms()->count());
        $this->assertDatabaseHas('rooms', ['property_id' => $property->id, 'name' => 'Room 1']);
    }

    public function test_two_tenants_can_occupy_different_rooms(): void
    {
        $property = $this->createPerRoomProperty();
        $rooms = $property->rooms;
        $first = $this->createTenant(['email' => 'one@example.com', 'mobile_number' => '07111111111']);
        $second = $this->createTenant(['email' => 'two@example.com', 'mobile_number' => '07222222222']);

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.rooms.tenancies.store', [$property, $rooms[0]]), [
                'tenant_id' => $first->id,
                'started_on' => '2026-01-01',
            ])
            ->assertRedirect();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.rooms.tenancies.store', [$property, $rooms[1]]), [
                'tenant_id' => $second->id,
                'started_on' => '2026-01-02',
            ])
            ->assertRedirect();

        $this->assertSame(2, Tenancy::query()->whereNull('ended_on')->count());
        $this->assertTrue($property->fresh()->isOccupied());
        $this->assertDatabaseHas('tenancies', [
            'room_id' => $rooms[0]->id,
            'active_room_id' => $rooms[0]->id,
            'active_whole_property_id' => null,
        ]);
    }

    public function test_same_tenant_cannot_hold_two_room_tenancies(): void
    {
        $property = $this->createPerRoomProperty();
        $rooms = $property->rooms;
        $tenant = $this->createTenant();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.rooms.tenancies.store', [$property, $rooms[0]]), [
                'tenant_id' => $tenant->id,
                'started_on' => '2026-01-01',
            ])
            ->assertRedirect();

        $this->actingAs($this->admin, 'admin')
            ->from(route('admin.properties.edit', $property))
            ->post(route('admin.properties.rooms.tenancies.store', [$property, $rooms[1]]), [
                'tenant_id' => $tenant->id,
                'started_on' => '2026-01-02',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('tenant_id');

        $this->assertSame(1, Tenancy::query()->whereNull('ended_on')->count());
    }

    public function test_second_tenant_cannot_take_occupied_room(): void
    {
        $property = $this->createPerRoomProperty();
        $room = $property->rooms->first();
        $first = $this->createTenant(['email' => 'one@example.com', 'mobile_number' => '07111111111']);
        $second = $this->createTenant(['email' => 'two@example.com', 'mobile_number' => '07222222222']);

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.rooms.tenancies.store', [$property, $room]), [
                'tenant_id' => $first->id,
                'started_on' => '2026-01-01',
            ]);

        $this->actingAs($this->admin, 'admin')
            ->from(route('admin.properties.edit', $property))
            ->post(route('admin.properties.rooms.tenancies.store', [$property, $room]), [
                'tenant_id' => $second->id,
                'started_on' => '2026-02-01',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('room_id');
    }

    public function test_cannot_assign_whole_tenancy_on_per_room_property(): void
    {
        $property = $this->createPerRoomProperty();
        $tenant = $this->createTenant();

        $this->actingAs($this->admin, 'admin')
            ->from(route('admin.properties.edit', $property))
            ->post(route('admin.properties.tenancies.store', $property), [
                'tenant_id' => $tenant->id,
                'started_on' => '2026-01-01',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('tenant_id');
    }

    public function test_cannot_assign_room_tenancy_on_whole_property(): void
    {
        $property = $this->createProperty();
        $tenant = $this->createTenant();
        $room = Room::query()->create([
            'property_id' => $property->id,
            'name' => 'Illegal Room',
            'sort_order' => 1,
        ]);

        $this->actingAs($this->admin, 'admin')
            ->from(route('admin.properties.edit', $property))
            ->post(route('admin.properties.rooms.tenancies.store', [$property, $room]), [
                'tenant_id' => $tenant->id,
                'started_on' => '2026-01-01',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('room_id');
    }

    public function test_cannot_switch_to_per_room_with_open_tenancy(): void
    {
        $property = $this->createProperty();
        $tenant = $this->createTenant();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.tenancies.store', $property), [
                'tenant_id' => $tenant->id,
                'started_on' => '2026-01-01',
            ]);

        $this->actingAs($this->admin, 'admin')
            ->from(route('admin.properties.edit', $property))
            ->put(route('admin.properties.update', $property), $this->propertyPayload([
                'rental_mode' => 'per_room',
                'rooms' => [
                    ['name' => 'Room 1'],
                ],
            ]))
            ->assertRedirect()
            ->assertSessionHasErrors('rental_mode');
    }

    public function test_cannot_switch_to_whole_with_room_history(): void
    {
        $property = $this->createPerRoomProperty();
        $room = $property->rooms->first();
        $tenant = $this->createTenant();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.rooms.tenancies.store', [$property, $room]), [
                'tenant_id' => $tenant->id,
                'started_on' => '2026-01-01',
            ]);

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.properties.rooms.tenancies.end', [$property, $room]), [
                'ended_on' => '2026-06-01',
            ])
            ->assertRedirect();

        $this->actingAs($this->admin, 'admin')
            ->from(route('admin.properties.edit', $property))
            ->put(route('admin.properties.update', $property), $this->propertyPayload([
                'rental_mode' => 'whole',
            ]))
            ->assertRedirect()
            ->assertSessionHasErrors('rental_mode');
    }

    public function test_can_switch_to_whole_when_rooms_have_no_history(): void
    {
        $property = $this->createPerRoomProperty();

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.properties.update', $property), $this->propertyPayload([
                'rental_mode' => 'whole',
            ]))
            ->assertRedirect(route('admin.properties.edit', ['property' => $property, 'tab' => 'overview']))
            ->assertSessionHasNoErrors();

        $property->refresh();
        $this->assertSame(RentalMode::Whole, $property->rental_mode);
        $this->assertSame(0, $property->rooms()->count());
    }

    public function test_cannot_delete_room_with_history_or_last_room(): void
    {
        $property = $this->createPerRoomProperty();
        $room = $property->rooms->first();
        $other = $property->rooms->last();
        $tenant = $this->createTenant();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.rooms.tenancies.store', [$property, $room]), [
                'tenant_id' => $tenant->id,
                'started_on' => '2026-01-01',
            ]);

        $this->actingAs($this->admin, 'admin')
            ->from(route('admin.properties.edit', $property))
            ->delete(route('admin.properties.rooms.destroy', [$property, $room]))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.properties.rooms.destroy', [$property, $other]))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertSame(1, $property->rooms()->count());

        $this->actingAs($this->admin, 'admin')
            ->from(route('admin.properties.edit', $property))
            ->delete(route('admin.properties.rooms.destroy', [$property, $room]))
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    public function test_room_date_overlap_is_rejected(): void
    {
        $property = $this->createPerRoomProperty();
        $room = $property->rooms->first();
        $first = $this->createTenant(['email' => 'one@example.com', 'mobile_number' => '07111111111']);
        $second = $this->createTenant(['email' => 'two@example.com', 'mobile_number' => '07222222222']);

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.rooms.tenancies.store', [$property, $room]), [
                'tenant_id' => $first->id,
                'started_on' => '2026-01-01',
            ]);

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.properties.rooms.tenancies.end', [$property, $room]), [
                'ended_on' => '2026-06-01',
            ]);

        $this->actingAs($this->admin, 'admin')
            ->from(route('admin.properties.edit', $property))
            ->post(route('admin.properties.rooms.tenancies.store', [$property, $room]), [
                'tenant_id' => $second->id,
                'started_on' => '2026-05-01',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('started_on');
    }

    public function test_tenant_side_assign_requires_room_for_per_room_property(): void
    {
        $property = $this->createPerRoomProperty();
        $room = $property->rooms->first();
        $tenant = $this->createTenant();

        $this->actingAs($this->admin, 'admin')
            ->from(route('admin.tenants.edit', $tenant))
            ->post(route('admin.tenants.tenancies.store', $tenant), [
                'property_id' => $property->id,
                'started_on' => '2026-01-01',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('room_id');

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.tenants.tenancies.store', $tenant), [
                'property_id' => $property->id,
                'room_id' => $room->id,
                'started_on' => '2026-01-01',
            ])
            ->assertRedirect(route('admin.tenants.edit', ['tenant' => $tenant, 'tab' => 'current']))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('tenancies', [
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'ended_on' => null,
        ]);
    }

    public function test_partial_occupancy_label(): void
    {
        $property = $this->createPerRoomProperty();
        $tenant = $this->createTenant();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.rooms.tenancies.store', [$property, $property->rooms->first()]), [
                'tenant_id' => $tenant->id,
                'started_on' => '2026-01-01',
            ]);

        $property->refresh()->load('rooms.currentTenancy');
        $this->assertTrue($property->isPartiallyOccupied());
        $this->assertSame('Partially occupied', $property->occupancyLabel());
        $this->assertSame('1 of 2 rooms occupied', $property->occupancySummary());
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function propertyPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => '4 Room House',
            'property_type_id' => $this->propertyType->id,
            'rental_mode' => 'whole',
            'address_line_1' => '4 Room Street',
            'city' => 'Leeds',
            'postcode' => 'LS1 1AA',
            'country' => 'United Kingdom',
        ], $overrides);
    }

    private function createProperty(): Property
    {
        return Property::query()->create($this->propertyPayload());
    }

    private function createPerRoomProperty(): Property
    {
        $property = Property::query()->create($this->propertyPayload([
            'rental_mode' => 'per_room',
        ]));

        $property->rooms()->create(['name' => 'Room 1', 'sort_order' => 1]);
        $property->rooms()->create(['name' => 'Room 2', 'sort_order' => 2]);

        return $property->fresh(['rooms']);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createTenant(array $overrides = []): Tenant
    {
        return Tenant::query()->create(array_merge([
            'first_name' => 'Jane',
            'last_name' => 'Tenant',
            'mobile_number' => '07123456789',
            'email' => 'jane.tenant@example.com',
        ], $overrides));
    }

    private function makeAdmin(): Admin
    {
        $admin = Admin::query()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password',
            'is_active' => true,
        ]);
        $admin->profile()->create([]);

        return $admin;
    }
}
