<?php

namespace App\Http\Controllers;

use App\Contracts\Services\EventServiceInterface;
use Illuminate\Http\Request;

class EventController
{

    public function __construct(public EventServiceInterface $eventService)
    {

    }


    public function event(Request $request)
    {
        return $this->eventService->event($request);
    }
}

