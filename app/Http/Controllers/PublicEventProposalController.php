<?php

namespace App\Http\Controllers;

class PublicEventProposalController extends Controller
{
    public function show()
    {
        return view('public-event-proposal');
    }

    public function thanks($id)
    {
        return view('event-proposal-thanks', ['id' => $id]);
    }
}
