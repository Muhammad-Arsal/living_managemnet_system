<?php

namespace App\Enums;

enum DocumentTemplateKey: string
{
    // RH-numbered templates first (ascending), then unnumbered forms.
    case InitialNeedsAssessment = 'initial_needs_assessment';
    case ExcludedLicenseAgreement = 'excluded_license_agreement';
    case HousingBenefitAuthorisation = 'housing_benefit_authorisation';
    case NilIncomeForm = 'nil_income_form';
    case RiskAssessment = 'risk_assessment';
    case StaffRiskAssessment = 'staff_risk_assessment';
    case SupportPlan = 'support_plan';
    case ResidentReviewForm = 'resident_review_form';
    case IncidentReport = 'incident_report';
    case MissedMeetingLetter = 'missed_meeting_letter';
    case WarningLetter = 'warning_letter';
    case FinalWarning = 'final_warning';
    case KnowingYourRights = 'knowing_your_rights';
    case ResidentExitForm = 'resident_exit_form';
    case ListOfInventory = 'list_of_inventory';
    case IdChecklist = 'id_checklist';
    case ReferralForm = 'referral_form';
    case MoveOnPlan = 'move_on_plan';
    case ServiceChargeNotice = 'service_charge_notice';

    public function label(): string
    {
        return match ($this) {
            self::InitialNeedsAssessment => 'Initial Needs Assessment (RH-1)',
            self::ExcludedLicenseAgreement => 'Excluded License Agreement (RH-2)',
            self::HousingBenefitAuthorisation => 'Housing Benefit Authorisation Form (RH-4)',
            self::NilIncomeForm => 'Nil Income Form (RH-6)',
            self::RiskAssessment => 'Your Risk Assessment (RH-9)',
            self::StaffRiskAssessment => 'Risk Assessment for Staff Eyes Only (RH-10)',
            self::SupportPlan => 'Your Support Plan (RH-11)',
            self::ResidentReviewForm => 'Resident Review Form (RH-12)',
            self::IncidentReport => 'Incident Form (RH-15)',
            self::MissedMeetingLetter => 'Missed Meeting Letter (RH-16)',
            self::WarningLetter => 'Warning Letter (RH-17)',
            self::FinalWarning => 'Final Warning for Non-Engagement (RH-18)',
            self::KnowingYourRights => 'Knowing Your Rights (RH-19)',
            self::ResidentExitForm => 'Resident Exit Form (RH-20)',
            self::ListOfInventory => 'List of Inventory (RH-21)',
            self::IdChecklist => 'ID Check List (RH-22)',
            self::ReferralForm => 'Referral & Support Assessment Form',
            self::MoveOnPlan => 'Resident Move-On Plan',
            self::ServiceChargeNotice => 'Weekly Service Charge Notice',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::InitialNeedsAssessment => 'Full initial needs assessment covering health, risk, finances, skills, and equality data.',
            self::ExcludedLicenseAgreement => 'Excluded license agreement with charges, property rules, and signatures.',
            self::HousingBenefitAuthorisation => 'Authorisation for Coventry City Council to discuss Housing Benefit with the provider.',
            self::NilIncomeForm => 'Nil income declaration with support methods and savings confirmation.',
            self::RiskAssessment => 'Resident risk assessment and support-plan scores across key risk areas.',
            self::StaffRiskAssessment => 'Staff-only risk management plans where resident scores are disputed or sensitive.',
            self::SupportPlan => 'Resident support plan with goals, review dates, and engagement commitments.',
            self::ResidentReviewForm => 'Resident support review covering goals, contact frequency, and next steps.',
            self::IncidentReport => 'Incident report with details, witnesses, emergency services, and follow-up.',
            self::MissedMeetingLetter => 'Letter confirming a missed support session and rearranged appointment.',
            self::WarningLetter => 'Written warning for licence agreement breach with meeting details.',
            self::FinalWarning => 'Final warning for missed support sessions with meeting details.',
            self::KnowingYourRights => 'Resident statement of understanding for rights and commitments.',
            self::ResidentExitForm => 'Exit form covering leave reason, belongings, benefits, and follow-up.',
            self::ListOfInventory => 'Property inventory list with item, description, and quantity.',
            self::IdChecklist => 'ID and supporting-document checklist for move-in.',
            self::ReferralForm => 'Referral and support assessment covering needs, health, and consent.',
            self::MoveOnPlan => 'Supported exempt accommodation move-on plan with goals and reviews.',
            self::ServiceChargeNotice => 'Notice of weekly service charge, payment schedule, and bank details.',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::InitialNeedsAssessment => 'bx:bx-user-check',
            self::ExcludedLicenseAgreement => 'bx:bx-file',
            self::HousingBenefitAuthorisation => 'bx:bx-home-alt',
            self::NilIncomeForm => 'bx:bx-wallet',
            self::RiskAssessment => 'bx:bx-shield-quarter',
            self::StaffRiskAssessment => 'bx:bx-show-alt',
            self::SupportPlan => 'bx:bx-target-lock',
            self::ResidentReviewForm => 'bx:bx-revision',
            self::IncidentReport => 'bx:bx-first-aid',
            self::MissedMeetingLetter => 'bx:bx-calendar-x',
            self::WarningLetter => 'bx:bx-error',
            self::FinalWarning => 'bx:bx-error-circle',
            self::KnowingYourRights => 'bx:bx-check-shield',
            self::ResidentExitForm => 'bx:bx-log-out',
            self::ListOfInventory => 'bx:bx-list-ul',
            self::IdChecklist => 'bx:bx-id-card',
            self::ReferralForm => 'bx:bx-clipboard',
            self::MoveOnPlan => 'bx:bx-map',
            self::ServiceChargeNotice => 'bx:bx-money',
        };
    }

    public function view(): string
    {
        return 'backend::admin.documents.templates.'.$this->value;
    }

    public function filenamePrefix(): string
    {
        return match ($this) {
            self::InitialNeedsAssessment => 'Initial-Needs-Assessment-RH-1',
            self::ExcludedLicenseAgreement => 'Excluded-License-Agreement-RH-2',
            self::HousingBenefitAuthorisation => 'Housing-Benefit-Authorisation-RH-4',
            self::NilIncomeForm => 'Nil-Income-Form-RH-6',
            self::RiskAssessment => 'Risk-Assessment-RH-9',
            self::StaffRiskAssessment => 'Staff-Risk-Assessment-RH-10',
            self::SupportPlan => 'Support-Plan-RH-11',
            self::ResidentReviewForm => 'Resident-Review-Form-RH-12',
            self::IncidentReport => 'Incident-Report-RH-15',
            self::MissedMeetingLetter => 'Missed-Meeting-Letter-RH-16',
            self::WarningLetter => 'Warning-Letter-RH-17',
            self::FinalWarning => 'Final-Warning-RH-18',
            self::KnowingYourRights => 'Knowing-Your-Rights-RH-19',
            self::ResidentExitForm => 'Resident-Exit-Form-RH-20',
            self::ListOfInventory => 'List-of-Inventory-RH-21',
            self::IdChecklist => 'ID-Checklist-RH-22',
            self::ReferralForm => 'Referral-Support-Assessment',
            self::MoveOnPlan => 'Move-On-Plan',
            self::ServiceChargeNotice => 'Weekly-Service-Charge-Notice',
        };
    }
}
