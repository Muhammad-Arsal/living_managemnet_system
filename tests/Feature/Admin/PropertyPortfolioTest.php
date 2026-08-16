<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Tenancy;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PropertyPortfolioTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected PropertyType $propertyType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->makeAdmin();
        $this->propertyType = PropertyType::query()->create([
            'name' => 'Flat',
            'slug' => 'flat',
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    public function test_admin_can_create_property_type(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.settings.property-types.store'), [
                'name' => 'House',
                'is_active' => '1',
                'sort_order' => 2,
            ])
            ->assertRedirect(route('admin.settings.property-types.index'));

        $this->assertDatabaseHas('property_types', [
            'name' => 'House',
            'slug' => 'house',
            'is_active' => true,
        ]);
    }

    public function test_cannot_delete_property_type_in_use(): void
    {
        $this->createProperty();

        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.settings.property-types.destroy', $this->propertyType))
            ->assertRedirect();

        $this->assertDatabaseHas('property_types', ['id' => $this->propertyType->id]);
    }

    public function test_admin_can_create_tenant(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.tenants.store'), $this->tenantPayload())
            ->assertRedirect(route('admin.tenants.index'));

        $this->assertDatabaseHas('tenants', [
            'email' => 'jane.tenant@example.com',
            'postcode' => 'SW1A 1AA',
            'mobile_number' => '07123456789',
        ]);
    }

    public function test_new_tenant_is_past_until_assigned(): void
    {
        $tenant = $this->createTenant();

        $this->assertFalse($tenant->isCurrent());
        $this->assertSame('Past', $tenant->statusLabel());
    }

    public function test_admin_can_create_property_with_multiple_images(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.store'), $this->propertyPayload([
                'images' => [
                    UploadedFile::fake()->image('front.jpg'),
                    UploadedFile::fake()->image('kitchen.png'),
                ],
            ]))
            ->assertRedirect(route('admin.properties.index'));

        $property = Property::query()->first();
        $this->assertNotNull($property);
        $this->assertSame(2, $property->images()->count());
        Storage::disk('public')->assertExists($property->images->first()->path);
    }

    public function test_assigning_tenant_makes_tenant_current_and_property_occupied(): void
    {
        $tenant = $this->createTenant();
        $property = $this->createProperty();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.tenancies.store', $property), [
                'tenant_id' => $tenant->id,
                'started_on' => '2026-01-01',
            ])
            ->assertRedirect(route('admin.properties.edit', ['property' => $property, 'tab' => 'current']));

        $tenant->refresh();
        $property->refresh();

        $this->assertTrue($tenant->isCurrent());
        $this->assertTrue($property->isOccupied());
        $this->assertDatabaseHas('tenancies', [
            'tenant_id' => $tenant->id,
            'property_id' => $property->id,
            'active_tenant_id' => $tenant->id,
            'active_property_id' => $property->id,
            'ended_on' => null,
        ]);
    }

    public function test_cannot_assign_second_current_tenant_to_property(): void
    {
        $first = $this->createTenant(['email' => 'one@example.com', 'mobile_number' => '07111111111']);
        $second = $this->createTenant(['email' => 'two@example.com', 'mobile_number' => '07222222222']);
        $property = $this->createProperty();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.tenancies.store', $property), [
                'tenant_id' => $first->id,
                'started_on' => '2026-01-01',
            ])
            ->assertRedirect();

        $this->actingAs($this->admin, 'admin')
            ->from(route('admin.properties.edit', $property))
            ->post(route('admin.properties.tenancies.store', $property), [
                'tenant_id' => $second->id,
                'started_on' => '2026-02-01',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('tenant_id');

        $this->assertSame(1, Tenancy::query()->whereNull('ended_on')->count());
    }

    public function test_ending_tenancy_preserves_history_and_vacates_property(): void
    {
        $tenant = $this->createTenant();
        $property = $this->createProperty();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.tenancies.store', $property), [
                'tenant_id' => $tenant->id,
                'started_on' => '2026-01-01',
            ]);

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.properties.tenancies.end', $property), [
                'ended_on' => '2026-06-01',
            ])
            ->assertRedirect(route('admin.properties.edit', ['property' => $property, 'tab' => 'current']));

        $tenancy = Tenancy::query()->first();
        $this->assertNotNull($tenancy);
        $this->assertSame('2026-06-01', $tenancy->ended_on?->toDateString());
        $this->assertNull($tenancy->active_tenant_id);
        $this->assertNull($tenancy->active_property_id);

        $this->assertFalse($tenant->fresh()->isCurrent());
        $this->assertFalse($property->fresh()->isOccupied());
        $this->assertSame(1, $property->tenancies()->count());
    }

    public function test_cannot_delete_tenant_with_history(): void
    {
        $tenant = $this->createTenant();
        $property = $this->createProperty();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.tenancies.store', $property), [
                'tenant_id' => $tenant->id,
                'started_on' => '2026-01-01',
            ]);

        $this->actingAs($this->admin, 'admin')
            ->from(route('admin.tenants.index'))
            ->delete(route('admin.tenants.destroy', $tenant))
            ->assertRedirect();

        $this->assertDatabaseHas('tenants', ['id' => $tenant->id]);
    }

    public function test_guest_cannot_access_admin_tenants(): void
    {
        $this->get(route('admin.tenants.index'))->assertRedirect();
    }

    public function test_admin_can_assign_property_from_tenant_edit(): void
    {
        $tenant = $this->createTenant();
        $property = $this->createProperty();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.tenants.tenancies.store', $tenant), [
                'property_id' => $property->id,
                'started_on' => '2026-01-01',
            ])
            ->assertRedirect(route('admin.tenants.edit', ['tenant' => $tenant, 'tab' => 'current']));

        $this->assertTrue($tenant->fresh()->isCurrent());
        $this->assertTrue($property->fresh()->isOccupied());
        $this->assertDatabaseHas('tenancies', [
            'tenant_id' => $tenant->id,
            'property_id' => $property->id,
            'ended_on' => null,
        ]);
    }

    public function test_cannot_assign_occupied_property_from_tenant_edit(): void
    {
        $first = $this->createTenant(['email' => 'one@example.com', 'mobile_number' => '07111111111']);
        $second = $this->createTenant(['email' => 'two@example.com', 'mobile_number' => '07222222222']);
        $property = $this->createProperty();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.tenants.tenancies.store', $first), [
                'property_id' => $property->id,
                'started_on' => '2026-01-01',
            ])
            ->assertRedirect();

        $this->actingAs($this->admin, 'admin')
            ->from(route('admin.tenants.edit', $second))
            ->post(route('admin.tenants.tenancies.store', $second), [
                'property_id' => $property->id,
                'started_on' => '2026-02-01',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('tenant_id');

        $this->assertSame(1, Tenancy::query()->whereNull('ended_on')->count());
    }

    public function test_ending_tenancy_from_tenant_edit_preserves_history(): void
    {
        $tenant = $this->createTenant();
        $property = $this->createProperty();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.tenants.tenancies.store', $tenant), [
                'property_id' => $property->id,
                'started_on' => '2026-01-01',
            ]);

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.tenants.tenancies.end', $tenant), [
                'ended_on' => '2026-06-01',
            ])
            ->assertRedirect(route('admin.tenants.edit', ['tenant' => $tenant, 'tab' => 'current']));

        $tenancy = Tenancy::query()->first();
        $this->assertNotNull($tenancy);
        $this->assertSame('2026-06-01', $tenancy->ended_on?->toDateString());
        $this->assertFalse($tenant->fresh()->isCurrent());
        $this->assertFalse($property->fresh()->isOccupied());
        $this->assertSame(1, $tenant->tenancies()->count());
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function tenantPayload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Jane',
            'last_name' => 'Tenant',
            'mobile_number' => '07123456789',
            'email' => 'jane.tenant@example.com',
            'address_line_1' => '10 Downing Street',
            'address_line_2' => '',
            'address_line_3' => '',
            'city' => 'London',
            'county' => 'Greater London',
            'postcode' => 'sw1a1aa',
            'country' => 'United Kingdom',
        ], $overrides);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function propertyPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => '12 High Street — Flat 2',
            'property_type_id' => $this->propertyType->id,
            'address_line_1' => '12 High Street',
            'city' => 'Manchester',
            'postcode' => 'M1 1AE',
            'country' => 'United Kingdom',
        ], $overrides);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createTenant(array $overrides = []): Tenant
    {
        return Tenant::query()->create($this->tenantPayload(array_merge([
            'postcode' => 'SW1A 1AA',
        ], $overrides)));
    }

    private function createProperty(): Property
    {
        return Property::query()->create($this->propertyPayload());
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
