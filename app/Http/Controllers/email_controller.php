<?php

namespace App\Http\Controllers;


use App\Mail\ActualMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendEmail($body,$emailTo){
        $details = [
            'title' => 'Mail from Loyalty application',
            'body' => $body,
        ];
        Mail::to($emailTo)->send(new ActualMail($details));
        return "email sent";
    }
}
