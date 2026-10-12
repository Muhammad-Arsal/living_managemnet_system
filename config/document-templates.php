<?php

use App\Enums\DocumentTemplateKey;

/**
 * Field definitions for property document generation.
 *
 * source values:
 * - today | site_name | site_phone
 * - tenant.full_name | tenant.first_name | tenant.email | tenant.mobile_number
 * - property.formatted_address | property.postcode | property.name
 * - tenancy.started_on
 * - null = manual entry only
 *
 * status in the UI is derived at runtime: prefilled | editable | missing
 */
return [

    'templates' => [

        DocumentTemplateKey::ServiceChargeNotice->value => [
            'fields' => [
                'provider_name' => ['label' => 'Provider / organisation name', 'type' => 'text', 'source' => 'site_name', 'required' => true],
                'date_issued' => ['label' => 'Date issued', 'type' => 'date', 'source' => 'today', 'required' => true],
                'tenant_name' => ['label' => 'Tenant name', 'type' => 'text', 'source' => 'tenant.full_name', 'required' => true],
                'property_address' => ['label' => 'Property address', 'type' => 'textarea', 'source' => 'property.formatted_address', 'required' => true],
                'dear_name' => ['label' => 'Salutation name', 'type' => 'text', 'source' => 'tenant.first_name', 'required' => true],
                'service_charge_amount' => ['label' => 'Weekly service charge amount (£)', 'type' => 'text', 'source' => null, 'required' => true],
                'payment_day' => ['label' => 'Payment due day (e.g. Monday)', 'type' => 'text', 'source' => null, 'required' => true],
                'first_payment_due' => ['label' => 'First payment due', 'type' => 'date', 'source' => null, 'required' => true],
                'account_name' => ['label' => 'Account name', 'type' => 'text', 'source' => null, 'required' => true],
                'sort_code' => ['label' => 'Sort code', 'type' => 'text', 'source' => null, 'required' => true],
                'account_number' => ['label' => 'Account number', 'type' => 'text', 'source' => null, 'required' => true],
            ],
        ],

        DocumentTemplateKey::MoveOnPlan->value => [
            'fields' => [
                'provider_name' => ['label' => 'Provider name', 'type' => 'text', 'source' => 'site_name', 'required' => true],
                'property_address' => ['label' => 'Property address', 'type' => 'textarea', 'source' => 'property.formatted_address', 'required' => true],
                'resident_name' => ['label' => 'Resident name', 'type' => 'text', 'source' => 'tenant.full_name', 'required' => true],
                'date_of_birth' => ['label' => 'Date of birth', 'type' => 'date', 'source' => null, 'required' => false],
                'ni_number' => ['label' => 'National Insurance number', 'type' => 'text', 'source' => null, 'required' => false],
                'support_worker' => ['label' => 'Support worker', 'type' => 'text', 'source' => null, 'required' => true],
                'plan_started_on' => ['label' => 'Date move-on plan started', 'type' => 'date', 'source' => 'today', 'required' => true],
                'review_date' => ['label' => 'Review date', 'type' => 'date', 'source' => null, 'required' => false],
                'target_move_on_date' => ['label' => 'Target move-on date', 'type' => 'date', 'source' => null, 'required' => false],
                'accommodation_type' => ['label' => 'Current accommodation type', 'type' => 'text', 'source' => null, 'required' => false],
                'moved_in_on' => ['label' => 'Date resident moved in', 'type' => 'date', 'source' => 'tenancy.started_on', 'required' => false],
                'length_of_stay' => ['label' => 'Length of stay to date', 'type' => 'text', 'source' => null, 'required' => false],
                'reason_for_placement' => ['label' => 'Reason for placement', 'type' => 'textarea', 'source' => null, 'required' => false],
                'long_term_goal' => ['label' => "Resident's long-term goal", 'type' => 'textarea', 'source' => null, 'required' => false],
                'housing_goal_type' => ['label' => 'Target accommodation type', 'type' => 'text', 'source' => null, 'required' => false],
                'target_timescale' => ['label' => 'Target timescale', 'type' => 'text', 'source' => null, 'required' => false],
                'objectives_notes' => ['label' => 'Move-on objectives / actions notes', 'type' => 'textarea', 'source' => null, 'required' => false],
                'barriers' => ['label' => 'Barriers to move-on', 'type' => 'textarea', 'source' => null, 'required' => false],
                'support_provided' => ['label' => 'Support provided', 'type' => 'textarea', 'source' => null, 'required' => false],
                'support_frequency' => ['label' => 'Frequency of support', 'type' => 'text', 'source' => null, 'required' => false],
            ],
        ],

        DocumentTemplateKey::WarningLetter->value => [
            'fields' => [
                'resident_name' => ['label' => 'Name of resident', 'type' => 'text', 'source' => 'tenant.full_name', 'required' => true],
                'address' => ['label' => 'Address', 'type' => 'textarea', 'source' => 'property.formatted_address', 'required' => true],
                'postcode' => ['label' => 'Postcode', 'type' => 'text', 'source' => 'property.postcode', 'required' => true],
                'date' => ['label' => 'Date', 'type' => 'date', 'source' => 'today', 'required' => true],
                'dear_name' => ['label' => 'Salutation name', 'type' => 'text', 'source' => 'tenant.full_name', 'required' => true],
                'warning_reasons' => ['label' => 'Reason(s) for warning', 'type' => 'textarea', 'source' => null, 'required' => true],
                'meeting_datetime' => ['label' => 'Meeting arranged for', 'type' => 'text', 'source' => null, 'required' => true],
                'meeting_location' => ['label' => 'Meeting location', 'type' => 'text', 'source' => null, 'required' => true],
                'contact_phone' => ['label' => 'Contact telephone', 'type' => 'text', 'source' => 'site_phone', 'required' => true],
                'support_worker_name' => ['label' => 'Support worker name', 'type' => 'text', 'source' => null, 'required' => true],
                'support_worker_title' => ['label' => 'Support worker title / line 2', 'type' => 'text', 'source' => null, 'required' => false, 'default' => 'Support Worker'],
            ],
        ],

        DocumentTemplateKey::FinalWarning->value => [
            'fields' => [
                'resident_name' => ['label' => 'Name of resident', 'type' => 'text', 'source' => 'tenant.full_name', 'required' => true],
                'address' => ['label' => 'Address', 'type' => 'textarea', 'source' => 'property.formatted_address', 'required' => true],
                'postcode' => ['label' => 'Postcode', 'type' => 'text', 'source' => 'property.postcode', 'required' => true],
                'date' => ['label' => 'Date', 'type' => 'date', 'source' => 'today', 'required' => true],
                'dear_name' => ['label' => 'Salutation name', 'type' => 'text', 'source' => 'tenant.full_name', 'required' => true],
                'missed_meeting_on' => ['label' => 'Missed meeting date', 'type' => 'text', 'source' => null, 'required' => true],
                'meeting_datetime' => ['label' => 'New meeting arranged for', 'type' => 'text', 'source' => null, 'required' => true],
                'meeting_location' => ['label' => 'Meeting location', 'type' => 'text', 'source' => null, 'required' => true],
                'contact_phone' => ['label' => 'Contact telephone', 'type' => 'text', 'source' => 'site_phone', 'required' => true],
                'support_worker_name' => ['label' => 'Support worker name', 'type' => 'text', 'source' => null, 'required' => true],
                'support_worker_title' => ['label' => 'Support worker title / line 2', 'type' => 'text', 'source' => null, 'required' => false, 'default' => 'Support Worker'],
            ],
        ],

        DocumentTemplateKey::RiskAssessment->value => [
            'fields' => [
                'resident_name' => ['label' => 'Name of resident', 'type' => 'text', 'source' => 'tenant.full_name', 'required' => true],
                'address' => ['label' => 'Address', 'type' => 'textarea', 'source' => 'property.formatted_address', 'required' => true],
                'telephone' => ['label' => 'Telephone number', 'type' => 'text', 'source' => 'tenant.mobile_number', 'required' => false],
                'email' => ['label' => 'Email address', 'type' => 'text', 'source' => 'tenant.email', 'required' => false],
                'support_worker' => ['label' => 'Name of support worker', 'type' => 'text', 'source' => null, 'required' => true],
                'assessment_date' => ['label' => 'Date', 'type' => 'date', 'source' => 'today', 'required' => true],
                'provider_name' => ['label' => 'Organisation name', 'type' => 'text', 'source' => 'site_name', 'required' => true],
                'review_3_month' => ['label' => '3 monthly review date', 'type' => 'date', 'source' => null, 'required' => false],
                'review_6_month' => ['label' => '6 monthly review date', 'type' => 'date', 'source' => null, 'required' => false],
                'review_9_month' => ['label' => '9 monthly review date', 'type' => 'date', 'source' => null, 'required' => false],
                'review_12_month' => ['label' => '12 monthly review date', 'type' => 'date', 'source' => null, 'required' => false],
                'risk_violence' => ['label' => '1. Violence & aggression — initial score / notes', 'type' => 'textarea', 'source' => null, 'required' => false],
                'risk_harm' => ['label' => '2. Harm to/from others — initial score / notes', 'type' => 'textarea', 'source' => null, 'required' => false],
                'risk_self_neglect' => ['label' => '3. Self-neglect or self-harm — initial score / notes', 'type' => 'textarea', 'source' => null, 'required' => false],
                'risk_mental' => ['label' => '4. Mental & emotional health — initial score / notes', 'type' => 'textarea', 'source' => null, 'required' => false],
                'risk_physical' => ['label' => '5. Physical health — initial score / notes', 'type' => 'textarea', 'source' => null, 'required' => false],
                'risk_substance' => ['label' => '6. Substance misuse / addiction — initial score / notes', 'type' => 'textarea', 'source' => null, 'required' => false],
                'risk_financial' => ['label' => '7. Financial hardship — initial score / notes', 'type' => 'textarea', 'source' => null, 'required' => false],
                'risk_offending' => ['label' => '8. Offending / ASB — initial score / notes', 'type' => 'textarea', 'source' => null, 'required' => false],
                'risk_learning' => ['label' => '9. Learning / communication — initial score / notes', 'type' => 'textarea', 'source' => null, 'required' => false],
                'risk_other' => ['label' => '10–11. Other risks (please specify)', 'type' => 'textarea', 'source' => null, 'required' => false],
            ],
        ],

        DocumentTemplateKey::ReferralForm->value => [
            'fields' => [
                'organisation' => ['label' => 'Organisation', 'type' => 'text', 'source' => 'site_name', 'required' => true],
                'full_name' => ['label' => 'Full name', 'type' => 'text', 'source' => 'tenant.full_name', 'required' => true],
                'dob_age' => ['label' => 'DOB & age', 'type' => 'text', 'source' => null, 'required' => false],
                'financial_status' => ['label' => 'Financial status', 'type' => 'textarea', 'source' => null, 'required' => false],
                'ni_number' => ['label' => 'NI number', 'type' => 'text', 'source' => null, 'required' => false],
                'telephone' => ['label' => 'Telephone', 'type' => 'text', 'source' => 'tenant.mobile_number', 'required' => false],
                'gender' => ['label' => 'Gender', 'type' => 'text', 'source' => null, 'required' => false],
                'email' => ['label' => 'Email', 'type' => 'text', 'source' => 'tenant.email', 'required' => false],
                'client_number' => ['label' => 'Client number', 'type' => 'text', 'source' => null, 'required' => false],
                'previous_contact' => ['label' => 'Previous contact with organisation', 'type' => 'text', 'source' => null, 'required' => false],
                'current_address' => ['label' => 'Current address / housing status', 'type' => 'textarea', 'source' => 'property.formatted_address', 'required' => false],
                'landlord_contact' => ['label' => 'Landlord name & contact', 'type' => 'text', 'source' => null, 'required' => false],
                'reason_support' => ['label' => 'Reason support required', 'type' => 'textarea', 'source' => null, 'required' => true],
                'current_accommodation' => ['label' => 'Current accommodation (summary)', 'type' => 'textarea', 'source' => null, 'required' => false],
                'current_circumstances' => ['label' => 'Current circumstances', 'type' => 'textarea', 'source' => null, 'required' => false],
                'support_needs' => ['label' => 'Support needs', 'type' => 'textarea', 'source' => null, 'required' => false],
                'translation_language' => ['label' => 'Translation language', 'type' => 'text', 'source' => null, 'required' => false],
                'health_issues' => ['label' => 'Health issues', 'type' => 'textarea', 'source' => null, 'required' => false],
                'gp_cpn' => ['label' => 'GP / CPN', 'type' => 'text', 'source' => null, 'required' => false],
                'gp_telephone' => ['label' => 'GP telephone', 'type' => 'text', 'source' => null, 'required' => false],
                'gp_address' => ['label' => 'GP address', 'type' => 'textarea', 'source' => null, 'required' => false],
                'medication' => ['label' => 'Medication', 'type' => 'textarea', 'source' => null, 'required' => false],
                'risk_factors' => ['label' => 'Risk factors', 'type' => 'textarea', 'source' => null, 'required' => false],
                'other_agencies' => ['label' => 'Other agencies involved', 'type' => 'textarea', 'source' => null, 'required' => false],
                'referring_agency' => ['label' => 'Referring agency', 'type' => 'text', 'source' => null, 'required' => false],
                'referrer_name' => ['label' => 'Referrer name', 'type' => 'text', 'source' => null, 'required' => false],
                'referrer_phone' => ['label' => 'Referrer contact number', 'type' => 'text', 'source' => null, 'required' => false],
                'referrer_email' => ['label' => 'Referrer email', 'type' => 'text', 'source' => null, 'required' => false],
                'application_date' => ['label' => 'Application date', 'type' => 'date', 'source' => 'today', 'required' => false],
                'referral_date' => ['label' => 'Referral date', 'type' => 'date', 'source' => 'today', 'required' => false],
                'safeguarding' => ['label' => 'Safeguarding concerns or risks', 'type' => 'textarea', 'source' => null, 'required' => false],
                'emergency_name' => ['label' => 'Emergency contact name', 'type' => 'text', 'source' => null, 'required' => false],
                'emergency_relationship' => ['label' => 'Emergency contact relationship', 'type' => 'text', 'source' => null, 'required' => false],
                'emergency_telephone' => ['label' => 'Emergency contact telephone', 'type' => 'text', 'source' => null, 'required' => false],
                'ethnicity' => ['label' => 'Ethnicity (optional)', 'type' => 'text', 'source' => null, 'required' => false],
                'religion' => ['label' => 'Religion / belief (optional)', 'type' => 'text', 'source' => null, 'required' => false],
                'disability' => ['label' => 'Disability (optional)', 'type' => 'text', 'source' => null, 'required' => false],
                'preferred_language' => ['label' => 'Preferred language (optional)', 'type' => 'text', 'source' => null, 'required' => false],
            ],
        ],

    ],

];
