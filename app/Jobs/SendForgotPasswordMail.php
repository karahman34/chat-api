<?php

namespace App\Jobs;

use App\Mail\ForgotPasswordEmail;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\Facades\Mail;

class SendForgotPasswordMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $token = Str::random(32);
        $url = $url = env('CLIENT_RESET_PASSWORD_URL') . '?token=' . $token . '&email=' . $this->user->email;

        DB::table('password_resets')->insert([
            'email' => $this->user->email,
            'token' => $token,
            'created_at' => now()
        ]);

        Mail::to($this->user)->send(new ForgotPasswordEmail($url));
    }
}
