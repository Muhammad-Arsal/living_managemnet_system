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

    public function label(): string
    {
        return match ($this) {
            self::ServiceChargeNotice => 'Weekly Service Charge Notice',
            self::MoveOnPlan => 'Resident Move-On Plan',
            self::WarningLetter => 'Warning Letter (RH-17)',
            self::FinalWarning => 'Final Warning for Non-Engagement (RH-18)',
            self::RiskAssessment => 'Your Risk Assessment (RH-9)',
            self::ReferralForm => 'Referral & Support Assessment Form',
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
        };
    }
}
