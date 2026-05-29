<?php

namespace App\Observers;

use App\Models\Equipment;
use App\Models\SecurityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * EquipmentObserver
 *
 * Hooks into Eloquent model lifecycle events for Equipment.
 * Automatically logs who created, updated, or deleted equipment.
 * This is the Observer pattern — registered in AppServiceProvider.
 *
 * Registered in: app/Providers/AppServiceProvider.php
 *   Equipment::observe(EquipmentObserver::class);
 *
 * Lifecycle hooks available:
 *   creating, created, updating, updated, deleting, deleted,
 *   restoring, restored, forceDeleted
 */
class EquipmentObserver
{
    /**
     * Handle the Equipment "created" event.
     * Fires after a new equipment item is saved.
     */
    public function created(Equipment $equipment): void
    {
        $this->logAction('created', $equipment);

        Log::info('Equipment created', [
            'equipment_id'   => $equipment->id,
            'equipment_name' => $equipment->name,
            'category'       => $equipment->category,
            'daily_rate'     => $equipment->daily_rate,
            'created_by'     => Auth::id() ?? 'system',
        ]);
    }

    /**
     * Handle the Equipment "updated" event.
     */
    public function updated(Equipment $equipment): void
    {
        $this->logAction('updated', $equipment);

        // Log what actually changed
        $changes = $equipment->getChanges();
        Log::info('Equipment updated', [
            'equipment_id' => $equipment->id,
            'changed_fields' => array_keys($changes),
            'updated_by'   => Auth::id() ?? 'system',
        ]);

        // Advanced Feature: Cache Invalidation
        if (isset($changes['category']) || isset($changes['status'])) {
            \Illuminate\Support\Facades\Cache::forget('equipment_categories');
        }
    }

    /**
     * Handle the Equipment "deleted" event.
     * SecurityLog records the deletion — audit trail.
     */
    public function deleted(Equipment $equipment): void
    {
        $this->logAction('deleted', $equipment);

        Log::warning('Equipment deleted', [
            'equipment_id'   => $equipment->id,
            'equipment_name' => $equipment->name,
            'deleted_by'     => Auth::id() ?? 'system',
        ]);
    }

    /**
     * Write to the security_logs table for audit trail.
     */
    private function logAction(string $action, Equipment $equipment): void
    {
        try {
            SecurityLog::create([
                'action'      => "equipment_{$action}",
                'description' => "Equipment '{$equipment->name}' was {$action}.",
                'user_id'     => Auth::id(),
                'ip_address'  => request()->ip(),
                'user_agent'  => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Don't break the app if logging fails
            Log::error('SecurityLog write failed: ' . $e->getMessage());
        }
    }
}
