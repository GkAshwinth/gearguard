<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * SecurityLog Model
 *
 * Tracks suspicious activity and critical admin actions.
 * Written by:
 *  - EquipmentObserver (every create/update/delete)
 *  - CheckRole middleware (every 403 IDOR attempt)
 *  - ApiController (every failed token attempt)
 *
 * SECURITY FEATURE: Creates an immutable audit trail.
 * Even if an attacker gains access and covers their tracks,
 * the security_logs table reveals what they did.
 */
class SecurityLog extends Model
{
    protected $fillable = [
        'action',
        'description',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    /**
     * The user who triggered this log entry (nullable — can be guest).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Only logs from the last 24 hours.
     */
    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', now()->subDay());
    }

    /**
     * Scope: Only security violation attempts.
     */
    public function scopeViolations($query)
    {
        return $query->whereIn('action', [
            'idor_attempt',
            'api_auth_failure',
            'invalid_status_tamper',
        ]);
    }
}
