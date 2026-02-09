<?php

namespace App\Services;

use App\Models\Account;
use App\Models\LedgerEntry;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class LedgerService
{
    /**
     * Record a transaction in the double-entry ledger.
     */
    public function recordTransaction(Transaction $transaction)
    {
        return DB::transaction(function () use ($transaction) {
            $site = $transaction->site;
            
            // 1. Determine Asset Account (Digital or Cash)
            if ($transaction->agent_id) {
                $assetAccountCode = 'AGENT_CASH_' . $transaction->agent_id;
                $assetAccountName = 'Cash: ' . ($transaction->agent->name ?? 'Agent ' . $transaction->agent_id);
            } else {
                $assetAccountCode = 'SYSTEM_DIGITAL';
                $assetAccountName = 'System Digital Wallet';
            }

            $assetAccount = Account::where('code', $assetAccountCode)->first();
            
            if (!$assetAccount) {
                $assetAccount = Account::create([
                    'name' => $assetAccountName,
                    'code' => $assetAccountCode,
                    'type' => 'asset',
                    'site_id' => $transaction->site_id, // Scoped to site
                    'balance' => 0
                ]);
            }

            // 2. Determine Site Payable Account
            $sitePayableCode = "SITE_PAYABLE_" . $site->id;
            $sitePayableAccount = Account::where('code', $sitePayableCode)->first();
            
            if (!$sitePayableAccount) {
                $sitePayableAccount = Account::create([
                    'name' => "Payable: " . $site->name,
                    'code' => $sitePayableCode,
                    'type' => 'liability',
                    'site_id' => $site->id,
                    'balance' => 0
                ]);
            }

            // 3. Platform Fee Account
            $feeAccountCode = 'FEE_INCOME';
            $feeAccount = Account::where('code', $feeAccountCode)->first();
            
            if (!$feeAccount) {
                $feeAccount = Account::create([
                    'name' => 'General Fee Income',
                    'code' => $feeAccountCode,
                    'type' => 'revenue',
                    'balance' => 0
                ]);
            }

            // DEBIT Asset Account (Total Customer Pay)
            $this->addEntry($assetAccount, $transaction->total_amount, 0, "Sale: " . $transaction->transaction_id, $transaction);

            // CREDIT Site Payable (Package Amount - Site Fee)
            $netSitePayable = $transaction->amount - $transaction->site_fee;
            $this->addEntry($sitePayableAccount, 0, $netSitePayable, "Sale Net: " . $transaction->transaction_id, $transaction);

            // CREDIT Fee Income (Total Fees)
            $this->addEntry($feeAccount, 0, $transaction->total_fee, "Fees: " . $transaction->transaction_id, $transaction);

            return true;
        });
    }

    /**
     * Helper to add a ledger entry and update account balance.
     */
    public function addEntry(Account $account, $debit, $credit, $description, Transaction $transaction = null)
    {
        $entryData = [
            'account_id' => $account->id,
            'transaction_id' => $transaction ? $transaction->id : null,
            'debit' => $debit,
            'credit' => $credit,
            'description' => $description,
            'reference_type' => 'sale',
            'reference_id' => $transaction ? $transaction->id : null
        ];

        // Track source (credit) or destination (debit)
        if ($credit > 0) {
            $entryData['source_account_id'] = $account->id;
        }
        
        if ($debit > 0) {
            $entryData['destination_account_id'] = $account->id;
        }

        $entry = LedgerEntry::create($entryData);

        // Update account balance
        // Asset/Expense: Debit increases, Credit decreases
        // Liability/Revenue/Equity: Credit increases, Debit decreases
        if (in_array($account->type, ['asset', 'expense'])) {
            $account->balance += ($debit - $credit);
        } else {
            $account->balance += ($credit - $debit);
        }
        $account->save();

        return $entry;
    }
}
