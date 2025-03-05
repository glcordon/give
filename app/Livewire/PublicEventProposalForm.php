<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\EventPlan;
use Filament\Forms;
use App\Filament\Resources\EventProposalResource;

class PublicEventProposalForm extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    // Define public properties for each form field
    public $event_name;
    public $event_date;
    public $event_description;
    public $purpose_of_event;
    public $proposer_name;
    public $proposer_email;
    public $proposer_phone;
    public $expected_attendees;
    public $target_population = [];
    public $target_population_other;
    public $start_time;
    public $end_time;
    public $is_registration_required = false;
    public $cost_per_person;
    public $guest_speakers_musicians = [];

    protected function getFormSchema(): array
    {
        return EventProposalResource::form(Forms\Form::make($this))->getComponents();
    }

    public function submit()
    {
        $data = $this->form->getState();
        $data['status'] = 'pending';
        $data['event_coordinators'] = 'pending';
        $data['budget'] = 0;
        $data['vision_support'] = 0;
        $data['setup_time'] = '';
        $data['tear_down_time'] = '';
        $data['main_purpose'] = $data['purpose_of_event'] ?? '';
        $event = EventPlan::create($data);
        return redirect()->route('event.proposal.thanks', ['id' => $event->id]);
    }

    public function render()
    {
        return view('livewire.public-event-proposal-form');
    }
}
