<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('event_plannings', function (Blueprint $table) {
            $table->id();

            // Snapshot
            $table->date('event_date');
            $table->string('event_name');
            $table->string('purpose_of_event');
            $table->string('event_coordinators');
            $table->string('proposer_name')->nullable();
            $table->string('proposer_email')->nullable();
            $table->string('proposer_phone')->nullable();
            $table->decimal('monetary_goal', 10, 2)->nullable();
            $table->decimal('budget', 10, 2);
            $table->string('auxiliary_tribe_lead')->nullable();
            $table->string('assigned_trustee')->nullable();
            $table->text('vision_support');
            $table->json('main_purpose');
            $table->json('target_population')->nullable();
            $table->string('target_population_other')->nullable();
            $table->text('event_description');
            $table->json('guest_speakers_musicians')->nullable();

            // Basics
            $table->integer('expected_attendees');
            $table->time('setup_time');
            $table->time('start_time');
            $table->time('end_time');
            $table->time('tear_down_time');
            $table->string('onsite_rooms')->nullable();
            $table->string('offsite_location')->nullable();
            $table->boolean('is_registration_required')->default(false);
            $table->date('registration_start_date')->nullable();
            $table->date('registration_deadline')->nullable();
            $table->boolean('is_participation_limited')->default(false);
            $table->integer('max_registrations')->nullable();
            $table->decimal('cost_per_person', 10, 2)->nullable();
            $table->date('payment_deadline')->nullable();
            $table->json('registration_methods')->nullable();
            $table->date('registration_period_start')->nullable();
            $table->date('registration_period_end')->nullable();
            $table->boolean('release_forms_needed')->default(false);

            // Major Elements
            $table->json('major_elements')->nullable();
            $table->string('major_elements_other')->nullable();

            // Facility Needs
            $table->string('facility_contact')->nullable();
            $table->string('facility_contact_means')->nullable();
            $table->json('facility_needs')->nullable();
            $table->text('short_term_storage_items')->nullable();
            $table->text('items_to_sort')->nullable();

            // Comestible Needs
            $table->string('comestible_contact')->nullable();
            $table->string('comestible_contact_means')->nullable();
            $table->json('comestible_needs')->nullable();
            $table->string('comestible_other')->nullable();

            // Technology Needs
            $table->string('technology_contact')->nullable();
            $table->string('technology_contact_means')->nullable();
            $table->json('technology_needs')->nullable();
            $table->json('recording_type')->nullable();

            // Administrative Support
            $table->string('admin_contact')->nullable();
            $table->string('admin_contact_means')->nullable();
            $table->json('admin_needs')->nullable();
            $table->string('admin_other')->nullable();

            // Personnel Needs
            $table->string('personnel_contact')->nullable();
            $table->string('personnel_contact_means')->nullable();
            $table->json('personnel_needs')->nullable();
            $table->string('personnel_other')->nullable();
            $table->text('volunteers_details')->nullable();

            // Financial Needs
            $table->string('financial_contact')->nullable();
            $table->string('financial_contact_means')->nullable();
            $table->json('financial_needs')->nullable();
            $table->text('rent_supplies_details')->nullable();

            // Checklist
            $table->date('checklist_event_date')->nullable();
            $table->date('checklist_registration_start')->nullable();
            $table->date('checklist_publicity_start')->nullable();

            // Event Planning Checklist
            $table->boolean('form_completed')->default(false);
            $table->boolean('form_submitted')->default(false);
            $table->boolean('event_approved')->default(false);
            $table->boolean('planning_meeting_scheduled')->default(false);
            $table->boolean('planning_meeting_completed')->default(false);

            // Schedule Church Resources
            $table->boolean('facility_forms_submitted')->default(false);

            // Public Relations
            $table->boolean('persuasive_verbiage_created')->default(false);
            $table->boolean('monitor_slide_created')->default(false);
            $table->boolean('community_flyer_created')->default(false);
            $table->boolean('advertising_request_submitted')->default(false);

            // Finances
            $table->boolean('contracts_negotiated')->default(false);
            $table->boolean('contract_submitted')->default(false);
            $table->boolean('fund_requests_submitted')->default(false);
            $table->boolean('caterer_fund_request')->default(false);
            $table->boolean('vendor_payment_request')->default(false);

            // Secure Additional Staff
            $table->boolean('security_secured')->default(false);
            $table->boolean('hospitality_secured')->default(false);
            $table->boolean('ushers_secured')->default(false);
            $table->boolean('comestible_secured')->default(false);
            $table->boolean('media_secured')->default(false);
            $table->boolean('pr_secured')->default(false);
            $table->boolean('choir_secured')->default(false);
            $table->boolean('volunteers_secured')->default(false);
            $table->boolean('childcare_secured')->default(false);

            // Other
            $table->boolean('mc_contacted')->default(false);
            $table->boolean('backup_mc_contacted')->default(false);
            $table->enum('status', ['pending', 'approved', 'denied'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_plannings');
    }
};
