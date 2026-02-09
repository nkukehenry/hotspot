<?php

namespace App\Services;

use App\Models\Site;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\SiteStakeholder;
use Illuminate\Support\Facades\DB;

class FeeService
{
    protected $ledgerService;

    public function __construct(LedgerService $ledgerService)
    {
        $this->ledgerService = $ledgerService;
    }

    /**
     * Calculate fees for a transaction before it is processed.
     */
    public function calculateFees(Site $site, $packageAmount)
    {
        $customerFee = $site->customer_fee_fixed + ($packageAmount * ($site->customer_fee_percent / 100));
        $siteFee = $site->site_fee_fixed + ($packageAmount * ($site->site_fee_percent / 100));
        $totalFee = $customerFee + $siteFee;
        $totalAmount = $packageAmount + $customerFee; // Total customer pays is base price + customer fee

        return [
            'amount' => $packageAmount,
            'customer_fee' => $customerFee,
            'site_fee' => $siteFee,
            'total_fee' => $totalFee,
            'total_amount' => $totalAmount
        ];
    }

    /**
     * Distribute fees to stakeholders after a successful transaction.
     */
    public function distributeFees(Transaction $transaction)
    {
        return DB::transaction(function () use ($transaction) {
            if ($transaction->fee_distributed) {
                return false;
            }

            $site = $transaction->site;
            $feeIncomeAccount = Account::where('code', 'FEE_INCOME')->first();
            
            if (!$feeIncomeAccount) {
                 return false;
            }

            $stakeholders = SiteStakeholder::where('site_id', $site->id)->get();
            $totalDistributed = 0;

            foreach ($stakeholders as $stakeholder) {
                $stakeholderAccount = $stakeholder->account;
                if (!$stakeholderAccount) continue;

                $shareAmount = $transaction->total_fee * ($stakeholder->share_percent / 100);
                
                // DEBIT Fee Income
                $this->ledgerService->addEntry(
                    $feeIncomeAccount, 
                    $shareAmount, 
                    0, 
                    "Dist: " . $stakeholder->name . " (T:" . $transaction->transaction_id . ")",
                    $transaction
                );

                // CREDIT Stakeholder Account
                $this->ledgerService->addEntry(
                    $stakeholderAccount, 
                    0, 
                    $shareAmount, 
                    "Share: Fee distribution (T:" . $transaction->transaction_id . ")",
                    $transaction
                );

                $totalDistributed += $shareAmount;
            }

            $transaction->fee_distributed = true;
            $transaction->save();

            return $totalDistributed;
        });
    }
}
