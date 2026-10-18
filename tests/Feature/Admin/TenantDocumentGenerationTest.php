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

class TenantDocumentGenerationTest extends TestCase
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

    public function test_generate_lives_on_tenant_documents_not_property(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.tenants.documents.generate', $this->tenant))
            ->assertOk()
            ->assertSee('Generate Document')
            ->assertSee('Jane Resident');

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.properties.edit', ['property' => $this->property, 'tab' => 'documents']))
            ->assertOk()
            ->assertDontSee('Generate from template');

        $this->assertFalse(
            \Illuminate\Support\Facades\Route::has('admin.properties.documents.generate')
        );
    }

    public function test_referral_form_uses_checkbox_options_not_textarea_summary(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.tenants.documents.generate', [
                'tenant' => $this->tenant,
                'template' => DocumentTemplateKey::ReferralForm->value,
            ]));

        $response->assertOk();
        $response->assertSee('2. Current accommodation');
        $response->assertSee('Rough Sleeping');
        $response->assertSee('Supported Housing');
        $response->assertSee('Threatened with Homelessness');
        $response->assertSee('Mental Health');
        $response->assertDontSee('Current accommodation (summary)');
        $response->assertSee('name="fields[current_accommodation][]"', false);
        $response->assertSee('name="fields[current_circumstances][]"', false);
        $response->assertSee('name="fields[support_needs][]"', false);
    }

    public function test_move_on_plan_includes_review_record_and_final_outcome_controls(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.tenants.documents.generate', [
                'tenant' => $this->tenant,
                'template' => DocumentTemplateKey::MoveOnPlan->value,
            ]));

        $response->assertOk();
        $response->assertSee('Review record 1 — date');
        $response->assertSee('Date resident moved out');
        $response->assertSee('Move-on destination');
        $response->assertSee('1. Tenancy sustainment — target date');
        $response->assertSee('1. Tenancy sustainment — completed');
        $response->assertSee('name="fields[goal_tenancy][]"', false);
    }

    public function test_risk_assessment_uses_five_column_score_rows(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.tenants.documents.generate', [
                'tenant' => $this->tenant,
                'template' => DocumentTemplateKey::RiskAssessment->value,
            ]));

        $response->assertOk();
        $response->assertSee('1. Risk of violence & aggression');
        $response->assertSee('Initial Score');
        $response->assertSee('3 Month Review');
        $response->assertSee('12 Month Review');
        $response->assertSee('name="fields[risk_violence][initial]"', false);
        $response->assertSee('name="fields[risk_violence][m12]"', false);
        $response->assertSee('10. Other (please specify)');
        $response->assertSee('11. Other (please specify)');
    }

    public function test_new_rh_templates_render_key_controls_on_form(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.tenants.documents.generate', [
                'tenant' => $this->tenant,
                'template' => DocumentTemplateKey::IncidentReport->value,
            ]))
            ->assertOk()
            ->assertSee('Incident Form (RH-15)')
            ->assertSee('Police')
            ->assertSee('Ambulance')
            ->assertSee('Fire')
            ->assertSee('name="fields[emergency_services][]"', false);

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.tenants.documents.generate', [
                'tenant' => $this->tenant,
                'template' => DocumentTemplateKey::MissedMeetingLetter->value,
            ]))
            ->assertOk()
            ->assertSee('Missed Meeting Letter (RH-16)')
            ->assertSee('We were due to meet on');

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.tenants.documents.generate', [
                'tenant' => $this->tenant,
                'template' => DocumentTemplateKey::KnowingYourRights->value,
            ]))
            ->assertOk()
            ->assertSee('Knowing Your Rights (RH-19)')
            ->assertSee('I understand I have the right to live in a clean, safe and secure home')
            ->assertSee('name="fields[statements][]"', false);

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.tenants.documents.generate', [
                'tenant' => $this->tenant,
                'template' => DocumentTemplateKey::ResidentExitForm->value,
            ]))
            ->assertOk()
            ->assertSee('Resident Exit Form (RH-20)')
            ->assertSee('Move onto independent living')
            ->assertSee('Unknown / lost contact')
            ->assertSee('name="fields[leave_reasons][]"', false);
    }

    public function test_risk_assessment_preview_renders_score_grid_cells(): void
    {
        $payload = $this->validPayload(DocumentTemplateKey::RiskAssessment);
        $payload['fields']['risk_violence'] = [
            'initial' => '2',
            'm3' => '1',
            'm6' => '',
            'm9' => '',
            'm12' => '',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.tenants.documents.generate.preview', $this->tenant), $payload);

        $response->assertOk();
        $response->assertSee('Initial Score', false);
        $response->assertSee('>2</td>', false);
        $response->assertSee('>1</td>', false);
        $response->assertSee('1. Risk of violence &amp; aggression', false);
    }

    public function test_preview_shows_checked_accommodation_options(): void
    {
        $payload = $this->validPayload(DocumentTemplateKey::ReferralForm);
        $payload['fields']['current_accommodation'] = ['Supported Housing', 'Other'];
        $payload['fields']['current_accommodation_further'] = 'Needs ground floor';

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.tenants.documents.generate.preview', $this->tenant), $payload);

        $response->assertOk();
        $response->assertSee('☑ Supported Housing', false);
        $response->assertSee('☐ Rough Sleeping', false);
        $response->assertSee('Needs ground floor', false);
    }

    #[DataProvider('templateKeysProvider')]
    public function test_each_template_can_be_previewed(string $templateKey): void
    {
        $template = DocumentTemplateKey::from($templateKey);
        $payload = $this->validPayload($template);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.tenants.documents.generate.preview', $this->tenant), $payload);

        $response->assertOk();
        $response->assertSee('Jane Resident', false);

        if ($template === DocumentTemplateKey::ResidentExitForm) {
            $response->assertSee('1 New Street, Coventry', false);
            $response->assertSee('Move onto independent living', false);
        } else {
            $response->assertSee('12 High Street', false);
        }
    }

    #[DataProvider('templateKeysProvider')]
    public function test_each_template_can_be_generated_and_saved(string $templateKey): void
    {
        $template = DocumentTemplateKey::from($templateKey);
        $payload = $this->validPayload($template);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.tenants.documents.generate.store', $this->tenant), $payload);

        $response->assertRedirect(route('admin.tenants.edit', [
            'tenant' => $this->tenant,
            'tab' => 'documents',
        ]));

        $document = Document::query()->where('documentable_id', $this->tenant->id)->first();
        $this->assertNotNull($document);
        $this->assertSame('application/pdf', $document->mime_type);
        $this->assertStringContainsString($template->filenamePrefix(), $document->original_name);
        Storage::disk('local')->assertExists($document->path);
        $this->assertGreaterThan(500, $document->size);
    }

    public function test_generation_requires_missing_manual_fields(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.tenants.documents.generate', [
                'tenant' => $this->tenant,
                'template' => DocumentTemplateKey::ServiceChargeNotice->value,
            ]))
            ->post(route('admin.tenants.documents.generate.store', $this->tenant), [
                'template' => DocumentTemplateKey::ServiceChargeNotice->value,
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
        $this->get(route('admin.tenants.documents.generate', $this->tenant))
            ->assertRedirect();

        $this->post(route('admin.tenants.documents.generate.store', $this->tenant), [])
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
                'accommodation_type_other' => '',
                'moved_in_on' => now()->subMonths(2)->toDateString(),
                'length_of_stay' => '2 months',
                'reason_for_placement' => 'Supported housing placement',
                'housing_goal_type' => 'Social Housing',
                'housing_goal_type_other' => '',
                'long_term_goal' => 'Independent social housing',
                'target_timescale' => '12 months',
                'target_timescale_other' => '',
                'goal_tenancy' => ['Maintain tenancy successfully', 'Pay rent on time'],
                'goal_tenancy_actions' => 'Weekly budgeting support',
                'goal_tenancy_target_date' => now()->addMonths(1)->toDateString(),
                'goal_tenancy_completed' => 'No',
                'goal_living' => ['Budgeting and money management'],
                'goal_living_actions' => '',
                'goal_living_target_date' => '',
                'goal_living_completed' => 'No',
                'goal_health' => ['Register with GP'],
                'goal_health_actions' => '',
                'goal_health_target_date' => '',
                'goal_health_completed' => 'Yes',
                'goal_employment' => ['Seek employment'],
                'goal_employment_actions' => '',
                'goal_employment_target_date' => '',
                'goal_employment_completed' => 'No',
                'goal_financial' => ['Maintain benefits'],
                'goal_financial_actions' => '',
                'goal_financial_target_date' => '',
                'goal_financial_completed' => 'No',
                'goal_community' => ['Access community services'],
                'goal_community_actions' => '',
                'goal_community_target_date' => '',
                'goal_community_completed' => 'No',
                'homefinder_registered' => 'Yes',
                'homefinder_date' => now()->toDateString(),
                'homefinder_banding' => 'Band 2',
                'homefinder_reference' => 'HF-123',
                'prs_actions' => ['Affordability assessed'],
                'prs_notes' => '',
                'barriers' => ['Limited life skills'],
                'barriers_other' => '',
                'barriers_actions' => 'Life skills sessions',
                'support_provided' => ['Weekly support sessions', 'Housing advice'],
                'support_frequency' => 'Weekly',
                'support_frequency_other' => '',
                'review_1_date' => now()->toDateString(),
                'review_1_progress' => 'Settling in well',
                'review_1_actions' => 'Continue weekly support',
                'review_1_initials' => 'SS',
                'review_2_date' => '',
                'review_2_progress' => '',
                'review_2_actions' => '',
                'review_2_initials' => '',
                'review_3_date' => '',
                'review_3_progress' => '',
                'review_3_actions' => '',
                'review_3_initials' => '',
                'review_4_date' => '',
                'review_4_progress' => '',
                'review_4_actions' => '',
                'review_4_initials' => '',
                'moved_out_on' => '',
                'move_on_destination' => 'Social Housing',
                'move_on_destination_other' => '',
                'move_on_address' => '',
                'outcome_summary' => '',
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
                'risk_violence' => ['initial' => 'Low', 'm3' => '', 'm6' => '', 'm9' => '', 'm12' => ''],
                'risk_harm' => ['initial' => 'Low', 'm3' => '', 'm6' => '', 'm9' => '', 'm12' => ''],
                'risk_self_neglect' => ['initial' => 'Medium', 'm3' => '', 'm6' => '', 'm9' => '', 'm12' => ''],
                'risk_mental' => ['initial' => 'Medium', 'm3' => '', 'm6' => '', 'm9' => '', 'm12' => ''],
                'risk_physical' => ['initial' => 'Low', 'm3' => '', 'm6' => '', 'm9' => '', 'm12' => ''],
                'risk_substance' => ['initial' => 'Low', 'm3' => '', 'm6' => '', 'm9' => '', 'm12' => ''],
                'risk_financial' => ['initial' => 'Medium', 'm3' => '', 'm6' => '', 'm9' => '', 'm12' => ''],
                'risk_offending' => ['initial' => 'Low', 'm3' => '', 'm6' => '', 'm9' => '', 'm12' => ''],
                'risk_learning' => ['initial' => 'Low', 'm3' => '', 'm6' => '', 'm9' => '', 'm12' => ''],
                'risk_other_1_specify' => '',
                'risk_other_1' => ['initial' => '', 'm3' => '', 'm6' => '', 'm9' => '', 'm12' => ''],
                'risk_other_2_specify' => '',
                'risk_other_2' => ['initial' => '', 'm3' => '', 'm6' => '', 'm9' => '', 'm12' => ''],
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
                'current_accommodation' => ['Supported Housing'],
                'current_accommodation_further' => '',
                'current_circumstances' => ['Support to Sustain Tenancy'],
                'current_circumstances_further' => '',
                'support_needs' => ['Housing', 'Benefits'],
                'support_needs_further' => '',
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
                'consent' => ['gdpr', 'rent', 'privacy', 'sharing'],
            ],
            DocumentTemplateKey::IncidentReport => [
                'provider_name' => 'Rneyz Housing',
                'completed_by' => 'Sam Support',
                'date_of_completion' => now()->toDateString(),
                'email' => 'sam@example.com',
                'contact_number' => '02400000000',
                'incident_location' => '12 High Street, Coventry, CV1 1AA, United Kingdom',
                'incident_details' => 'Resident fell in the hallway at 10:00. First aid administered.',
                'persons_involved' => 'Jane Resident; Sam Support 07123456789',
                'emergency_services' => ['Ambulance'],
                'emergency_details' => 'Ambulance ref AMB-123',
                'follow_up_details' => 'Review risk assessment within 48 hours',
                'other_comments' => 'None',
            ],
            DocumentTemplateKey::MissedMeetingLetter => [
                'resident_name' => 'Jane Resident',
                'address' => '12 High Street, Coventry, CV1 1AA, United Kingdom',
                'postcode' => 'CV1 1AA',
                'date' => now()->toDateString(),
                'dear_name' => 'Jane Resident',
                'missed_meeting_on' => 'Monday 6 October 2026',
                'new_appointment_on' => 'Monday 20 October 2026 at 10:00',
                'meeting_location' => 'Alpha House office',
                'contact_phone' => '02400000000',
                'support_worker_name' => 'Sam Support',
                'support_worker_title' => 'Support Worker',
            ],
            DocumentTemplateKey::KnowingYourRights => [
                'provider_name' => 'Rneyz Housing',
                'resident_name' => 'Jane Resident',
                'address' => '12 High Street, Coventry, CV1 1AA, United Kingdom',
                'statements' => [
                    'clean_safe_home',
                    'landlord',
                    'engage_support',
                    'fire',
                    'licence',
                    'support_plan',
                    'risk_assessment',
                    'pay_service_charge',
                    'service_charge_for',
                    'complaint',
                    'contact_support',
                    'appeal',
                ],
                'support_worker' => 'Sam Support',
                'date_of_completion' => now()->toDateString(),
            ],
            DocumentTemplateKey::ResidentExitForm => [
                'resident_name' => 'Jane Resident',
                'completed_by' => 'Sam Support',
                'date_of_completion' => now()->toDateString(),
                'leave_reasons' => ['Move onto independent living'],
                'leave_reason_other' => '',
                'new_address' => '1 New Street, Coventry',
                'housing_benefit_cancelled' => 'Yes',
                'left_belongings' => 'No',
                'belongings_list' => '',
                'next_of_kin_contacted' => 'N/A',
                'follow_up_required' => 'No',
                'follow_up_details' => '',
            ],
        };

        return [
            'template' => $template->value,
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
