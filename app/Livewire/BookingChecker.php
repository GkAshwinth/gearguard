<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Equipment;
use App\Models\Booking;
use Carbon\Carbon;

class BookingChecker extends Component
{
    public Equipment $equipment;
    public $start_date;
    public $end_date;
    public $isAvailable = null;
    public $totalCost = 0;

    // This runs automatically whenever a user changes a date!
    public function updated($property)
    {
        if ($this->start_date && $this->end_date) {
            $this->checkAvailability();
        }
    }

    public function checkAvailability()
    {
        // 1. Check if the dates overlap with any approved/pending bookings
        $conflict = Booking::where('equipment_id', $this->equipment->id)
            ->whereNotIn('status', ['rejected', 'cancelled'])
            ->where(function ($query) {
                $query->whereBetween('start_date', [$this->start_date, $this->end_date])
                      ->orWhereBetween('end_date', [$this->start_date, $this->end_date])
                      ->orWhere(function ($q) {
                          $q->where('start_date', '<=', $this->start_date)
                            ->where('end_date', '>=', $this->end_date);
                      });
            })->exists();

        if ($conflict) {
            $this->isAvailable = false;
            $this->totalCost = 0;
        } else {
            $this->isAvailable = true;
            // Calculate days and cost securely on the server
            $days = Carbon::parse($this->start_date)->diffInDays(Carbon::parse($this->end_date)) + 1;
            $this->totalCost = $days * (float) $this->equipment->getRawOriginal('daily_rate');
        }
    }

    public function render()
    {
        return view('livewire.booking-checker');
    }
}
