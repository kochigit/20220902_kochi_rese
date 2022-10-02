<?php

namespace App\Mail;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Reminder extends Mailable
{
    use Queueable, SerializesModels;

    protected $reservation;

    public function __construct($reservation)
    {
        $date = new DateTime($reservation->date);
        $time = new DateTime($reservation->time);
        $reservation->date = $date->format('n月 j日 (D)');
        $reservation->time = $time->format('H時 i分');
        $this->reservation = $reservation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
        ->subject('本日は予約日です')
        ->with(['reservation' => $this->reservation])
        ->view('email.reminder');
    }
}
