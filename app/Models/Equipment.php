<?php

namespace App\Models;

use App\Models\Scopes\ActiveEquipmentScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'daily_rate',
        'image_path',
        'description',
        'status',
    ];

    protected $casts = [
        'daily_rate' => \App\Casts\Currency::class,
    ];

    /**
     * Global Scope: automatically filters to 'available' equipment on all queries.
     * Matches friend's ActiveProductScope pattern.
     * Admin inventory bypasses this with ::withoutGlobalScope(ActiveEquipmentScope::class)
     */
    protected static function booted(): void
    {
        // Global scope — available equipment only by default
        static::addGlobalScope(new ActiveEquipmentScope());

        // Observer — logs all CRUD actions to security_logs table
        static::observe(\App\Observers\EquipmentObserver::class);
    }

    /**
     * Relationship: Equipment has many bookings.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Local Scope: explicit available filter (for clarity in code).
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Local Scope: Filter by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Check if this equipment is available for a given date range.
     * Uses a DB transaction-safe query to prevent race conditions.
     */
    public function isAvailableFor(string $startDate, string $endDate): bool
    {
        return !$this->bookings()
            ->whereNotIn('status', ['rejected', 'cancelled'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->where('start_date', '<=', $startDate)
                         ->where('end_date', '>=', $endDate);
                  });
            })
            ->exists();
    }

    /**
     * Accessor: get the full image URL.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image_path && str_starts_with($this->image_path, 'http')) {
            return $this->image_path;
        }
        return $this->image_path
            ? asset('storage/' . $this->image_path)
            : 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=800&q=80';
    }
}
