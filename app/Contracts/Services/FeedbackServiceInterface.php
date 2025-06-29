<?php

namespace App\Contracts\Services;

use Illuminate\Http\Request;

interface FeedbackServiceInterface
{
    public function feedback(Request $request);
}
