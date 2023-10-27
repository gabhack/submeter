<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMailable;
use App\Jobs\SendEmailJob;
use Carbon\Carbon;

class EmailController extends Controller
{
    public function sendEmail()
    {
        $emailJob = (new SendEmailJob('Welcome'))->delay(Carbon::now()->addSeconds(60));
   		dispatch($emailJob);
        echo 'email sent';
    }
}
