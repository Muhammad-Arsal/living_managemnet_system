@extends('backend::admin.documents.templates.layout')

@section('content')
    @php
        $f = $fields;
        $provider = $f['provider_name'] ?? '';
    @endphp

    <h1>Excluded License Agreement (RH-2)</h1>

    <p class="center muted">{{ $provider }} is an Exempt Supported Accommodation Provider, Company Number 17225272.</p>

    <h2>"You"</h2>
    <p class="meta-row"><span class="label">Name(s):</span> {{ $f['names'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Room No:</span> {{ $f['room_no'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Address:</span> {{ $f['address'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Postcode:</span> {{ $f['postcode'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Date of Birth:</span> {{ $f['date_of_birth'] ?? '' }}</p>
    <p class="meta-row"><span class="label">National Insurance Number:</span> {{ $f['ni_number'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Phone Number/ Email Address</span> {{ $f['phone_email'] ?? '' }}</p>

    <p class="muted">License to occupy with any other persons {{ $provider }} (see below) nominates:</p>
    <p class="meta-row"><span class="label">License start date:</span> {{ $f['license_start_date'] ?? '' }}</p>
    <p class="muted">On the terms and conditions in this agreement (the "License") which begins on: {{ $f['license_begins_on'] ?? '' }}</p>
    <p class="muted">and is for a week and thereafter is weekly until it is ended under the terms of this License.</p>

    <h2>What it Costs to Stay at the Property</h2>
    <h3>License Charge payments</h3>
    <p class="muted">You must pay a License charge to stay at the Property. It is made up of an "Weekly basic Rent" plus a "Service Charge" plus "Other Charges" (which are paid directly to your Managing Agent) added together which at the start date of this License are:</p>
    <p class="meta-row"><span class="label">Total Core Rent:</span> £: {{ $f['total_core_rent'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Total Eligible Services Charges:</span> £: {{ $f['total_eligible_service_charges'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Total Eligible Service Charge &amp; Rent for HB:</span> £: {{ $f['total_eligible_service_charge_and_rent'] ?? '' }}</p>
    <p class="muted">The License Charge is due weekly in advance and is payable on a Monday. Please note that the above amount is subject to change.</p>
    <p class="muted">{{ $provider }} manage the above property.</p>

    <h2>1. The Contents</h2>
    <p class="muted">{{ $provider }} will provide You with furniture, furnishings, fixtures and other items (the "Contents").</p>

    <h2>2. General Terms</h2>
    <h3>PURPOSE OF THE PROPERTY</h3>
    <p class="muted">Your Room is part of a Property which provides supported accommodation to individuals who are unable to obtain permanent accommodation and/ or require support to manage their License.</p>
    <p class="muted">For this reason, you do not have exclusive possession of your Room or any part of the Property. Staff have to be able to check on occupiers of the Property, including you, at all times to see that occupiers are safe, have not hurt themselves or anyone else, are not under the influence of illegal drugs or alcohol.</p>

    <h2>3. You and {{ $provider }}</h2>
    <p class="muted">Your Room is in a building or buildings managed by {{ $provider }} (the "Building"), which is part of the Property and the Property includes shared communal areas such as (but not limited to) any access paths, stairways, lifts, communal gardens, balconies, landings, wash rooms and parking areas (the "Communal Areas").</p>
    <p class="muted">This License does not confer exclusive possession. {{ $provider }} and its staff have the absolute right to enter your Room at any time without notice and will exercise this right with consideration for your wellbeing and/or that of any other occupiers.</p>
    <p class="muted">Nothing in this License shall give any benefit to any other person or the right to enforce any term of this License and You and/or {{ $provider }} may vary or end this License without being required to obtain the consent of any other person.</p>

    <h2>4. Changes to the License Charge</h2>
    <p class="muted">Unless agreed in advance, the License Charge may be increased or decreased by {{ $provider }} giving You not less than 7 days' notice of the increase or decrease in writing. The new License Charge will be the License Charge stated in the notice and becomes payable by You on the date stated in the notice.</p>
    <p class="muted">If You do not want to continue the License at the new License Charge, you can end the License by giving {{ $provider }} 7 days' notice in writing to end the License (ending on or before the new License Charge takes effect).</p>

    <h2>5. Notices</h2>
    <h3>To serve a notice on You</h3>
    <p class="muted">Any notice which {{ $provider }} serve on you will be validly served if it is addressed to You and posted or delivered to Your Room.</p>
    <h3>To serve a notice on {{ $provider }}</h3>
    <p class="muted">You can serve any notice on {{ $provider }} if You send or deliver it to the Managing Agent at your respective Property.</p>

    <h2>6. {{ $provider }} Obligations</h2>
    <p class="muted">{{ $provider }} Agree</p>
    <p class="muted"><span class="label">Possession: Letting You into Your Room</span><br>To allow You to occupy Your Room.</p>
    <p class="muted"><span class="label">Insurance: What {{ $provider }} will insure</span><br>To insure the structure of the Property and any fixtures and fittings owned by the {{ $provider }} (but not Your personal belongings) against any risks (for example fire) {{ $provider }} reasonably believes it needs to.</p>

    <h2>7. What You agree to do whilst You are at the Property</h2>
    <p class="muted"><span class="label">YOU AGREE:</span></p>
    <p class="muted"><span class="label">Possession: Living in Your Room</span><br>To move into your Room at the start of the License and to accept and co-operate with {{ $provider }} staff and the rules of the Property.</p>
    <p class="muted">You must continue to reside permanently at the property. If you go missing for a maximum of 7 days without informing staff , we assume you no longer need the property and re-let your room.</p>
    <p class="muted"><span class="label">Charges</span><br>To pay the License Charge when it is due.</p>
    <p class="muted"><span class="label">Property Rules</span><br>To comply with the Property Rules. A copy of the Property Rules is at Schedule 1, however these can be changed by {{ $provider }} from time to time and you will have to abide by the new Property Rules as and when they are given to you. You agree that failure to abide by Property rules may result in termination of your License agreement with immediate effect.</p>
    <p class="muted"><span class="label">Outgoings</span><br>To pay all outgoings You are responsible for.</p>
    <p class="muted"><span class="label">Lodgers, visitors and sub-letting</span><br>Not to take in a lodger or to part with possession or sub-let the whole, or any part of Your Room.<br>Not to allow anyone who is not authorised by {{ $provider }} to live or sleep in Your Room with You. You cannot have anyone stay with You at the Property overnight.</p>
    <p class="muted"><span class="label">Alterations</span><br>Not to remove or make any alterations to Your Room, the Property or any fixtures or fittings or the Contents belonging to the {{ $provider }}.</p>
    <p class="muted"><span class="label">Use of Your Room and the Property</span><br>That neither You nor Your visitors shall operate a business or any other commercial activity in Your Room or in the Building.<br>That neither You nor Your visitors shall carry out any repairs or servicing of any vehicle(s) in Your Room or in the Building.<br>That You will not be under the influence of illegal drugs, not consume alcohol, not smoke in the Property, nor use the Property for any illegal and/or unlawful activity.<br>That You will not do anything to encourage or allow Your visitors to be under the influence of illegal drugs, consume alcohol or smoke, in the Property, nor use the Property for any illegal and/or unlawful activity.</p>
    <p class="muted"><span class="label">Graffiti</span><br>That You and/or Your visitors will not graffiti Your Room or the Building, the Property and/or anything belonging to any person living, visiting, or undertaking a lawful activity in the building.</p>
    <p class="muted"><span class="label">Pets</span><br>Not to keep any animal, bird, fish, reptile or insect or similar in Your Room or the Building without first getting {{ $provider }} consent in writing.</p>
    <p class="muted"><span class="label">Vehicles</span><br>Not to park any vehicle in the Communal Areas of the Property without consent from {{ $provider }} which can be withdrawn at any time without notice</p>
    <p class="muted"><span class="label">Internal decoration: Treating Your Room with respect</span><br>To keep Your Room in a good, clean and tidy condition.</p>
    <p class="muted"><span class="label">Damage or Interference</span><br>To take all reasonable steps to prevent damage to Your Room and/or the Property and the Contents including security or safety equipment (such as fire and smoke alarms, any equipment for putting out fires, door entry systems and closed-circuit systems)<br>If You or Your visitors damage the Property, the Building or the Contents then You will pay the Cost of repairing or replacing what has been damaged within 7 days of You receiving an invoice for those costs.</p>
    <p class="muted"><span class="label">Health and Safety</span><br>Not to keep anything in Your Room or in the Building which may or is likely to cause an explosion.<br>To protect Your and other residents' safety and security by:<br>Closing external, safety and fire doors in and to the Building; and<br>Controlling and not lending out any key or fob to any Communal Areas<br>That You and/or Your visitors will not obstruct or keep or leave rubbish or dangerous materials or belongings which could constitute a Health or Fire Safety risk in the Property.<br>{{ $provider }} are entitled to remove and dispose of any rubbish, dangerous material or belongings which could constitute a Health or Fire Safety risk in the Property.</p>

    <h2>8. How to end Your License</h2>
    <p class="muted">Unless You are ending the License because You do not want to pay a new License Charge, to:<br>Give the {{ $provider }} at least 7 days' notice, ending on a Sunday, that You want to end this License; and<br>If You leave before 7 days' notice has been given, to pay the License Charge as if You had given 7 days' notice</p>

    <h2>9. Moving Out</h2>
    <p class="muted">That on the date on which this License ends:<br>You will move out taking Your belongings with You.<br>You will return the keys (and where applicable all the door entry fobs) to Your Room and the Property to {{ $provider }} by 10:00am.<br>You will leave Your Room and {{ $provider }} fixtures and fittings and the Contents in a clean and good condition.<br>You will leave the Contents in Your Room.<br>If you do not return the keys (and where applicable all the door entry fobs) by 10.00am on the day this License ends You will pay to {{ $provider }} an amount equivalent to the License Charge due for the last week of the License within 7 days of the housing requesting payment from You.<br>That {{ $provider }} is not responsible for anything left in Your Room after this License ends.</p>

    <h2>10. How {{ $provider }} can end the License</h2>
    <p class="muted">{{ $provider }} can end the License at any time and without reason by giving You a written notice.<br>{{ $provider }} will give You reasonable notice to end the License.<br>The License may be ended if:<br>You fail to pay the License Charge when it is due.<br>You fail to abide by the terms of this License.<br>You and/or Your visitors have caused serious and/or persistent nuisance to other people who live in the Property, {{ $provider }} staff or any visitors to the Property.<br>You are offered alternative accommodation.<br>Your continuing occupancy would /could pose a threat to Yourself, staff or other people who use the Property.<br>You need greater support and services than the Property or Service is intended to provide.</p>

    <h2>11. Complaints Procedure</h2>
    <p class="muted">If You think {{ $provider }} has not done anything it has agreed to do, you can first make a complaint to {{ $provider }} using its complaints procedure.<br>If you are not satisfied with how {{ $provider }} have dealt with Your complaint, You can then complain to the Housing Ombudsman Service. Please speak with your support worker and they will explain how to complain and how to go to the Housing Ombudsman Service.<br>You can also get advice from a Citizens Advice Bureau, Housing Advice Centre, Law Centre or Solicitor.</p>

    <h2>12. Support Arrangements</h2>
    <p class="muted">You have been issued a License because you qualify for supported accommodation. As part of your License condition, you are required to attend support sessions with your allocated support worker in accordance with your support plan. Failure to attend support sessions or non-engagement with the support service will put you in breach of your License agreement and may lead to the termination of your License agreement.</p>

    <h2>13. Utilities</h2>
    <p class="muted">If you are residing in a room within a HMO, you will not be liable to pay for the utilities yourself.<br>If you are residing in a self contained flat, you will be liable to pay for the utilities. This includes: electric, gas, water, council tax.</p>
    <p class="meta-row"><span class="label">Electric move in meter read:</span> {{ $f['electric_meter'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Gas move in meter read:</span> {{ $f['gas_meter'] ?? '' }}</p>
    <p class="meta-row"><span class="label">Water move in meter read:</span> {{ $f['water_meter'] ?? '' }}</p>

    <h2>Schedule 1 – Property Rules</h2>
    <p class="muted">1. You are not to bring weapons of any kind into the premises. This includes regular and ornamental/toy weapons. If You bring dangerous weapons on site, we have the right to confiscate the weapon, call the police and evict You.</p>
    <p class="muted">2. Violence will not be tolerated at the property. If You are involved in any violence on site, you may be evicted.</p>
    <p class="muted">3. No illicit substances are to be brought on site. If there is any evidence of such substances Police will be called immediately and You will be evicted.</p>
    <p class="muted">4. No visitors are allowed on site between the hours of 9pm and 9am.</p>
    <p class="muted">5. {{ $provider }} are not responsible for any theft, loss or damage to any personal effects.</p>
    <p class="muted">6. Once You leave the property, we are not responsible for Your belongings. Anything left on site when You leave will be given to any customer in need or disposed of.</p>
    <p class="muted">7. If the fire alarm sounds, you must evacuate the building immediately following the fire evacuation procedures and assemble in the designated area.</p>
    <p class="muted">8. No smoking is allowed inside the property in any area. Smoking is strictly outside the grounds of the property.</p>
    <p class="muted">9. No loud music or TV which causes disturbance to other occupants or neighbours is allowed at any time.</p>
    <p class="muted">10. You must keep your room clean and tidy-if u use the vacuum provided on site, you must place it back in the holding area.</p>
    <p class="muted">11. No hanging of pictures or frames or TVs on the Walls is allowed. (unless written permission is granted by {{ $provider }}.</p>
    <p class="muted">12. No pets are allowed on site at any time.</p>
    <p class="muted">13. Any Disturbance to neighbours is not allowed please remain responsible, as this may lead to eviction</p>
    <p class="muted">14. Drinking Alcohol irresponsibly, disorderly actions and any Antisocial behaviour is not allowed and will result in eviction.</p>
    <p class="muted">15. Any deliberate damage to property discovered or found may result in eviction.</p>
    <p class="muted">16. Failure to Pay the Accommodation Service Charge will result in eviction.</p>
    <p class="muted">17. No Alcohol is allowed in Communal Areas Strictly. Failure to respect this will result in eviction.</p>

    <p class="muted mt">Before signing this License, I have been requested to read, and understand the terms in this License, which includes the Property Rules set out in Schedule 1, I also agree to engage on a regular basis with my managing agent and understand that I must abide by the rules set above as mandatory whilst living in {{ $provider }}. By signing the below, I confirm I have done so, and I accept the accommodation.</p>

    <div class="signature">
        <p class="signature-line"><span class="label">Print Name by the Licensee:</span> {{ $f['licensee_print_name'] ?? '' }}</p>
        <p class="signature-line"><span class="label">Dated:</span> {{ $f['licensee_dated_1'] ?? '' }}</p>
        <p class="signature-line"><span class="label">Signed by the Licensee:</span> ______________________________</p>
        <p class="signature-line"><span class="label">Dated:</span> {{ $f['licensee_dated_2'] ?? '' }}</p>
        <p class="signature-line"><span class="label">Print Name on behalf of {{ $provider }}</span> {{ $f['provider_signatory_name'] ?? '' }}</p>
        <p class="signature-line"><span class="label">Dated:</span> {{ $f['provider_dated_1'] ?? '' }}</p>
        <p class="signature-line"><span class="label">Signed on behalf of {{ $provider }}</span> ______________________________</p>
        <p class="signature-line"><span class="label">Dated:</span> {{ $f['provider_dated_2'] ?? '' }}</p>
    </div>

    <p class="center mt"><span class="label">END OF AGREEMENT</span></p>
@endsection
