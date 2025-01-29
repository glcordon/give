<?php

use App\Livewire\ActivityForm;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/activity-form', ActivityForm::class);
