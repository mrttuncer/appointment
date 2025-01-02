<?php

namespace App\Console\Commands;

use App\Jobs\SendAvailableDatesJob;
use Illuminate\Console\Command;

class CheckAppointmentDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-appointment-date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $telegramToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = '1553035293';
        SendAvailableDatesJob::dispatch($telegramToken, $chatId);
    }
}
