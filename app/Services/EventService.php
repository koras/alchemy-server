<?php

namespace App\Services;

use App\Contracts\Models\AppEventInterface;
use App\Contracts\Services\EventServiceInterface;

class EventService implements EventServiceInterface
{
    public function __construct(private AppEventInterface $appEvent)
    {

    }

    public function event($request)
    {
        $this->appEvent->create([
            'user_id' => $request->user_id,
            'event_type' => $request->event_type,
            'event_data' => $request->event_data,
            'device_info' => $request->device_info,
        ]);
    }
}
