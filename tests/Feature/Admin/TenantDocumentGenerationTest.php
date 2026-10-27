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
        $response->assertSee('Review record 1 date');
        $response->assertSee('Date resident moved out');
        $response->assertSee('Move-on destination');
        $response->assertSee('1. Tenancy sustainment target date');
        $response->assertSee('1. Tenancy sustainment completed');
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
        $response->assertSee('Understanding risk factors, triggers and how to reduce the risk.', false);
        $response->assertSee('Are there any ways we can minimize the risk? (Risk Management Plan)', false);
        $response->assertSee('This risk assessment will be reviewed every 3 months at your Quarterly Review support session.', false);
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

        if ($template === DocumentTemplateKey::InitialNeedsAssessment) {
            $response->assertSee('INITIAL NEEDS ASSESSMENT (RH-1)', false);
            $response->assertSee('Jane', false);
            $response->assertSee('Resident', false);
            $response->assertSee('12 High Street', false);
        } elseif ($template === DocumentTemplateKey::ResidentExitForm) {
            $response->assertSee('Jane Resident', false);
            $response->assertSee('1 New Street, Coventry', false);
            $response->assertSee('Move onto independent living', false);
        } elseif ($template === DocumentTemplateKey::StaffRiskAssessment) {
            $response->assertSee('Jane Resident', false);
            $response->assertSee('Risk Assessment for Staff Eyes Only (RH-10)', false);
            $response->assertSee('Risk Management Plan', false);
        } elseif ($template === DocumentTemplateKey::SupportPlanSchedule) {
            $response->assertSee('A. SUPPORT PLAN', false);
            $response->assertSee('B. SUPPORT SCHEDULE', false);
            $response->assertSee('Keywork sessions', false);
        } else {
            $response->assertSee('Jane Resident', false);
            $response->assertSee('12 High Street', false);
        }
    }

    public function test_templates_are_ordered_by_rh_number(): void
    {
        $labels = array_map(
            static fn (DocumentTemplateKey $key) => $key->label(),
            DocumentTemplateKey::cases()
        );

        $this->assertSame([
            'Initial Needs Assessment (RH-1)',
            'Excluded License Agreement (RH-2)',
            'Housing Benefit Authorisation Form (RH-4)',
            'Nil Income Form (RH-6)',
            'Your Risk Assessment (RH-9)',
            'Risk Assessment for Staff Eyes Only (RH-10)',
            'Your Support Plan (RH-11)',
            'Support Plan Schedule',
            'Resident Review Form (RH-12)',
            'Incident Form (RH-15)',
            'Missed Meeting Letter (RH-16)',
            'Warning Letter (RH-17)',
            'Final Warning for Non-Engagement (RH-18)',
            'Knowing Your Rights (RH-19)',
            'Resident Exit Form (RH-20)',
            'List of Inventory (RH-21)',
            'ID Check List (RH-22)',
            'Referral & Support Assessment Form',
            'Resident Move-On Plan',
            'Weekly Service Charge Notice',
        ], $labels);
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
                'detail_1_risk' => 'Violence & aggression',
                'detail_1_score' => '2',
                'detail_1_why' => 'Historical incident only',
                'detail_1_triggers' => 'Conflict with peers',
                'detail_1_plan' => 'De-escalation support',
                'detail_2_risk' => '',
                'detail_2_score' => '',
                'detail_2_why' => '',
                'detail_2_triggers' => '',
                'detail_2_plan' => '',
                'detail_3_risk' => '',
                'detail_3_score' => '',
                'detail_3_why' => '',
                'detail_3_triggers' => '',
                'detail_3_plan' => '',
                'detail_4_risk' => '',
                'detail_4_score' => '',
                'detail_4_why' => '',
                'detail_4_triggers' => '',
                'detail_4_plan' => '',
                'detail_5_risk' => '',
                'detail_5_score' => '',
                'detail_5_why' => '',
                'detail_5_triggers' => '',
                'detail_5_plan' => '',
                'detail_6_risk' => '',
                'detail_6_score' => '',
                'detail_6_why' => '',
                'detail_6_triggers' => '',
                'detail_6_plan' => '',
                'detail_7_risk' => '',
                'detail_7_score' => '',
                'detail_7_why' => '',
                'detail_7_triggers' => '',
                'detail_7_plan' => '',
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
                'office_assessment_date' => now()->toDateString(),
                'office_outcome' => 'Accepted',
                'office_allocated_worker' => 'Sam Support',
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
            DocumentTemplateKey::InitialNeedsAssessment => $this->initialNeedsAssessmentPayload(),
            DocumentTemplateKey::ExcludedLicenseAgreement => [
                'provider_name' => 'Rneyz Housing',
                'names' => 'Jane Resident',
                'room_no' => '1',
                'address' => '12 High Street, Coventry, CV1 1AA, United Kingdom',
                'postcode' => 'CV1 1AA',
                'date_of_birth' => '1990-01-01',
                'ni_number' => 'AB123456C',
                'phone_email' => '07123456789 / jane.resident@example.com',
                'license_start_date' => now()->toDateString(),
                'license_begins_on' => now()->toDateString(),
                'total_core_rent' => '100.00',
                'total_eligible_service_charges' => '35.00',
                'total_eligible_service_charge_and_rent' => '135.00',
                'electric_meter' => '1000',
                'gas_meter' => '2000',
                'water_meter' => '3000',
                'licensee_print_name' => 'Jane Resident',
                'licensee_dated_1' => now()->toDateString(),
                'licensee_dated_2' => now()->toDateString(),
                'provider_signatory_name' => 'Sam Support',
                'provider_dated_1' => now()->toDateString(),
                'provider_dated_2' => now()->toDateString(),
            ],
            DocumentTemplateKey::NilIncomeForm => [
                'claimant_name' => 'Jane Resident',
                'period_from' => now()->subMonth()->toDateString(),
                'period_to' => now()->toDateString(),
                'address' => '12 High Street, Coventry, CV1 1AA, United Kingdom',
                'support_methods' => ['Using Savings'],
                'support_details' => 'Using personal savings',
                'savings_value' => '250.00',
                'other_information' => 'None',
                'claimant_date' => now()->toDateString(),
                'filled_by_reason' => '',
                'filled_by_name' => '',
                'filled_by_relationship' => '',
                'filled_by_date' => '',
            ],
            DocumentTemplateKey::ResidentReviewForm => [
                'resident_name' => 'Jane Resident',
                'date_of_completion' => now()->toDateString(),
                'address' => '12 High Street, Coventry, CV1 1AA, United Kingdom',
                'happy_with_support' => 'Yes',
                'happy_with_support_why' => 'Support is helpful',
                'see_support_worker' => 'Weekly',
                'goal_1' => 'Budgeting',
                'goal_2' => 'Job search',
                'goal_3' => 'Health',
                'goal_4' => 'Social networks',
                'goal_5' => 'Move-on readiness',
                'next_3_months' => 'Apply for social housing',
                'anything_else' => 'No',
                'anything_else_details' => '',
                'support_worker_name' => 'Sam Support',
            ],
            DocumentTemplateKey::ListOfInventory => [
                'resident_name' => 'Jane Resident',
                'property_address' => '12 High Street, Coventry, CV1 1AA, United Kingdom',
                'date_moved_in' => now()->subMonths(2)->toDateString(),
                'inv_1' => ['item' => 'Bed', 'description' => 'Single bed with mattress', 'quantity' => '1'],
                'inv_2' => ['item' => 'Wardrobe', 'description' => 'Wooden wardrobe', 'quantity' => '1'],
                'inv_3' => ['item' => '', 'description' => '', 'quantity' => ''],
                'inv_4' => ['item' => '', 'description' => '', 'quantity' => ''],
                'inv_5' => ['item' => '', 'description' => '', 'quantity' => ''],
                'inv_6' => ['item' => '', 'description' => '', 'quantity' => ''],
                'inv_7' => ['item' => '', 'description' => '', 'quantity' => ''],
                'inv_8' => ['item' => '', 'description' => '', 'quantity' => ''],
                'inv_9' => ['item' => '', 'description' => '', 'quantity' => ''],
                'inv_10' => ['item' => '', 'description' => '', 'quantity' => ''],
            ],
            DocumentTemplateKey::IdChecklist => [
                'resident_name' => 'Jane Resident',
                'address' => '12 High Street, Coventry, CV1 1AA, United Kingdom',
                'date_moved_in' => now()->subMonths(2)->toDateString(),
                'support_worker_name' => 'Sam Support',
                'previous_rp_landlord' => 'Previous Landlord',
                'previous_housing_provider' => 'Previous Provider',
                'previous_address' => 'Old Address',
                'documents_completed' => ['ID-1', 'ID-5', 'ID-6'],
            ],
            DocumentTemplateKey::HousingBenefitAuthorisation => [
                'provider_name' => 'Rneyz Housing',
                'resident_name' => 'Jane Resident',
                'address' => '12 High Street, Coventry, CV1 1AA, United Kingdom',
                'date_of_birth' => '1990-01-01',
                'ni_number' => 'AB123456C',
                'authorisation_date' => now()->toDateString(),
            ],
            DocumentTemplateKey::StaffRiskAssessment => [
                'resident_name' => 'Jane Resident',
                'risk_1_name' => 'Violence & aggression',
                'risk_1_resident_score' => '1',
                'risk_1_staff_score' => '3',
                'risk_1_disagree_reason' => 'Resident minimised history',
                'risk_1_why_score' => 'Based on previous incidents',
                'risk_1_triggers' => 'Conflict with peers',
                'risk_1_plan' => 'Staff present during mediation',
                'risk_2_name' => '',
                'risk_2_resident_score' => '',
                'risk_2_staff_score' => '',
                'risk_2_disagree_reason' => '',
                'risk_2_why_score' => '',
                'risk_2_triggers' => '',
                'risk_2_plan' => '',
                'further_comments' => 'None',
            ],
            DocumentTemplateKey::SupportPlan => $this->supportPlanPayload(),
            DocumentTemplateKey::SupportPlanSchedule => [],
        };

        return [
            'template' => $template->value,
            'fields' => $fields,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function initialNeedsAssessmentPayload(): array
    {
        $fields = [
            'completed_by' => 'Sam Support',
            'date_of_completion' => now()->toDateString(),
            'preferred_title' => 'Miss',
            'preferred_title_other' => '',
            'surname' => 'Resident',
            'first_name' => 'Jane',
            'gender' => 'Female',
            'ni_number' => 'AB123456C',
            'previous_address' => '12 High Street, Coventry, CV1 1AA, United Kingdom',
            'date_of_birth' => '1990-01-01',
            'contact_number' => '07123456789',
            'email' => 'jane.resident@example.com',
            'reason_homelessness' => 'Loss of previous accommodation',
            'family_contact' => 'Yes',
            'family_support_details' => 'Occasional support',
            'has_next_of_kin' => 'Yes',
            'nok_name' => 'John Resident',
            'nok_contact' => '07999999999',
            'nok_relationship' => 'Brother',
            'uses_substances' => 'No',
            'drugs_details' => '',
            'alcohol_details' => '',
            'has_substance_worker' => 'No',
            'substance_worker_details' => '',
            'want_substance_help' => 'No',
            'substance_help_no_reason' => 'N/A',
            'physical_health' => 'No',
            'physical_health_details' => '',
            'physical_influences_daily' => 'No',
            'physical_daily_details' => '',
            'need_physical_help' => 'No',
            'mental_health' => 'No',
            'mental_health_details' => '',
            'mental_influences_daily' => 'No',
            'mental_daily_details' => '',
            'self_harm_history' => 'No',
            'self_harm_details' => '',
            'prescribed_medication' => 'No',
            'medication_details' => '',
            'medication_reminder' => 'No',
            'registered_gp' => 'Yes',
            'gp_details' => 'City Surgery',
            'need_healthcare_register' => 'No',
            'healthcare_services' => [],
            'criminal_convictions' => 'No',
            'convictions_details' => '',
            'subject_to_orders' => 'No',
            'orders_details' => '',
            'need_reduce_asb' => 'No',
            'reduce_asb_details' => '',
            'need_positive_networks' => 'No',
            'positive_networks_details' => '',
            'violence_history' => 'No',
            'violence_details' => '',
            'arson_history' => 'No',
            'arson_details' => '',
            'claims_benefits' => 'Yes',
            'benefits_details' => 'Universal Credit',
            'other_income' => 'No',
            'other_income_details' => '',
            'has_debts' => 'No',
            'debts_details' => '',
            'repaying_debts' => 'No',
            'want_debt_help' => 'No',
            'debt_help_no_reason' => '',
            'owed_money' => 'No',
            'owed_money_details' => '',
            'need_recoup_money' => 'No',
            'has_bank_account' => 'Yes',
            'want_bank_help' => 'No',
            'currently_working' => 'No',
            'working_details' => '',
            'want_employment_access' => 'Yes',
            'employment_type_wanted' => 'Retail',
            'employment_history' => 'Yes',
            'employment_history_details' => 'Previous retail work',
            'has_cv' => 'No',
            'want_cv_help' => 'Yes',
            'want_leisure_help' => 'No',
            'leisure_details' => '',
            'own_accommodation_before' => 'Yes',
            'able_manage_accommodation' => 'No',
            'manage_accommodation_why' => 'Needs support',
            'registered_social_housing' => 'No',
            'social_housing_details' => '',
            'interested_social_housing' => 'Yes',
            'interested_social_details' => 'Local authority waiting list',
            'skill_budget' => 'Yes',
            'skill_hygiene' => 'No',
            'skill_housekeeping' => 'Yes',
            'skill_laundry' => 'No',
            'skill_cook' => 'Yes',
            'skill_shop' => 'No',
            'skill_asb' => 'No',
            'skill_communicate' => 'No',
            'skill_organise' => 'Yes',
            'skill_perspective' => 'No',
            'risk_violence' => 'No',
            'risk_offending' => 'No',
            'risk_harm' => 'No',
            'risk_learning' => 'No',
            'risk_self_neglect' => 'No',
            'risk_sex_offences' => 'No',
            'risk_mental_deterioration' => 'No',
            'risk_domestic_abuse' => 'No',
            'risk_physical_deterioration' => 'No',
            'risk_suicide' => 'No',
            'risk_substance' => 'No',
            'risk_arson' => 'No',
            'risk_financial' => 'No',
            'risk_other' => 'No',
            'risk_other_text' => '',
            'risk_explanation' => 'No significant risks identified',
            'ethnic_origin' => 'White: British',
            'ethnic_origin_other' => '',
            'religion' => 'No religion /Atheist',
            'religion_other' => '',
            'sexual_orientation' => 'Heterosexual',
            'sexual_orientation_other' => '',
            'support_worker_name' => 'Sam Support',
        ];

        return $fields;
    }

    /**
     * @return array<string, mixed>
     */
    private function supportPlanPayload(): array
    {
        $fields = [
            'resident_name' => 'Jane Resident',
            'address' => '12 High Street, Coventry, CV1 1AA, United Kingdom',
            'telephone_number' => '07123456789',
            'email' => 'jane.resident@example.com',
            'support_worker' => 'Sam Support',
            'assessment_date' => now()->toDateString(),
            'review_3_month' => now()->addMonths(3)->toDateString(),
            'review_6_month' => now()->addMonths(6)->toDateString(),
            'review_9_month' => now()->addMonths(9)->toDateString(),
            'review_12_month' => now()->addYear()->toDateString(),
            'what_achieve' => 'Live independently',
            'what_stopping' => 'Needs budgeting support',
            'goal_bank' => [
                'st_access_healthcare',
                'st_open_bank',
                'st_maximize_income',
                'mt_reduce_debt',
                'lt_independent_accommodation',
            ],
            'lived_supported_before' => 'No',
            'did_engage' => 'Yes',
            'engage_no_reason' => '',
            'feedback' => 'Support has been helpful',
        ];

        foreach (['short', 'medium', 'long'] as $term) {
            for ($i = 1; $i <= 4; $i++) {
                $fields[$term.'_'.$i.'_goal'] = $i === 1 && $term === 'short' ? 'Open bank account' : '';
                $fields[$term.'_'.$i.'_how'] = $i === 1 && $term === 'short' ? 'Attend bank appointment with support worker' : '';
                $fields[$term.'_'.$i.'_target_date'] = $i === 1 && $term === 'short' ? now()->addMonth()->toDateString() : '';
                $fields[$term.'_'.$i.'_date_completed'] = '';
            }
        }

        return $fields;
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
