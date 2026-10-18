<?php

namespace App\Enums;

enum DocumentTemplateKey: string
{
    case ServiceChargeNotice = 'service_charge_notice';
    case MoveOnPlan = 'move_on_plan';
    case WarningLetter = 'warning_letter';
    case FinalWarning = 'final_warning';
    case RiskAssessment = 'risk_assessment';
    case ReferralForm = 'referral_form';
    case IncidentReport = 'incident_report';
    case MissedMeetingLetter = 'missed_meeting_letter';
    case KnowingYourRights = 'knowing_your_rights';
    case ResidentExitForm = 'resident_exit_form';

    public function label(): string
    {
        return match ($this) {
            self::ServiceChargeNotice => 'Weekly Service Charge Notice',
            self::MoveOnPlan => 'Resident Move-On Plan',
            self::WarningLetter => 'Warning Letter (RH-17)',
            self::FinalWarning => 'Final Warning for Non-Engagement (RH-18)',
            self::RiskAssessment => 'Your Risk Assessment (RH-9)',
            self::ReferralForm => 'Referral & Support Assessment Form',
            self::IncidentReport => 'Incident Form (RH-15)',
            self::MissedMeetingLetter => 'Missed Meeting Letter (RH-16)',
            self::KnowingYourRights => 'Knowing Your Rights (RH-19)',
            self::ResidentExitForm => 'Resident Exit Form (RH-20)',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ServiceChargeNotice => 'Notice of weekly service charge, payment schedule, and bank details.',
            self::MoveOnPlan => 'Supported exempt accommodation move-on plan with goals and reviews.',
            self::WarningLetter => 'Written warning for licence agreement breach with meeting details.',
            self::FinalWarning => 'Final warning for missed support sessions with meeting details.',
            self::RiskAssessment => 'Resident risk assessment and support-plan scores across key risk areas.',
            self::ReferralForm => 'Referral and support assessment covering needs, health, and consent.',
            self::IncidentReport => 'Incident report with details, witnesses, emergency services, and follow-up.',
            self::MissedMeetingLetter => 'Letter confirming a missed support session and rearranged appointment.',
            self::KnowingYourRights => 'Resident statement of understanding for rights and commitments.',
            self::ResidentExitForm => 'Exit form covering leave reason, belongings, benefits, and follow-up.',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::ServiceChargeNotice => 'bx:bx-money',
            self::MoveOnPlan => 'bx:bx-map',
            self::WarningLetter => 'bx:bx-error',
            self::FinalWarning => 'bx:bx-error-circle',
            self::RiskAssessment => 'bx:bx-shield-quarter',
            self::ReferralForm => 'bx:bx-clipboard',
            self::IncidentReport => 'bx:bx-first-aid',
            self::MissedMeetingLetter => 'bx:bx-calendar-x',
            self::KnowingYourRights => 'bx:bx-check-shield',
            self::ResidentExitForm => 'bx:bx-log-out',
        };
    }

    public function view(): string
    {
        return 'backend::admin.documents.templates.'.$this->value;
    }

    public function filenamePrefix(): string
    {
        return match ($this) {
            self::ServiceChargeNotice => 'Weekly-Service-Charge-Notice',
            self::MoveOnPlan => 'Move-On-Plan',
            self::WarningLetter => 'Warning-Letter-RH-17',
            self::FinalWarning => 'Final-Warning-RH-18',
            self::RiskAssessment => 'Risk-Assessment-RH-9',
            self::ReferralForm => 'Referral-Support-Assessment',
            self::IncidentReport => 'Incident-Report-RH-15',
            self::MissedMeetingLetter => 'Missed-Meeting-Letter-RH-16',
            self::KnowingYourRights => 'Knowing-Your-Rights-RH-19',
            self::ResidentExitForm => 'Resident-Exit-Form-RH-20',
        };
    }
}
