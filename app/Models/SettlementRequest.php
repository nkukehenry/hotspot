<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettlementRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'amount',
        'start_date',
        'end_date',
        'status',
        'notes',
        'approved_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
