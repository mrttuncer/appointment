<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendAvailableDatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     */
    protected $telegramToken;
    protected $chatId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($telegramToken, $chatId)
    {
        $this->telegramToken = $telegramToken;
        $this->chatId = $chatId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = new Client();
        $url = "https://api.schengenvisaappointments.com/api/visa-list/?format=json";

        try {
            $response = $client->request('GET', $url);

            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);
                $messages = [];

                foreach ($data as $entry) {
                    $mission_country = $entry['mission_country'] ?? null;
                    $source_country = $entry['source_country'] ?? null;
                    $appointment_date = $entry['appointment_date'] ?? null;

                    if ($mission_country === "Italy" && $source_country === "Turkiye") {
                        $message = $appointment_date
                            ? "{$mission_country} için randevu tarihi: {$appointment_date}\n"
                            : "{$mission_country} için mevcut randevu yok.\n";

                        $message .= "Kategori: {$entry['visa_category']}\n";
                        $message .= "Alt Kategori: {$entry['visa_subcategory']}\n";
                        $message .= "Merkez: {$entry['center_name']}\n";
                        $message .= "Randevu Al: {$entry['book_now_link']}\n";

                        $messages[] = $message;
                    }
                }
                if (!empty($messages)) {
                    foreach ($messages as $message) {
                        $this->sendToTelegram($message);
                    }
                }
            } else {
                echo "API'den veri alınamadı. Hata kodu: " . $response->getStatusCode();
            }
        } catch (\Exception $e) {
            echo "Hata oluştu: " . $e->getMessage();
        }
    }

    private function sendToTelegram($message)
    {
        $telegramUrl = "https://api.telegram.org/bot{$this->telegramToken}/sendMessage";

        $client = new Client();

        try {
            $client->request('POST', $telegramUrl, [
                'form_params' => [
                    'chat_id' => $this->chatId,
                    'text' => $message
                ]
            ]);
        } catch (\Exception $e) {
            echo "Telegram mesajı gönderilemedi: " . $e->getMessage();
        }
    }
}
