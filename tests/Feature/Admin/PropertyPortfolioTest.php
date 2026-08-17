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
            'mobile_number' => '07123456789',
        ]);
        $this->assertDatabaseMissing('tenants', [
            'email' => 'jane.tenant@example.com',
            'address_line_1' => '10 Downing Street',
        ]);
    }

    public function test_admin_can_create_tenant_with_multiple_documents(): void
    {
        Storage::fake('local');

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.tenants.store'), $this->tenantPayload([
                'documents' => [
                    UploadedFile::fake()->create('id.pdf', 120, 'application/pdf'),
                    UploadedFile::fake()->create('contract.docx', 80, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
                ],
            ]))
            ->assertRedirect(route('admin.tenants.index'));

        $tenant = Tenant::query()->first();
        $this->assertNotNull($tenant);
        $this->assertSame(2, $tenant->documents()->count());
        Storage::disk('local')->assertExists($tenant->documents->first()->path);
    }

    public function test_admin_can_update_tenant_and_upload_additional_documents(): void
    {
        Storage::fake('local');

        $tenant = $this->createTenant();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.tenants.documents.store', $tenant), [
                'documents' => [
                    UploadedFile::fake()->create('passport.pdf', 90, 'application/pdf'),
                ],
            ])
            ->assertRedirect(route('admin.tenants.edit', ['tenant' => $tenant, 'tab' => 'documents']));

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.tenants.update', $tenant), [
                'first_name' => 'Janet',
                'last_name' => 'Tenant',
                'mobile_number' => '07123456789',
                'email' => 'jane.tenant@example.com',
            ])
            ->assertRedirect(route('admin.tenants.edit', ['tenant' => $tenant, 'tab' => 'overview']));

        $tenant->refresh();
        $this->assertSame('Janet', $tenant->first_name);
        $this->assertSame(1, $tenant->documents()->count());
        Storage::disk('local')->assertExists($tenant->documents->first()->path);

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.tenants.documents.download', [$tenant, $tenant->documents->first()]))
            ->assertOk();
    }

    public function test_tenant_address_is_derived_from_assigned_property(): void
    {
        $tenant = $this->createTenant();
        $property = $this->createProperty();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.tenancies.store', $property), [
                'tenant_id' => $tenant->id,
                'started_on' => '2026-01-01',
            ])
            ->assertRedirect();

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.tenants.edit', $tenant))
            ->assertOk()
            ->assertDontSee('name="address_line_1"', false)
            ->assertSee($property->formattedAddress())
            ->assertSee('lmsConfirmModal', false)
            ->assertDontSee('return confirm(', false);
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

    public function test_admin_can_create_property_with_documents(): void
    {
        Storage::fake('local');

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.store'), $this->propertyPayload([
                'documents' => [
                    UploadedFile::fake()->create('epc.pdf', 200, 'application/pdf'),
                    UploadedFile::fake()->create('floorplan.png', 40, 'image/png'),
                ],
            ]))
            ->assertRedirect(route('admin.properties.index'));

        $property = Property::query()->first();
        $this->assertNotNull($property);
        $this->assertSame(2, $property->documents()->count());
        Storage::disk('local')->assertExists($property->documents->first()->path);

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.properties.documents.download', [$property, $property->documents->first()]))
            ->assertOk();
    }

    public function test_admin_can_update_property_without_losing_images_or_relationships(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.store'), $this->propertyPayload([
                'images' => [
                    UploadedFile::fake()->image('front.jpg'),
                ],
                'documents' => [
                    UploadedFile::fake()->create('lease.pdf', 150, 'application/pdf'),
                ],
            ]))
            ->assertRedirect(route('admin.properties.index'));

        $property = Property::query()->first();
        $this->assertNotNull($property);
        $existingImagePath = $property->images->first()->path;
        $existingDocumentPath = $property->documents->first()->path;
        $tenant = $this->createTenant();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.tenancies.store', $property), [
                'tenant_id' => $tenant->id,
                'started_on' => '2026-01-01',
            ])
            ->assertRedirect();

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.properties.update', $property), $this->propertyPayload([
                'name' => '12 High Street — Updated',
            ]))
            ->assertRedirect(route('admin.properties.edit', ['property' => $property, 'tab' => 'overview']))
            ->assertSessionHasNoErrors();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.images.store', $property), [
                'images' => [
                    UploadedFile::fake()->image('garden.jpg'),
                ],
            ])
            ->assertRedirect(route('admin.properties.edit', ['property' => $property, 'tab' => 'images']));

        $property->refresh()->load(['images', 'documents', 'currentTenancy']);
        $this->assertSame('12 High Street — Updated', $property->name);
        $this->assertSame(2, $property->images()->count());
        $this->assertTrue($property->images->contains('path', $existingImagePath));
        $this->assertSame(1, $property->documents()->count());
        $this->assertSame($existingDocumentPath, $property->documents->first()->path);
        $this->assertTrue($property->isOccupied());
        $this->assertSame($tenant->id, $property->currentTenancy?->tenant_id);
        Storage::disk('public')->assertExists($existingImagePath);
        Storage::disk('local')->assertExists($existingDocumentPath);
    }

    public function test_admin_can_upload_additional_property_documents_on_edit(): void
    {
        Storage::fake('local');
        $property = $this->createProperty();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.documents.store', $property), [
                'documents' => [
                    UploadedFile::fake()->create('gas-cert.pdf', 90, 'application/pdf'),
                ],
            ])
            ->assertRedirect(route('admin.properties.edit', ['property' => $property, 'tab' => 'documents']));

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.documents.store', $property), [
                'documents' => [
                    UploadedFile::fake()->create('insurance.pdf', 70, 'application/pdf'),
                ],
            ])
            ->assertRedirect(route('admin.properties.edit', ['property' => $property, 'tab' => 'documents']));

        $this->assertSame(2, $property->documents()->count());
    }

    public function test_admin_can_delete_a_property_document_without_removing_others(): void
    {
        Storage::fake('local');
        $property = $this->createProperty();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.documents.store', $property), [
                'documents' => [
                    UploadedFile::fake()->create('keep.pdf', 40, 'application/pdf'),
                    UploadedFile::fake()->create('remove.pdf', 40, 'application/pdf'),
                ],
            ]);

        $keep = $property->documents()->where('original_name', 'keep.pdf')->first();
        $remove = $property->documents()->where('original_name', 'remove.pdf')->first();
        $this->assertNotNull($keep);
        $this->assertNotNull($remove);

        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.properties.documents.destroy', [$property, $remove]))
            ->assertRedirect(route('admin.properties.edit', ['property' => $property, 'tab' => 'documents']));

        $this->assertSame(1, $property->documents()->count());
        $this->assertDatabaseHas('documents', ['id' => $keep->id]);
        $this->assertDatabaseMissing('documents', ['id' => $remove->id]);
        Storage::disk('local')->assertMissing($remove->path);
        Storage::disk('local')->assertExists($keep->path);
    }

    public function test_admin_can_delete_a_tenant_document(): void
    {
        Storage::fake('local');
        $tenant = $this->createTenant();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.tenants.documents.store', $tenant), [
                'documents' => [
                    UploadedFile::fake()->create('passport.pdf', 90, 'application/pdf'),
                ],
            ]);

        $document = $tenant->documents()->first();
        $this->assertNotNull($document);

        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.tenants.documents.destroy', [$tenant, $document]))
            ->assertRedirect(route('admin.tenants.edit', ['tenant' => $tenant, 'tab' => 'documents']));

        $this->assertSame(0, $tenant->documents()->count());
        Storage::disk('local')->assertMissing($document->path);
    }

    public function test_property_edit_does_not_nest_image_delete_forms(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.store'), $this->propertyPayload([
                'images' => [
                    UploadedFile::fake()->image('front.jpg'),
                ],
            ]));

        $property = Property::query()->first();
        $this->assertNotNull($property);

        $html = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.properties.edit', ['property' => $property, 'tab' => 'images']))
            ->assertOk()
            ->assertSee('property-images', false)
            ->assertSee($property->images->first()->original_name)
            ->assertSee('lmsConfirmModal', false)
            ->assertDontSee('return confirm(', false)
            ->getContent();

        $this->assertStringContainsString(route('admin.properties.images.destroy', [$property, $property->images->first()]), $html);

        $dom = new \DOMDocument;
        @$dom->loadHTML($html);
        foreach ($dom->getElementsByTagName('form') as $form) {
            $this->assertSame(0, $form->getElementsByTagName('form')->length);
        }
    }

    public function test_admin_can_delete_a_property_image_from_the_images_tab(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.store'), $this->propertyPayload([
                'images' => [
                    UploadedFile::fake()->image('front.jpg'),
                    UploadedFile::fake()->image('kitchen.png'),
                ],
            ]));

        $property = Property::query()->first();
        $this->assertNotNull($property);
        $image = $property->images->first();

        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.properties.images.destroy', [$property, $image]))
            ->assertRedirect(route('admin.properties.edit', ['property' => $property, 'tab' => 'images']));

        $this->assertSame(1, $property->images()->count());
        Storage::disk('public')->assertMissing($image->path);
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

    public function test_upcoming_tenancy_can_be_ended_on_or_after_start_date(): void
    {
        $tenant = $this->createTenant();
        $property = $this->createProperty();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.tenancies.store', $property), [
                'tenant_id' => $tenant->id,
                'started_on' => '2026-10-12',
            ])
            ->assertRedirect();

        $this->actingAs($this->admin, 'admin')
            ->from(route('admin.properties.edit', ['property' => $property, 'tab' => 'current']))
            ->put(route('admin.properties.tenancies.end', $property), [
                'ended_on' => '2026-08-16',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('ended_on');

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.properties.tenancies.end', $property), [
                'ended_on' => '2026-10-16',
            ])
            ->assertRedirect(route('admin.properties.edit', ['property' => $property, 'tab' => 'current']))
            ->assertSessionHasNoErrors();

        $this->assertSame('2026-10-16', Tenancy::query()->first()?->ended_on?->toDateString());
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
        return Tenant::query()->create($this->tenantPayload($overrides));
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
