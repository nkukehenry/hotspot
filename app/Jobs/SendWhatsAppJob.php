<?php

namespace App\Jobs;

use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $mobileNumber;
    protected $message;

    /**
     * Create a new job instance.
     *
     * @param string $mobileNumber
     * @param string $message
     */
    public function __construct($mobileNumber, $message)
    {
        $this->mobileNumber = $mobileNumber;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(WhatsAppService $whatsAppService)
    {
        \Illuminate\Support\Facades\Log::info("Processing SendWhatsAppJob for number: {$this->mobileNumber}");
        $response = $whatsAppService->sendMessage($this->mobileNumber, $this->message);
        \Illuminate\Support\Facades\Log::info("SendWhatsAppJob completed. Response: " . json_encode($response));
    }
}
