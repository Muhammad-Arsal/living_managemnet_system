<?php

namespace Tests\Feature\Admin;

use App\Enums\DocumentTemplateKey;
use App\Enums\SiteSettingKey;
use App\Models\Admin;
use App\Models\Document;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\SiteSetting;
use App\Models\Tenancy;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PropertyDocumentGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected PropertyType $propertyType;

    protected Property $property;

    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->admin = $this->makeAdmin();
        $this->propertyType = PropertyType::query()->create([
            'name' => 'Flat',
            'slug' => 'flat',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        SiteSetting::setValue(SiteSettingKey::SiteName->value, 'Rneyz Housing');
        SiteSetting::setValue(SiteSettingKey::Phone->value, '02400000000');

        $this->property = Property::query()->create([
            'property_type_id' => $this->propertyType->id,
            'rental_mode' => 'whole',
            'name' => 'Alpha House',
            'address_line_1' => '12 High Street',
            'city' => 'Coventry',
            'postcode' => 'CV1 1AA',
            'country' => 'United Kingdom',
        ]);

        $this->tenant = Tenant::query()->create([
            'first_name' => 'Jane',
            'last_name' => 'Resident',
            'mobile_number' => '07123456789',
            'email' => 'jane.resident@example.com',
        ]);

        Tenancy::query()->create([
            'property_id' => $this->property->id,
            'tenant_id' => $this->tenant->id,
            'started_on' => now()->subMonths(2)->toDateString(),
            'ended_on' => null,
        ]);
    }

    public function test_generate_page_lists_all_templates_and_prefills_tenant_fields(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.properties.documents.generate', [
                'property' => $this->property,
                'template' => DocumentTemplateKey::WarningLetter->value,
                'tenant_id' => $this->tenant->id,
            ]));

        $response->assertOk();
        $response->assertSee('Warning Letter (RH-17)');
        $response->assertSee('Jane Resident');
        $response->assertSee('12 High Street');
        $response->assertSee('CV1 1AA');
        $response->assertSee('02400000000');
    }

    #[DataProvider('templateKeysProvider')]
    public function test_each_template_can_be_previewed(string $templateKey): void
    {
        $template = DocumentTemplateKey::from($templateKey);
        $payload = $this->validPayload($template);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.documents.generate.preview', $this->property), $payload);

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/html; charset=UTF-8');
        $response->assertSee('Jane Resident', false);
        $response->assertSee('12 High Street', false);
    }

    #[DataProvider('templateKeysProvider')]
    public function test_each_template_can_be_generated_and_saved(string $templateKey): void
    {
        $template = DocumentTemplateKey::from($templateKey);
        $payload = $this->validPayload($template);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.properties.documents.generate.store', $this->property), $payload);

        $response->assertRedirect(route('admin.properties.edit', [
            'property' => $this->property,
            'tab' => 'documents',
        ]));

        $document = Document::query()->where('documentable_id', $this->property->id)->first();
        $this->assertNotNull($document);
        $this->assertSame('application/pdf', $document->mime_type);
        $this->assertStringEndsWith('.pdf', $document->original_name);
        $this->assertStringContainsString($template->filenamePrefix(), $document->original_name);
        Storage::disk('local')->assertExists($document->path);
        $this->assertGreaterThan(500, $document->size);
    }

    public function test_generation_requires_missing_manual_fields(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.properties.documents.generate', [
                'property' => $this->property,
                'template' => DocumentTemplateKey::ServiceChargeNotice->value,
                'tenant_id' => $this->tenant->id,
            ]))
            ->post(route('admin.properties.documents.generate.store', $this->property), [
                'template' => DocumentTemplateKey::ServiceChargeNotice->value,
                'tenant_id' => $this->tenant->id,
                'fields' => [
                    'provider_name' => 'Rneyz Housing',
                    'date_issued' => now()->toDateString(),
                    'tenant_name' => 'Jane Resident',
                    'property_address' => '12 High Street, Coventry, CV1 1AA',
                    'dear_name' => 'Jane',
                    'service_charge_amount' => '',
                    'payment_day' => '',
                    'first_payment_due' => '',
                    'account_name' => '',
                    'sort_code' => '',
                    'account_number' => '',
                ],
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors([
            'fields.service_charge_amount',
            'fields.payment_day',
            'fields.first_payment_due',
            'fields.account_name',
            'fields.sort_code',
            'fields.account_number',
        ]);
        $this->assertSame(0, Document::query()->count());
    }

    public function test_guest_cannot_generate_documents(): void
    {
        $this->get(route('admin.properties.documents.generate', $this->property))
            ->assertRedirect();

        $this->post(route('admin.properties.documents.generate.store', $this->property), [])
            ->assertRedirect();
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function templateKeysProvider(): array
    {
        $cases = [];

        foreach (DocumentTemplateKey::cases() as $template) {
            $cases[$template->value] = [$template->value];
        }

        return $cases;
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(DocumentTemplateKey $template): array
    {
        $fields = match ($template) {
            DocumentTemplateKey::ServiceChargeNotice => [
                'provider_name' => 'Rneyz Housing',
                'date_issued' => now()->toDateString(),
                'tenant_name' => 'Jane Resident',
                'property_address' => '12 High Street, Coventry, CV1 1AA, United Kingdom',
                'dear_name' => 'Jane',
                'service_charge_amount' => '35.00',
                'payment_day' => 'Monday',
                'first_payment_due' => now()->addWeek()->toDateString(),
                'account_name' => 'Rneyz Housing',
                'sort_code' => '00-00-00',
                'account_number' => '12345678',
            ],
            DocumentTemplateKey::MoveOnPlan => [
                'provider_name' => 'Rneyz Housing',
                'property_address' => '12 High Street, Coventry, CV1 1AA, United Kingdom',
                'resident_name' => 'Jane Resident',
                'date_of_birth' => '1990-01-01',
                'ni_number' => 'AB123456C',
                'support_worker' => 'Sam Support',
                'plan_started_on' => now()->toDateString(),
                'review_date' => now()->addMonths(3)->toDateString(),
                'target_move_on_date' => now()->addYear()->toDateString(),
                'accommodation_type' => 'Shared accommodation',
                'moved_in_on' => now()->subMonths(2)->toDateString(),
                'length_of_stay' => '2 months',
                'reason_for_placement' => 'Supported housing placement',
                'long_term_goal' => 'Independent social housing',
                'housing_goal_type' => 'Social Housing',
                'target_timescale' => '12 months',
                'objectives_notes' => 'Maintain tenancy and build budgeting skills.',
                'barriers' => 'Limited life skills',
                'support_provided' => 'Weekly support sessions',
                'support_frequency' => 'Weekly',
            ],
            DocumentTemplateKey::WarningLetter => [
                'resident_name' => 'Jane Resident',
                'address' => '12 High Street, Coventry, CV1 1AA, United Kingdom',
                'postcode' => 'CV1 1AA',
                'date' => now()->toDateString(),
                'dear_name' => 'Jane Resident',
                'warning_reasons' => 'Missed agreed support sessions.',
                'meeting_datetime' => 'Monday 20 October 2026 at 10:00',
                'meeting_location' => 'Alpha House office',
                'contact_phone' => '02400000000',
                'support_worker_name' => 'Sam Support',
                'support_worker_title' => 'Support Worker',
            ],
            DocumentTemplateKey::FinalWarning => [
                'resident_name' => 'Jane Resident',
                'address' => '12 High Street, Coventry, CV1 1AA, United Kingdom',
                'postcode' => 'CV1 1AA',
                'date' => now()->toDateString(),
                'dear_name' => 'Jane Resident',
                'missed_meeting_on' => 'Monday 6 October 2026',
                'meeting_datetime' => 'Monday 20 October 2026 at 10:00',
                'meeting_location' => 'Alpha House office',
                'contact_phone' => '02400000000',
                'support_worker_name' => 'Sam Support',
                'support_worker_title' => 'Support Worker',
            ],
            DocumentTemplateKey::RiskAssessment => [
                'resident_name' => 'Jane Resident',
                'address' => '12 High Street, Coventry, CV1 1AA, United Kingdom',
                'telephone' => '07123456789',
                'email' => 'jane.resident@example.com',
                'support_worker' => 'Sam Support',
                'assessment_date' => now()->toDateString(),
                'provider_name' => 'Rneyz Housing',
                'review_3_month' => now()->addMonths(3)->toDateString(),
                'review_6_month' => now()->addMonths(6)->toDateString(),
                'review_9_month' => now()->addMonths(9)->toDateString(),
                'review_12_month' => now()->addYear()->toDateString(),
                'risk_violence' => 'Initial score: Low',
                'risk_harm' => 'Initial score: Low',
                'risk_self_neglect' => 'Initial score: Medium',
                'risk_mental' => 'Initial score: Medium',
                'risk_physical' => 'Initial score: Low',
                'risk_substance' => 'Initial score: Low',
                'risk_financial' => 'Initial score: Medium',
                'risk_offending' => 'Initial score: Low',
                'risk_learning' => 'Initial score: Low',
                'risk_other' => '',
            ],
            DocumentTemplateKey::ReferralForm => [
                'organisation' => 'Rneyz Housing',
                'full_name' => 'Jane Resident',
                'dob_age' => '01/01/1990 (36)',
                'financial_status' => 'Receiving benefits',
                'ni_number' => 'AB123456C',
                'telephone' => '07123456789',
                'gender' => 'Female',
                'email' => 'jane.resident@example.com',
                'client_number' => 'C-100',
                'previous_contact' => 'None',
                'current_address' => '12 High Street, Coventry, CV1 1AA, United Kingdom',
                'landlord_contact' => 'Rneyz Housing',
                'reason_support' => 'Needs supported accommodation',
                'current_accommodation' => 'Supported Housing',
                'current_circumstances' => 'Support to Sustain Tenancy',
                'support_needs' => 'Housing, Benefits',
                'translation_language' => '',
                'health_issues' => 'None declared',
                'gp_cpn' => 'Dr Smith',
                'gp_telephone' => '02411111111',
                'gp_address' => 'City Surgery',
                'medication' => 'None',
                'risk_factors' => 'None known',
                'other_agencies' => 'None',
                'referring_agency' => 'Local Authority',
                'referrer_name' => 'Alex Referrer',
                'referrer_phone' => '02422222222',
                'referrer_email' => 'alex@example.com',
                'application_date' => now()->toDateString(),
                'referral_date' => now()->toDateString(),
                'safeguarding' => 'None',
                'emergency_name' => 'John Resident',
                'emergency_relationship' => 'Brother',
                'emergency_telephone' => '07999999999',
                'ethnicity' => '',
                'religion' => '',
                'disability' => '',
                'preferred_language' => 'English',
            ],
        };

        return [
            'template' => $template->value,
            'tenant_id' => $this->tenant->id,
            'fields' => $fields,
        ];
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
