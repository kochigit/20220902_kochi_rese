<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;


class EmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this
        ->subject('メールアドレスの認証をお願いいたします')
        ->from('rese@example.com')
        ->with(['hashedEmail' => Hash::make($this->user->email)])
        ->view('email.verification');
    }
}
