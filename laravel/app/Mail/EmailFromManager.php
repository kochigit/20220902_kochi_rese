<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailFromManager extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $request;

    public function __construct($user, $request)
    {
        $this->user = $user;
        $this->request = $request;
    }

    public function build()
    {
        return $this
        ->subject($this->request->title)
        ->from('rese.restaurant@example.com')
        ->with(['content' => $this->request->body])
        ->view('email.from-manager');
        // Hashだけでは「/」が文字列に入る可能性がありURLに挿入するには不適切なため、base64encodeする。
    }
}
