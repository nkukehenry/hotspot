<?php

namespace App\Jobs;

use App\Services\SMSService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $mobileNumber;
    protected $voucherCode;

    /**
     * Create a new job instance.
     *
     * @param string $mobileNumber
     * @param string $voucherCode
     */
    public function __construct($mobileNumber, $voucherCode)
    {
        $this->mobileNumber = $mobileNumber;
        $this->voucherCode = $voucherCode;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SMSService $smsService)
    {
        $smsService->sendVoucher($this->mobileNumber, $this->voucherCode);
    }
}