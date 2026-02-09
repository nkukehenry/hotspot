<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class SiteScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Only filter if the user is attached to a specific site
            // Platform Owners see all data.
            if ($user->site_id) {
                $table = $model->getTable();
                
                // Safety check: ensure the table actually has a site_id column
                // This prevents SQL errors on incompatible models.
                if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'site_id')) {
                    $builder->where($table . '.site_id', $user->site_id);
                }
            }
        }
    }
}
