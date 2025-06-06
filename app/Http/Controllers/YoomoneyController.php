<?php

namespace App\Http\Controllers;

use App\Contracts\Services\YoomoneyServiceInterface;

class YoomoneyController extends Controller
{

    public function __construct(private readonly YoomoneyServiceInterface $service)
    {
        //Accept
        // application/json
    }

    public function redirect(){
        return ['status'=>'true'];
    }

    public function notification(){
        return ['status'=>'true'];
    }
}
