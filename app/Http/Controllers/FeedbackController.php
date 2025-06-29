<?php

namespace App\Http\Controllers;

use App\Contracts\Services\FeedbackServiceInterface;
use Illuminate\Http\Request;

class FeedbackController
{
    public function __construct(public FeedbackServiceInterface $feedbackService){

    }


    public function feedback(Request $request)
    {
        return $this->feedbackService->feedback($request);
    }
}

