<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class MultitenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::hasUser()) {
            $user = Auth::user();

            // Owners see everything.
            if ($user->hasRole('Owner')) {
                return;
            }

            $table = $model->getTable();

            // Use a safer check to see if we should apply scoping.
            // We'll check if the model has the 'site_id' or 'company_id' attributes defined in its fillable or if it's a known scoped model.
            $isSiteScoped = $model->isFillable('site_id') || in_array('site_id', $model->getHidden());
            $isCompanyScoped = $model->isFillable('company_id') || in_array('company_id', $model->getHidden());

            // 1. Handle Company Admin Scoping
            if ($user->hasRole('Company Admin')) {
                if ($isCompanyScoped) {
                    $builder->where($table . '.company_id', $user->company_id);
                } elseif ($isSiteScoped) {
                    // For models related to a site, ensure the site belongs to the company.
                    $builder->whereIn($table . '.site_id', function ($query) use ($user) {
                        $query->select('id')->from('sites')->where('company_id', $user->company_id);
                    });
                }
                return;
            }

            // 2. Handle Site-restricted Roles (Manager/Supervisor/Agent)
            if ($isSiteScoped) {
                if ($user->site_id) {
                    $builder->where($table . '.site_id', $user->site_id);
                } else {
                    // Force zero results if they HAVE to be in a site but aren't assigned.
                    $builder->whereRaw('0 = 1');
                }
            } elseif ($isCompanyScoped) {
                // For models like User/Site that have company_id, lower roles still stay in their company.
                $builder->where($table . '.company_id', $user->company_id);
            }
        }
    }
}
