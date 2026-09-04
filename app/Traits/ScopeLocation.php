<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait ScopeLocation
{
    /**
     * Scope query to user's assigned location if user does not have global location access.
     */
    public function scopeForUserLocation(Builder $query, ?User $user = null): Builder
    {
        $user = $user ?? auth()->user();

        if (! $user) {
            return $query;
        }

        if ($user->hasGlobalLocationAccess()) {
            return $query;
        }

        if ($user->location_id) {
            $table = $query->getModel()->getTable();

            return $query->where($table.'.location_id', $user->location_id);
        }

        return $query->whereRaw('1 = 0');
    }
}
