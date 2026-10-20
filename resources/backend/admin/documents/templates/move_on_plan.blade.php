@extends('backend::admin.documents.templates.layout')

@section('content')
    @php
        $f = $fields;
        $defs = $definitions ?? [];
        $completedMark = function (?string $value): string {
            if ($value === 'Yes') {
                return '☑ Yes  ☐ No';
            }
            if ($value === 'No') {
                return '☐ Yes  ☑ No';
            }

            return '☐ Yes  ☐ No';
        };
    @endphp

    <p class="center muted">SUPPORTED EXEMPT ACCOMMODATION (SEA)</p>
    <h1>RESIDENT MOVE-ON PLAN</h1>

    <div class="meta">
        <p class="meta-row"><span class="label">Provider Name:</span> {{ $f['provider_name'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Property Address:</span> {{ $f['property_address'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Resident Name:</span> {{ $f['resident_name'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Date of Birth:</span> {{ $f['date_of_birth'] ?? '' }}</p>
        <p class="meta-row"><span class="label">National Insurance Number:</span> {{ $f['ni_number'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Support Worker:</span> {{ $f['support_worker'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Date Move-On Plan Started:</span> {{ $f['plan_started_on'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Review Date:</span> {{ $f['review_date'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Target Move-On Date:</span> {{ $f['target_move_on_date'] ?? '' }}</p>
    </div>

    <h2>PURPOSE OF THIS PLAN</h2>
    <p>This Move-On Plan has been developed to support the resident in progressing towards greater independence and securing suitable long-term accommodation. The plan will be reviewed regularly and updated to reflect progress and changing needs.</p>

    <h2>CURRENT HOUSING SITUATION</h2>
    <p class="label">Current Accommodation Type:</p>
    @include('backend::admin.documents.templates.partials.option-list', [
        'options' => $defs['accommodation_type']['options'] ?? [],
        'selected' => $f['accommodation_type'] ?? '',
    ])
    @if (! empty($f['accommodation_type_other']))
        <p class="meta-row"><span class="label">Other:</span> {{ $f['accommodation_type_other'] }}</p>
    @endif
    <p class="meta-row"><span class="label">Date Resident Moved In:</span> {{ $f['moved_in_on'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Length of Stay to Date:</span> {{ $f['length_of_stay'] ?? '' }}</p>
    <p class="label">Reason for Placement:</p>
    <div class="box">{{ $f['reason_for_placement'] ?? '' }}</div>

    <h2>LONG-TERM HOUSING GOAL</h2>
    <p class="label">What type of accommodation is the resident aiming to move into?</p>
    @include('backend::admin.documents.templates.partials.option-list', [
        'options' => $defs['housing_goal_type']['options'] ?? [],
        'selected' => $f['housing_goal_type'] ?? '',
    ])
    @if (! empty($f['housing_goal_type_other']))
        <p class="meta-row"><span class="label">Other:</span> {{ $f['housing_goal_type_other'] }}</p>
    @endif
    <p class="label">Resident's Long-Term Goal:</p>
    <div class="box">{{ $f['long_term_goal'] ?? '' }}</div>
    <p class="label">Target Timescale:</p>
    @include('backend::admin.documents.templates.partials.option-list', [
        'options' => $defs['target_timescale']['options'] ?? [],
        'selected' => $f['target_timescale'] ?? '',
    ])
    @if (! empty($f['target_timescale_other']))
        <p class="meta-row"><span class="label">Other:</span> {{ $f['target_timescale_other'] }}</p>
    @endif

    <h2>MOVE-ON OBJECTIVES</h2>

    @php
        $objectives = [
            ['title' => '1. TENANCY SUSTAINMENT', 'goals' => 'goal_tenancy', 'actions' => 'goal_tenancy_actions', 'date' => 'goal_tenancy_target_date', 'done' => 'goal_tenancy_completed'],
            ['title' => '2. INDEPENDENT LIVING SKILLS', 'goals' => 'goal_living', 'actions' => 'goal_living_actions', 'date' => 'goal_living_target_date', 'done' => 'goal_living_completed'],
            ['title' => '3. HEALTH AND WELLBEING', 'goals' => 'goal_health', 'actions' => 'goal_health_actions', 'date' => 'goal_health_target_date', 'done' => 'goal_health_completed'],
            ['title' => '4. EMPLOYMENT, EDUCATION OR TRAINING', 'goals' => 'goal_employment', 'actions' => 'goal_employment_actions', 'date' => 'goal_employment_target_date', 'done' => 'goal_employment_completed'],
            ['title' => '5. FINANCIAL STABILITY', 'goals' => 'goal_financial', 'actions' => 'goal_financial_actions', 'date' => 'goal_financial_target_date', 'done' => 'goal_financial_completed'],
            ['title' => '6. COMMUNITY ENGAGEMENT', 'goals' => 'goal_community', 'actions' => 'goal_community_actions', 'date' => 'goal_community_target_date', 'done' => 'goal_community_completed'],
        ];
    @endphp

    @foreach ($objectives as $objective)
        <h3>{{ $objective['title'] }}</h3>
        <p class="label">Goal:</p>
        @include('backend::admin.documents.templates.partials.option-list', [
            'options' => $defs[$objective['goals']]['options'] ?? [],
            'selected' => $f[$objective['goals']] ?? [],
        ])
        <p class="label">Actions:</p>
        <div class="box">{{ $f[$objective['actions']] ?? '' }}</div>
        <p class="meta-row"><span class="label">Target Date:</span> {{ $f[$objective['date']] ?? '' }}</p>
        <p class="meta-row"><span class="label">Completed:</span> {{ $completedMark($f[$objective['done']] ?? null) }}</p>
    @endforeach

    <h2>HOUSING APPLICATIONS</h2>
    <h3>Housing Register</h3>
    <p class="label">Registered with Coventry Homefinder:</p>
    @include('backend::admin.documents.templates.partials.option-list', ['options' => $defs['homefinder_registered']['options'] ?? [], 'selected' => $f['homefinder_registered'] ?? ''])
    <p class="meta-row"><span class="label">Date Registered:</span> {{ $f['homefinder_date'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Banding:</span> {{ $f['homefinder_banding'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Reference Number:</span> {{ $f['homefinder_reference'] ?? '' }}</p>
    <h3>Private Rented Sector</h3>
    <p class="label">Actions Taken:</p>
    @include('backend::admin.documents.templates.partials.option-list', ['options' => $defs['prs_actions']['options'] ?? [], 'selected' => $f['prs_actions'] ?? []])
    <p class="label">Notes:</p>
    <div class="box">{{ $f['prs_notes'] ?? '' }}</div>

    <h2>BARRIERS TO MOVE-ON</h2>
    <p class="label">Identify barriers preventing independent living:</p>
    @include('backend::admin.documents.templates.partials.option-list', ['options' => $defs['barriers']['options'] ?? [], 'selected' => $f['barriers'] ?? []])
    @if (! empty($f['barriers_other']))
        <p class="meta-row"><span class="label">Other:</span> {{ $f['barriers_other'] }}</p>
    @endif
    <p class="label">Actions to Address Barriers:</p>
    <div class="box">{{ $f['barriers_actions'] ?? '' }}</div>

    <h2>SUPPORT PROVIDED</h2>
    <p class="label">Support to be delivered by provider:</p>
    @include('backend::admin.documents.templates.partials.option-list', ['options' => $defs['support_provided']['options'] ?? [], 'selected' => $f['support_provided'] ?? []])
    <p class="label">Frequency of Support:</p>
    @include('backend::admin.documents.templates.partials.option-list', ['options' => $defs['support_frequency']['options'] ?? [], 'selected' => $f['support_frequency'] ?? ''])
    @if (! empty($f['support_frequency_other']))
        <p class="meta-row"><span class="label">Other:</span> {{ $f['support_frequency_other'] }}</p>
    @endif

    <h2>REVIEW RECORD</h2>
    <table class="risk">
        <thead>
            <tr>
                <th style="width:18%">Review Date</th>
                <th style="width:32%">Progress Made</th>
                <th style="width:32%">Actions Agreed</th>
                <th style="width:18%">Staff Initials</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 1; $i <= 4; $i++)
                <tr>
                    <td>{{ $f['review_'.$i.'_date'] ?? '' }}</td>
                    <td>{{ $f['review_'.$i.'_progress'] ?? '' }}</td>
                    <td>{{ $f['review_'.$i.'_actions'] ?? '' }}</td>
                    <td>{{ $f['review_'.$i.'_initials'] ?? '' }}</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <h2>FINAL MOVE-ON OUTCOME</h2>
    <p class="meta-row"><span class="label">Date Resident Moved Out:</span> {{ $f['moved_out_on'] ?? '' }}</p>
    <p class="label">Move-On Destination:</p>
    @include('backend::admin.documents.templates.partials.option-list', [
        'options' => $defs['move_on_destination']['options'] ?? [],
        'selected' => $f['move_on_destination'] ?? '',
    ])
    @if (! empty($f['move_on_destination_other']))
        <p class="meta-row"><span class="label">Other:</span> {{ $f['move_on_destination_other'] }}</p>
    @endif
    <p class="label">Address (if known):</p>
    <div class="box">{{ $f['move_on_address'] ?? '' }}</div>
    <p class="label">Outcome Summary:</p>
    <div class="box">{{ $f['outcome_summary'] ?? '' }}</div>

    <h2>DECLARATION</h2>
    <p>I confirm that this Move-On Plan has been discussed with me and I understand the goals and actions agreed.</p>
    <p class="signature-line"><span class="label">Resident Name:</span> {{ $f['resident_name'] ?? '' }}</p>
    <p class="signature-line"><span class="label">Resident Signature:</span> __________________ &nbsp;&nbsp; <span class="label">Date:</span> __________</p>
    <p class="signature-line"><span class="label">Support Worker Name:</span> {{ $f['support_worker'] ?? '' }}</p>
    <p class="signature-line"><span class="label">Support Worker Signature:</span> _____________ &nbsp;&nbsp; <span class="label">Date:</span> ________</p>
    <p class="signature-line"><span class="label">Manager Signature:</span> ___________________ &nbsp;&nbsp; <span class="label">Date:</span> _________</p>
@endsection
