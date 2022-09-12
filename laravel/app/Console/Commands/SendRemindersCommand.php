<?php

namespace App\Console\Commands;

use App\Mail\Reminder;
use App\Models\Reservation;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class SendRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'リマインダーを送信します';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $reservations = Reservation::with([
            'user:uuid,name,email',
            'restaurant',
        ])->where('date', Carbon::today())->get();
        foreach ($reservations as $reservation) {
            Mail::to($reservation->user->email)->send(new Reminder($reservation));
        }
    }
}
