{{--
    Livewire View: booking/checker.blade.php
    Renders the reactive date picker and availability result.
    wire:model.live triggers a server round-trip on every change,
    calling BookingChecker::updated() which re-checks availability.
--}}
<div class="space-y-4">
    <h3 class="font-semibold text-gray-900 text-lg">Check Availability</h3>

    {{-- Date Pickers --}}
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
            <input type="date"
                   wire:model.live="startDate"
                   min="{{ now()->toDateString() }}"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
            <input type="date"
                   wire:model.live="endDate"
                   min="{{ now()->toDateString() }}"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
        </div>
    </div>

    {{-- Loading indicator (shown during Livewire round-trip) --}}
    <div wire:loading class="text-sm text-sky-600 animate-pulse">Checking availability...</div>

    {{-- Result: Available --}}
    @if($isAvailable === true)
    <div class="bg-green-50 border border-green-200 rounded-xl p-4">
        <p class="text-green-800 font-semibold">✓ Available for your dates!</p>
        <div class="mt-2 text-sm text-green-700 space-y-1">
            <p>Duration: <strong>{{ $days }} day{{ $days > 1 ? 's' : '' }}</strong></p>
            <p>Total Cost: <strong>LKR {{ number_format($totalCost) }}</strong></p>
        </div>
    </div>

    @auth
    <form action="{{ route('booking.checkout', $equipment) }}" method="GET">
        <input type="hidden" name="start_date" value="{{ $startDate }}">
        <input type="hidden" name="end_date" value="{{ $endDate }}">
        <button type="submit"
                class="w-full bg-sky-600 text-white py-3 rounded-xl font-semibold hover:bg-sky-700 transition text-lg">
            Book Now — LKR {{ number_format($totalCost) }}
        </button>
    </form>
    @else
    <a href="{{ route('login') }}"
       class="block w-full text-center bg-sky-600 text-white py-3 rounded-xl font-semibold hover:bg-sky-700 transition text-lg">
        Log in to Book
    </a>
    @endauth

    {{-- Result: Unavailable --}}
    @elseif($isAvailable === false)
    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
        <p class="text-red-800 font-semibold">✗ Not Available</p>
        <p class="text-red-700 text-sm mt-1">{{ $errorMessage }}</p>
    </div>

    {{-- Initial State: no dates selected --}}
    @else
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-center text-gray-400 text-sm">
        Select dates above to check availability and pricing.
    </div>
    @endif
</div>
