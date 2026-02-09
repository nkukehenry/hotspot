<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id', 
        'source_account_id',
        'destination_account_id',
        'transaction_id', 
        'debit', 
        'credit', 
        'description', 
        'reference_type', 
        'reference_id'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
