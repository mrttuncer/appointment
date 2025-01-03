<?php

namespace App\Console\Commands;

use App\Jobs\SendAvailableDatesJob;
use Illuminate\Console\Command;

class CheckAppointment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-appointment';

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
        $chatId = env('CHAT_ID');
        SendAvailableDatesJob::dispatch($telegramToken, $chatId);
    }
}
