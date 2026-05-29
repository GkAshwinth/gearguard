<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'equipment_id',
        'start_date',
        'end_date',
        'total_cost',
        'status',
        'cancellation_reason',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'total_cost'  => 'decimal:2',
    ];

    /**
     * Relationship: Booking belongs to a User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Booking belongs to Equipment.
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Scope: Only active (approved) bookings.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: Only pending bookings awaiting approval.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Only overdue bookings (approved but end_date passed).
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'approved')
                     ->where('end_date', '<', now()->toDateString());
    }

    /**
     * Accessor: number of rental days.
     */
    public function getDaysAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    /**
     * Accessor: human-readable status badge colour (Tailwind).
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'yellow',
            'approved'  => 'green',
            'rejected'  => 'red',
            'completed' => 'blue',
            'cancelled' => 'gray',
            default     => 'gray',
        };
    }
}
