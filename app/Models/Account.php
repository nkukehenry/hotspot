<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'type', 'site_id', 'balance'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function ledgerEntries()
    {
        return $this->hasMany(LedgerEntry::class);
    }
}
