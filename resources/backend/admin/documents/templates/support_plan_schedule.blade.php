@extends('backend::admin.documents.templates.layout')

@section('content')
    <h1>A. SUPPORT PLAN</h1>

    <p class="label">Include:</p>
    <p class="label">Tenant Goals</p>
    <p class="muted">Examples:</p>
    <ul>
        <li>maintain tenancy</li>
        <li>improve mental health</li>
        <li>engage with services</li>
        <li>budgeting skills</li>
        <li>reduce offending</li>
        <li>improve daily living</li>
    </ul>

    <h2>Support Areas</h2>
    <table class="risk">
        <thead>
            <tr>
                <th>Area</th>
                <th>Frequency</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Keywork sessions</td>
                <td>Weekly</td>
            </tr>
            <tr>
                <td>Welfare checks</td>
                <td>Daily</td>
            </tr>
            <tr>
                <td>Budgeting support</td>
                <td>Weekly</td>
            </tr>
            <tr>
                <td>GP appointments</td>
                <td>As needed</td>
            </tr>
            <tr>
                <td>Mental health support</td>
                <td>Weekly</td>
            </tr>
        </tbody>
    </table>

    <p class="label">Risk Factors</p>
    <p class="muted">Examples:</p>
    <ul>
        <li>self-neglect</li>
        <li>homelessness</li>
        <li>debt</li>
        <li>isolation</li>
        <li>relapse risk</li>
        <li>exploitation risk</li>
    </ul>

    <h1 class="mt">B. SUPPORT SCHEDULE</h1>
    <p>This proves support is structured.</p>
    <p class="muted">Example:</p>
    <table class="risk">
        <thead>
            <tr>
                <th>Day</th>
                <th>Activity</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Monday- Friday</td>
                <td>Welfare check</td>
            </tr>
            <tr>
                <td>Monday   Wednesday</td>
                <td>Keywork session</td>
            </tr>
            <tr>
                <td>Friday</td>
                <td>Budgeting support</td>
            </tr>
        </tbody>
    </table>
@endsection
