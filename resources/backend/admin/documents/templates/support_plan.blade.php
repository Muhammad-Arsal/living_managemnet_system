@extends('backend::admin.documents.templates.layout')

@section('content')
    @php
        $f = $fields;
        $defs = $definitions ?? [];
        $goalBankOptions = $defs['goal_bank']['options'] ?? [];
        $goalBankSelected = $f['goal_bank'] ?? [];
        $goalBankSubset = function (array $keys) use ($goalBankOptions): array {
            $subset = [];
            foreach ($keys as $key) {
                if (isset($goalBankOptions[$key])) {
                    $subset[$key] = $goalBankOptions[$key];
                }
            }

            return $subset;
        };
        $goalTerms = [
            'short' => 'SHORT TERM GOALS',
            'medium' => 'MEDIUM TERM GOALS',
            'long' => 'LONG TERM GOALS',
        ];
    @endphp

    <h1>Your Support Plan (RH-11)</h1>

    <div class="meta">
        <p class="meta-row"><span class="label">Name of Resident:</span> {{ $f['resident_name'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Address:</span> {{ $f['address'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Telephone Number:</span> {{ $f['telephone_number'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Email Address:</span> {{ $f['email'] ?? '' }}</p>
        <p class="meta-row"><span class="label">Name of Support Worker:</span> {{ $f['support_worker'] ?? '' }}</p>
    </div>

    <div class="signature">
        <p class="signature-line"><span class="label">Resident Signature</span> ______________________________</p>
        <p class="meta-row"><span class="label">Date:</span> {{ $f['assessment_date'] ?? '' }}</p>
    </div>

    <h2>Key Review Dates</h2>
    <p class="meta-row"><span class="label">3 Monthly Review:</span> {{ $f['review_3_month'] ?? '' }}</p>
    <p class="meta-row"><span class="label">6 Monthly Review:</span> {{ $f['review_6_month'] ?? '' }}</p>
    <p class="meta-row"><span class="label">9 Monthly Review:</span> {{ $f['review_9_month'] ?? '' }}</p>
    <p class="meta-row"><span class="label">12 Monthly Review:</span> {{ $f['review_12_month'] ?? '' }}</p>

    <h2>Welcome to your Support Plan</h2>
    <p>This plan will help you identify what support you require and help us make sure the support you receive is relevant to your needs.</p>
    <p>At the end of this meeting you should know:</p>
    <p>What support can be offered to you and what support you feel is important to you.</p>
    <p>What your support goals are and an understanding of how they will be achieved</p>
    <p>Who your support worker is, and how often you will meet.</p>
    <p>For us to provide the best support possible to you, we need to know what you would like to achieve so we can agree on an action plan for us to follow. Our goal is to support you to be able to live independently.</p>

    <p class="label">What do you want to achieve?</p>
    <div class="box">{{ $f['what_achieve'] ?? '' }}</div>

    <p class="label">What do you feel is stopping you from achieving this?</p>
    <div class="box">{{ $f['what_stopping'] ?? '' }}</div>

    <p>Now we have identified some of the issues you need help with we can look at how we can overcome these while working together.</p>
    <p>Look at the support goals on the next page and choose at least 5 you feel are important to you.</p>
    <p>You will receive support to achieve these goals. When a goal is achieved, a new goal should be chosen.</p>

    <h2>SHORT TERM</h2>
    @include('backend::admin.documents.templates.partials.option-list', [
        'options' => $goalBankSubset([
            'st_access_healthcare',
            'st_open_bank',
            'st_maintain_hygiene',
            'st_maximize_income',
            'st_access_leisure',
        ]),
        'selected' => $goalBankSelected,
    ])

    <h2>MEDIUM TERM</h2>
    @include('backend::admin.documents.templates.partials.option-list', [
        'options' => $goalBankSubset([
            'mt_reduce_debt',
            'mt_volunteering',
            'mt_support_networks',
            'mt_education',
            'mt_offending',
        ]),
        'selected' => $goalBankSelected,
    ])

    <h2>LONG TERM</h2>
    @include('backend::admin.documents.templates.partials.option-list', [
        'options' => $goalBankSubset([
            'lt_independent_accommodation',
            'lt_substance',
            'lt_employment',
            'lt_maintain_accommodation',
            'lt_physical_health',
            'lt_mental_health',
        ]),
        'selected' => $goalBankSelected,
    ])

    <p>In addition to the above list, developing independent living skills can fall into any of the timescales above.</p>
    <p>This will depend on your individual circumstances &amp; abilities.</p>
    <p>Developing independent living skills can include any of the support needs listed below:</p>
    @include('backend::admin.documents.templates.partials.option-list', [
        'options' => $goalBankSubset([
            'skill_budget',
            'skill_housekeeping',
            'skill_laundry',
            'skill_cook',
            'skill_shop',
            'skill_asb',
            'skill_communicate',
            'skill_organise',
            'skill_perspective',
        ]),
        'selected' => $goalBankSelected,
    ])

    <p>We have put the goals into time frames of short, medium and long term.</p>
    <p>However, we understand different people work at different paces.</p>
    <p>Please put your chosen goals into the categories on the next page. If you think it will take you more or less time to complete the goal, please add it to the time frame that better suits you.</p>

    @foreach ($goalTerms as $prefix => $heading)
        <h2>{{ $heading }}</h2>
        @for ($i = 1; $i <= 4; $i++)
            <div class="section mt">
                <p class="meta-row"><span class="label">What is the goal?</span> {{ $f[$prefix.'_'.$i.'_goal'] ?? '' }}</p>
                <p class="meta-row"><span class="label">How will this be achieved?</span> {{ $f[$prefix.'_'.$i.'_how'] ?? '' }}</p>
                <p class="meta-row"><span class="label">Target achievement date:</span> {{ $f[$prefix.'_'.$i.'_target_date'] ?? '' }}</p>
                <p class="signature-line"><span class="label">Resident signature:</span> ______________________________</p>
                <p class="meta-row"><span class="label">Date completed:</span> {{ $f[$prefix.'_'.$i.'_date_completed'] ?? '' }}</p>
                <p class="signature-line"><span class="label">Resident signature:</span> ______________________________</p>
            </div>
        @endfor
    @endforeach

    <p>Now we know what your goals are and how we will work together to achieve them.</p>
    <p>We will:</p>
    <p>Meet in person 3 hours a week to work on the goals chosen.</p>
    <p>Communicate with each other either by phone, text, WhatsApp or email.</p>
    <p>Be open and honest with each other.</p>
    <p>Meet every 3 months to review the support and decide if you still need to reside in supported accommodation.</p>
    <p>It can sometimes be difficult to engage with a new service and there may be times when you do not want to take part. To help you, we need to understand reasons why this might happen.</p>

    <p class="meta-row"><span class="label">Have you ever lived in supported accommodation before?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['lived_supported_before'] ?? null])</p>
    <p class="meta-row"><span class="label">Did you engage?</span> @include('backend::admin.documents.templates.partials.yes-no', ['value' => $f['did_engage'] ?? null])</p>
    <p class="label">If no, please explain why?</p>
    <div class="box">{{ $f['engage_no_reason'] ?? '' }}</div>

    <p>We value your opinion and feedback to ensure that we are delivering the best service possible.</p>
    <p>If you have any concerns or compliments about the support you are receiving. Please provide feedback to us.</p>
    <div class="box">{{ $f['feedback'] ?? '' }}</div>

    <p>Now we have completed your support plan, you should now know:</p>
    <p>What support can be offered to you and what support you feel is important to you.</p>
    <p>What your support goals are and an understanding of how they will be achieved</p>
    <p>Who your support worker is, and how often you will meet.</p>
    <p>I confirm that I now understand the above. I agree to engage in weekly support sessions to work towards my goal of living independently.</p>

    <div class="signature">
        <p class="signature-line"><span class="label">Resident Signature:</span> ______________________________</p>
        <p class="meta-row"><span class="label">Support Worker Name:</span> {{ $f['support_worker'] ?? '' }}</p>
        <p class="signature-line"><span class="label">Support Worker Signature:</span> ______________________________</p>
    </div>
@endsection
