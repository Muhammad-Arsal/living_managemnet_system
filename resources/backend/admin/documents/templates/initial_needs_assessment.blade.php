@extends('backend::admin.documents.templates.layout')

@section('content')
    @php
        $f = $fields;
        $defs = $definitions ?? [];
        $skills = [
            ['key' => 'skill_budget', 'label' => 'Learn how to budget & manage your finances better'],
            ['key' => 'skill_hygiene', 'label' => 'Learn how to improve & maintain personal hygiene'],
            ['key' => 'skill_housekeeping', 'label' => 'Learn basic housekeeping & how to clean effectively'],
            ['key' => 'skill_laundry', 'label' => 'Learn how to sort, wash & dry clothes effectively'],
            ['key' => 'skill_cook', 'label' => 'Learn how to cook basic meals & basic food safety'],
            ['key' => 'skill_shop', 'label' => 'Learn how to shop efficiently to reduce spending costs'],
            ['key' => 'skill_asb', 'label' => 'Learn how to deal with and address anti-social behaviour'],
            ['key' => 'skill_communicate', 'label' => 'Learn how to communicate effectively & efficiently'],
            ['key' => 'skill_organise', 'label' => 'Learn how to manage your time & be more organised'],
            ['key' => 'skill_perspective', 'label' => "Learn to look at situations from other people's perspective"],
        ];
        $risks = [
            ['key' => 'risk_violence', 'label' => 'Violence & Aggression'],
            ['key' => 'risk_offending', 'label' => 'Offending and /or Anti- Social Behaviour'],
            ['key' => 'risk_harm', 'label' => 'Harm to others or from others'],
            ['key' => 'risk_learning', 'label' => 'Learning, understanding or communication difficulties'],
            ['key' => 'risk_self_neglect', 'label' => 'Self-neglect or self-harm'],
            ['key' => 'risk_sex_offences', 'label' => 'Sex offences'],
            ['key' => 'risk_mental_deterioration', 'label' => 'Deterioration in mental and emotional health'],
            ['key' => 'risk_domestic_abuse', 'label' => 'Domestic abuse'],
            ['key' => 'risk_physical_deterioration', 'label' => 'Deterioration in physical health'],
            ['key' => 'risk_suicide', 'label' => 'Suicide'],
            ['key' => 'risk_substance', 'label' => 'Substance misuse, alcohol abuse or other addiction'],
            ['key' => 'risk_arson', 'label' => 'Arson'],
            ['key' => 'risk_financial', 'label' => 'Financial hardship or difficulties managing finances'],
            ['key' => 'risk_other', 'label' => 'Other'],
        ];
    @endphp

    <h1>INITIAL NEEDS ASSESSMENT (RH-1)</h1>

    <div class="meta">
        <p class="meta-row"><span class="label">Name of person completing assessment:</span> {{ $f['completed_by'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Date of Completion:</span> {{ $f['date_of_completion'] ?? '' }}</p>
    </div>

    <h2>1. Applicants Details</h2>
    <p class="label">Preferred Title</p>
    @include('backend::admin.documents.templates.partials.option-list', [
        'options' => $defs['preferred_title']['options'] ?? [],
        'selected' => $f['preferred_title'] ?? null,
    ])
    @if (! empty($f['preferred_title_other']))
        <p class="meta-row"><span class="label">Other:</span> {{ $f['preferred_title_other'] }}</p>
    @endif
    <p class="meta-row"><span class="label">Surname:</span> {{ $f['surname'] ?? '' }}</p>
    <p class="meta-row"><span class="label">First Name(s):</span> {{ $f['first_name'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Gender:</span> {{ $f['gender'] ?? '' }}</p>
    <p class="meta-row"><span class="label">NI Number:</span> {{ $f['ni_number'] ?? '' }}</p>
    <p class="label">Address: (where you have been living previously)</p>
    <div class="box">{{ $f['previous_address'] ?? '' }}</div>
    <p class="meta-row"><span class="label">Date of Birth:</span> {{ $f['date_of_birth'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Contact Number:</span> {{ $f['contact_number'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Email Address:</span> {{ $f['email'] ?? '' }}</p>
    <p class="label">Reason for Homelessness:</p>
    <div class="box">{{ $f['reason_homelessness'] ?? '' }}</div>
    <p class="meta-row"><span class="label">Do you have contact with your family?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['family_contact'] ?? null])</p>
    <p class="label">If yes, what support do they provide, if any? Are there any other people that help you?</p>
    <div class="box">{{ $f['family_support_details'] ?? '' }}</div>

    <h2>2. Next of Kin Details</h2>
    <p class="meta-row"><span class="label">Do you have next of kin or an emergency contact?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['has_next_of_kin'] ?? null])</p>
    <p class="label">If yes, please give details</p>
    <p class="meta-row"><span class="label">Name:</span> {{ $f['nok_name'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Contact Number</span> {{ $f['nok_contact'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Relationship:</span> {{ $f['nok_relationship'] ?? '' }}</p>

    <h2>3. Substance Use</h2>
    <p class="meta-row"><span class="label">Do you use drugs or alcohol?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['uses_substances'] ?? null])</p>
    <p class="label">If yes, what drugs do you use? How is it administered? (i.e., snort, inject, smoke)</p>
    <div class="box">{{ $f['drugs_details'] ?? '' }}</div>
    <p class="label">If yes, what alcohol, how much &amp; how often?</p>
    <div class="box">{{ $f['alcohol_details'] ?? '' }}</div>
    <p class="meta-row"><span class="label">Do you have a drug / alcohol worker?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['has_substance_worker'] ?? null])</p>
    <p class="label">If yes, please give details. (Company, worker name, contact details, any prescription information)</p>
    <div class="box">{{ $f['substance_worker_details'] ?? '' }}</div>
    <p class="meta-row"><span class="label">If not, would you like help to reduce substance misuse and access drug / alcohol support services?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['want_substance_help'] ?? null])</p>
    <p class="label">If no, please explain why?</p>
    <div class="box">{{ $f['substance_help_no_reason'] ?? '' }}</div>

    <h2>4. Physical and Mental Health</h2>
    <p class="meta-row"><span class="label">Do you have any physical health conditions?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['physical_health'] ?? null])</p>
    <p class="label">If yes, please give details:</p>
    <div class="box">{{ $f['physical_health_details'] ?? '' }}</div>
    <p class="meta-row"><span class="label">If yes, do these health conditions influence your daily living?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['physical_influences_daily'] ?? null])</p>
    <p class="label">If yes, please give details:</p>
    <div class="box">{{ $f['physical_daily_details'] ?? '' }}</div>
    <p class="meta-row"><span class="label">Do you need help to manage / improve your physical health?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['need_physical_help'] ?? null])</p>
    <p class="meta-row"><span class="label">Do you have any mental health conditions?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['mental_health'] ?? null])</p>
    <p class="label">If yes, please give details of condition and if you have any support from mental health services</p>
    <div class="box">{{ $f['mental_health_details'] ?? '' }}</div>
    <p class="meta-row"><span class="label">If yes, do your mental health conditions influence your daily living?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['mental_influences_daily'] ?? null])</p>
    <p class="label">If yes, please give details</p>
    <div class="box">{{ $f['mental_daily_details'] ?? '' }}</div>
    <p class="meta-row"><span class="label">Have you ever self-harmed, had suicidal thoughts or attempted suicide?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['self_harm_history'] ?? null])</p>
    <p class="label">If yes, please explain in further detail.</p>
    <div class="box">{{ $f['self_harm_details'] ?? '' }}</div>
    <p class="meta-row"><span class="label">Are you prescribed medication?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['prescribed_medication'] ?? null])</p>
    <p class="label">If so, what are you prescribed and what is it for?</p>
    <div class="box">{{ $f['medication_details'] ?? '' }}</div>
    <p class="meta-row"><span class="label">If yes, do you need a reminder to take your medication?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['medication_reminder'] ?? null])</p>
    <p class="meta-row"><span class="label">Are you registered with a G.P?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['registered_gp'] ?? null])</p>
    <p class="label">If yes, please provide details.</p>
    <div class="box">{{ $f['gp_details'] ?? '' }}</div>
    <p class="meta-row"><span class="label">Do you need help to register with healthcare services?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['need_healthcare_register'] ?? null])</p>
    @include('backend::admin.documents.templates.partials.option-list', [
        'options' => $defs['healthcare_services']['options'] ?? [],
        'selected' => $f['healthcare_services'] ?? [],
    ])

    <h2>5. Legal Status/Behaviour</h2>
    <p class="meta-row"><span class="label">Do you have any criminal convictions?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['criminal_convictions'] ?? null])</p>
    <p class="label">If yes, please give details</p>
    <div class="box">{{ $f['convictions_details'] ?? '' }}</div>
    <p class="meta-row"><span class="label">Are you subject to any orders?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['subject_to_orders'] ?? null])</p>
    <p class="label">If yes, please give details DRR/Probation/Injunctions, if applicable, please give details of Probation Officer</p>
    <div class="box">{{ $f['orders_details'] ?? '' }}</div>
    <p class="meta-row"><span class="label">Do you need help to reduce anti-social or offending behaviour?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['need_reduce_asb'] ?? null])</p>
    <p class="label">If yes, please give details:</p>
    <div class="box">{{ $f['reduce_asb_details'] ?? '' }}</div>
    <p class="meta-row"><span class="label">Do you need help to establish positive support networks?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['need_positive_networks'] ?? null])</p>
    <p class="label">If yes, please give details:</p>
    <div class="box">{{ $f['positive_networks_details'] ?? '' }}</div>
    <p class="meta-row"><span class="label">Do you have a history of violence/aggression?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['violence_history'] ?? null])</p>
    <p class="label">If yes, please give details including triggers</p>
    <div class="box">{{ $f['violence_details'] ?? '' }}</div>
    <p class="meta-row"><span class="label">Do you have any history of committing arson or property damage?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['arson_history'] ?? null])</p>
    <p class="label">If yes, please give details:</p>
    <div class="box">{{ $f['arson_details'] ?? '' }}</div>

    <h2>6. Finances/Income</h2>
    <p class="meta-row"><span class="label">Do you currently claim benefits?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['claims_benefits'] ?? null])</p>
    <p class="label">If yes, please give details and amounts</p>
    <div class="box">{{ $f['benefits_details'] ?? '' }}</div>
    <p class="meta-row"><span class="label">Do you have any other source of income?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['other_income'] ?? null])</p>
    <p class="label">If yes, please give details</p>
    <div class="box">{{ $f['other_income_details'] ?? '' }}</div>
    <p class="meta-row"><span class="label">Do you have any debts?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['has_debts'] ?? null])</p>
    <p class="label">If yes, please give details</p>
    <div class="box">{{ $f['debts_details'] ?? '' }}</div>
    <p class="meta-row"><span class="label">Are you repaying any of the debts at the moment?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['repaying_debts'] ?? null])</p>
    <p class="meta-row"><span class="label">If not, would you like help to address these debts?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['want_debt_help'] ?? null])</p>
    <p class="label">If not, please explain why?</p>
    <div class="box">{{ $f['debt_help_no_reason'] ?? '' }}</div>
    <p class="meta-row"><span class="label">Does anyone owe you any money?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['owed_money'] ?? null])</p>
    <p class="label">If yes, please give details</p>
    <div class="box">{{ $f['owed_money_details'] ?? '' }}</div>
    <p class="meta-row"><span class="label">Do you need help to recoup this money?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['need_recoup_money'] ?? null])</p>
    <p class="meta-row"><span class="label">Do you have a bank account?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['has_bank_account'] ?? null])</p>
    <p class="meta-row"><span class="label">If no, would you like help to get a bank account?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['want_bank_help'] ?? null])</p>

    <h2>7. Enjoy &amp; Achieve</h2>
    <p class="meta-row"><span class="label">Are you currently working, training or volunteering?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['currently_working'] ?? null])</p>
    <p class="label">If yes, please provide details:</p>
    <div class="box">{{ $f['working_details'] ?? '' }}</div>
    <p class="meta-row"><span class="label">If no, would you like to access employment, training or volunteering opportunities</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['want_employment_access'] ?? null])</p>
    <p class="label">If yes, what type of employment, training or volunteering opportunities</p>
    <div class="box">{{ $f['employment_type_wanted'] ?? '' }}</div>
    <p class="meta-row"><span class="label">Do you have any history of employment, training or volunteering?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['employment_history'] ?? null])</p>
    <p class="label">If yes, please give details:</p>
    <div class="box">{{ $f['employment_history_details'] ?? '' }}</div>
    <p class="meta-row"><span class="label">Do you have a CV?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['has_cv'] ?? null])</p>
    <p class="meta-row"><span class="label">If no, would you like help with creating a CV?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['want_cv_help'] ?? null])</p>

    <h2>8. Leisure, Faith &amp; Culture</h2>
    <p class="meta-row"><span class="label">Would you like help to access leisure, faith or cultural activities?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['want_leisure_help'] ?? null])</p>
    <p class="label">If yes, please give details:</p>
    <div class="box">{{ $f['leisure_details'] ?? '' }}</div>

    <h2>9. Move On</h2>
    <p class="meta-row"><span class="label">Have you had your own accommodation before?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['own_accommodation_before'] ?? null])</p>
    <p class="meta-row"><span class="label">Do you currently feel able to manage your own accommodation?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['able_manage_accommodation'] ?? null])</p>
    <p class="label">Please explain why?</p>
    <div class="box">{{ $f['manage_accommodation_why'] ?? '' }}</div>
    <p class="meta-row"><span class="label">Are you registered for social housing?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['registered_social_housing'] ?? null])</p>
    <p class="label">If yes, please give details:</p>
    <div class="box">{{ $f['social_housing_details'] ?? '' }}</div>
    <p class="meta-row"><span class="label">If no, are you interested in applying for social housing?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['interested_social_housing'] ?? null])</p>
    <p class="label">If yes, please give details:</p>
    <div class="box">{{ $f['interested_social_details'] ?? '' }}</div>

    <h2>10. Independent Living Skills</h2>
    <p class="label">Do you feel you need help with any of the following?</p>
    @foreach ($skills as $skill)
        <p class="meta-row"><span class="label">{{ $skill['label'] }}</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f[$skill['key']] ?? null])</p>
    @endforeach

    <h2>11. Risk Assessment</h2>
    @foreach ($risks as $risk)
        <p class="meta-row">
            <span class="label">{{ $risk['label'] }}@if ($risk['key'] === 'risk_other' && ! empty($f['risk_other_text'])): {{ $f['risk_other_text'] }}@endif</span>
            @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f[$risk['key']] ?? null])
        </p>
    @endforeach
    <p class="label">Please explain the identified risks in further detail:</p>
    <div class="box">{{ $f['risk_explanation'] ?? '' }}</div>

    <h2>12. Ethnic Origin</h2>
    @include('backend::admin.documents.templates.partials.option-list', [
        'options' => $defs['ethnic_origin']['options'] ?? [],
        'selected' => $f['ethnic_origin'] ?? null,
    ])
    @if (! empty($f['ethnic_origin_other']))
        <p class="meta-row"><span class="label">Any other: Please Specify</span> {{ $f['ethnic_origin_other'] }}</p>
    @endif

    <h2>13. Religion</h2>
    @include('backend::admin.documents.templates.partials.option-list', [
        'options' => $defs['religion']['options'] ?? [],
        'selected' => $f['religion'] ?? null,
    ])
    @if (! empty($f['religion_other']))
        <p class="meta-row"><span class="label">Any other: Please Specify</span> {{ $f['religion_other'] }}</p>
    @endif

    <h2>14. Sexual Orientation</h2>
    @include('backend::admin.documents.templates.partials.option-list', [
        'options' => $defs['sexual_orientation']['options'] ?? [],
        'selected' => $f['sexual_orientation'] ?? null,
    ])
    @if (! empty($f['sexual_orientation_other']))
        <p class="meta-row"><span class="label">Other:</span> {{ $f['sexual_orientation_other'] }}</p>
    @endif

    <div class="signature">
        <p class="signature-line"><span class="label">Resident Signature:</span> ______________________________</p>
        <p class="signature-line"><span class="label">Support Worker Name:</span> {{ $f['support_worker_name'] ?? '' }}</p>
        <p class="signature-line"><span class="label">Support Worker Signature:</span> ______________________________</p>
    </div>
@endsection
