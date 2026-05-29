<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class OwnerBookingManager extends Component
{
    use WithPagination;
    public $filter = 'all';
    
    // Modal state for rejection
    public $rejectingBookingId = null;
    public $cancellationReason = '';

    public function setFilter($filter)
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function approve($id)
    {
        if (!auth()->user()->isOwner()) abort(403);
        
        $booking = \App\Models\Booking::findOrFail($id);
        $booking->update(['status' => 'approved']);
    }
    
    public function markCompleted($id)
    {
        if (!auth()->user()->isOwner()) abort(403);
        
        $booking = \App\Models\Booking::findOrFail($id);
        $booking->update(['status' => 'completed']);
    }

    public function initiateReject($id)
    {
        $this->rejectingBookingId = $id;
        $this->cancellationReason = '';
    }

    public function confirmReject()
    {
        if (!auth()->user()->isOwner()) abort(403);
        
        $this->validate([
            'cancellationReason' => 'required|string|max:255',
        ]);

        $booking = \App\Models\Booking::findOrFail($this->rejectingBookingId);
        $booking->update([
            'status' => 'rejected',
            'cancellation_reason' => $this->cancellationReason,
        ]);

        $this->rejectingBookingId = null;
        $this->cancellationReason = '';
        
        $this->dispatch('close-modal');
    }
    
    public function cancelReject()
    {
        $this->rejectingBookingId = null;
        $this->cancellationReason = '';
        $this->dispatch('close-modal');
    }

    public function render()
    {
        $query = \App\Models\Booking::with(['user', 'equipment'])->latest();
        
        if ($this->filter !== 'all') {
            $query->where('status', $this->filter);
        }
        
        $bookings = $query->paginate(15);
        
        $pendingCount = \App\Models\Booking::where('status', 'pending')->count();
        
        return view('livewire.owner-booking-manager', [
            'bookings' => $bookings,
            'pendingCount' => $pendingCount,
        ]);
    }
}
