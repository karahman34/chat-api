<?php

namespace App\Listeners;

use App\Mail\VerifyEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class SendEmailVerification implements ShouldQueue
{

    /**
     * Handle the event.
     *
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        $url = URL::signedRoute('verify-email', ['user' => $event->user->id]);

        Mail::to($event->user)->send(new VerifyEmail($url));
    }
}
