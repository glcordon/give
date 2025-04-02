<?php

use App\Livewire\ActivityForm;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicEventProposalController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/activity-form', ActivityForm::class);

Route::get('/propose-event', [PublicEventProposalController::class, 'show'])->name('event.proposal.form');
Route::get('/event-proposal-thanks/{id}', [PublicEventProposalController::class, 'thanks'])->name('event.proposal.thanks');
