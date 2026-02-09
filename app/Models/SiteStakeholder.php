<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteStakeholder extends Model
{
    use HasFactory;

    protected $fillable = ['site_id', 'name', 'share_percent', 'account_id'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
