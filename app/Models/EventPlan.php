<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventPlan extends Model
{
    use HasFactory;

    protected $table = 'event_plannings';

    protected $fillable = [
        // Snapshot
        'event_name',
        'event_date',
        'proposer_name',
        'proposer_email',
        'proposer_phone',
        'purpose_of_event',
        'event_coordinators',
        'monetary_goal',
        'budget',
        'auxiliary_tribe_lead',
        'assigned_trustee',
        'vision_support',
        'main_purpose',
        'target_population',
        'target_population_other',
        'event_description',
        'guest_speakers_musicians',

        // Basics
        'expected_attendees',
        'setup_time',
        'start_time',
        'end_time',
        'tear_down_time',
        'onsite_rooms',
        'offsite_location',
        'is_registration_required',
        'registration_start_date',
        'registration_deadline',
        'is_participation_limited',
        'max_registrations',
        'cost_per_person',
        'payment_deadline',
        'registration_methods',
        'registration_period_start',
        'registration_period_end',
        'release_forms_needed',

        // Major Elements
        'major_elements',
        'major_elements_other',

        // Facility Needs
        'facility_contact',
        'facility_contact_means',
        'facility_needs',
        'short_term_storage_items',
        'items_to_sort',

        // Comestible Needs
        'comestible_contact',
        'comestible_contact_means',
        'comestible_needs',
        'comestible_other',

        // Technology Needs
        'technology_contact',
        'technology_contact_means',
        'technology_needs',
        'recording_type',

        // Administrative Support
        'admin_contact',
        'admin_contact_means',
        'admin_needs',
        'admin_other',

        // Personnel Needs
        'personnel_contact',
        'personnel_contact_means',
        'personnel_needs',
        'personnel_other',
        'volunteers_details',

        // Financial Needs
        'financial_contact',
        'financial_contact_means',
        'financial_needs',
        'rent_supplies_details',

        // Checklist
        'checklist_event_date',
        'checklist_registration_start',
        'checklist_publicity_start',

        // Event Planning Checklist
        'form_completed',
        'form_submitted',
        'event_approved',
        'planning_meeting_scheduled',
        'planning_meeting_completed',

        // Schedule Church Resources
        'facility_forms_submitted',

        // Public Relations
        'persuasive_verbiage_created',
        'monitor_slide_created',
        'community_flyer_created',
        'advertising_request_submitted',

        // Finances
        'contracts_negotiated',
        'contract_submitted',
        'fund_requests_submitted',
        'caterer_fund_request',
        'vendor_payment_request',

        // Secure Additional Staff
        'security_secured',
        'hospitality_secured',
        'ushers_secured',
        'comestible_secured',
        'media_secured',
        'pr_secured',
        'choir_secured',
        'volunteers_secured',
        'childcare_secured',

        // Other
        'mc_contacted',
        'backup_mc_contacted',
    ];

    protected $casts = [
        'event_date' => 'date',
        'registration_start_date' => 'date',
        'registration_deadline' => 'date',
        'payment_deadline' => 'date',
        'registration_period_start' => 'date',
        'registration_period_end' => 'date',
        'checklist_event_date' => 'date',
        'checklist_registration_start' => 'date',
        'checklist_publicity_start' => 'date',
        'setup_time' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'tear_down_time' => 'datetime',
        'monetary_goal' => 'decimal:2',
        'budget' => 'decimal:2',
        'cost_per_person' => 'decimal:2',
        'expected_attendees' => 'integer',
        'max_registrations' => 'integer',
        'is_registration_required' => 'boolean',
        'is_participation_limited' => 'boolean',
        'release_forms_needed' => 'boolean',
        'guest_speakers_musicians' => 'array',
        'main_purpose' => 'array',
        'target_population' => 'array',
        'registration_methods' => 'array',
        'major_elements' => 'array',
        'facility_needs' => 'array',
        'comestible_needs' => 'array',
        'technology_needs' => 'array',
        'recording_type' => 'array',
        'admin_needs' => 'array',
        'personnel_needs' => 'array',
        'financial_needs' => 'array',
        'status' => 'string',

        // Checklist booleans
        'form_completed' => 'boolean',
        'form_submitted' => 'boolean',
        'event_approved' => 'boolean',
        'planning_meeting_scheduled' => 'boolean',
        'planning_meeting_completed' => 'boolean',
        'facility_forms_submitted' => 'boolean',
        'persuasive_verbiage_created' => 'boolean',
        'monitor_slide_created' => 'boolean',
        'community_flyer_created' => 'boolean',
        'advertising_request_submitted' => 'boolean',
        'contracts_negotiated' => 'boolean',
        'contract_submitted' => 'boolean',
        'fund_requests_submitted' => 'boolean',
        'caterer_fund_request' => 'boolean',
        'vendor_payment_request' => 'boolean',
        'security_secured' => 'boolean',
        'hospitality_secured' => 'boolean',
        'ushers_secured' => 'boolean',
        'comestible_secured' => 'boolean',
        'media_secured' => 'boolean',
        'pr_secured' => 'boolean',
        'choir_secured' => 'boolean',
        'volunteers_secured' => 'boolean',
        'childcare_secured' => 'boolean',
        'mc_contacted' => 'boolean',
        'backup_mc_contacted' => 'boolean',
    ];

    public const STATUSES = ['pending', 'approved', 'denied'];
}
