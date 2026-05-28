<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * ActiveEquipmentScope — Global Scope
 *
 * Automatically filters out equipment with status='maintenance'
 * or status='rented' from ALL queries on the Equipment model
 * unless explicitly removed with ::withoutGlobalScope().
 *
 * Applied in: Equipment::booted() method
 *
 * This means Equipment::all() automatically returns only
 * available items — no developer can forget the filter.
 *
 * To bypass (e.g. in admin inventory view):
 *   Equipment::withoutGlobalScope(ActiveEquipmentScope::class)->get()
 */
class ActiveEquipmentScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where('status', 'available');
    }
}
