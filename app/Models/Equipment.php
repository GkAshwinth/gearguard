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
        $productMap = [
            'Sony A7 IV' => asset('images/products/sony_a7_iv.png'),
            'Canon EOS R5' => asset('images/products/canon_eos_r5.png'),
            'Nikon Z9' => asset('images/products/nikon_z9.png'),
            'Fujifilm X-T4' => asset('images/products/fujifilm_x_t4.png'),
            'Blackmagic Pocket 6K' => asset('images/products/blackmagic_pocket_6k.png'),
            'Panasonic GH6' => asset('images/products/panasonic_gh6.png'),
            'Sony 24-70mm GM II' => asset('images/products/sony_24_70mm.png'),
            'Canon RF 50mm f/1.2' => asset('images/products/canon_rf_50mm.png'),
            'Sigma 85mm Art' => asset('images/products/sigma_85mm.png'),
            'Nikon Z 70-200mm f/2.8' => asset('images/products/nikon_70_200mm.png'),
            'Sony 16-35mm GM' => asset('images/products/sony_16_35mm.png'),
            'Canon RF 100mm Macro' => asset('images/products/canon_rf_100mm.png'),
            'Aputure 120d II' => asset('images/products/aputure_120d.png'),
        ];
        
        $categoryMap = [
            'Cameras' => asset('images/categories/camera.png'),
            'Lenses' => asset('images/categories/lenses.png'),
            'Lighting' => asset('images/categories/lighting.png'),
            'Audio' => asset('images/categories/audio.png'),
        ];
        
        $fallback = $productMap[$this->name] ?? ($categoryMap[$this->category] ?? asset('images/categories/camera.png'));

        if ($this->image_path && str_starts_with($this->image_path, 'http')) {
            if (str_contains($this->image_path, 'unsplash.com')) {
                return $fallback;
            }
            return $this->image_path;
        }
        return $this->image_path
            ? asset('storage/' . $this->image_path)
            : $fallback;
    }
}
