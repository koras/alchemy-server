<?php

namespace App\Services;

use App\Contracts\Models\FeedbackInterface;
use App\Contracts\Services\YoomoneyServiceInterface;
use App\Contracts\Services\FeedbackServiceInterface;


use Illuminate\Http\Request;

class FeedbackService implements FeedbackServiceInterface
{
    public function __construct(private FeedbackInterface $feedback)
    {

    }
    public function feedback(Request $request)
    {
        $this->feedback->send(
            [
                'level_id'=> $request->level_id,
                'user_id'=> $request->user_id,
                'email'=> $request->email,
                'message'=> $request->message,
            ]
        );
    }
}

