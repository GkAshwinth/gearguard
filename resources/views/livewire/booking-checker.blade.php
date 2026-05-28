<div class="mt-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Start Date</label>
            <input type="date" wire:model.live="start_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">End Date</label>
            <input type="date" wire:model.live="end_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>
    </div>

    <div class="mt-6">
        @if($isAvailable === true)
            <div class="p-4 bg-green-50 border border-green-200 rounded-md">
                <p class="text-green-800 font-bold">✓ Available for these dates!</p>
                <p class="text-green-700 mt-1">Total Cost: LKR {{ number_format($totalCost, 2) }}</p>
                
                <form action="{{ route('bookings.store') }}" method="POST" class="mt-4">
                    @csrf
                    <input type="hidden" name="equipment_id" value="{{ $equipment->id }}">
                    <input type="hidden" name="start_date" value="{{ $start_date }}">
                    <input type="hidden" name="end_date" value="{{ $end_date }}">
                    <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 font-semibold">
                        Confirm Booking
                    </button>
                </form>
            </div>
        @elseif($isAvailable === false)
            <div class="p-4 bg-red-50 border border-red-200 rounded-md">
                <p class="text-red-800 font-bold">✗ Not available.</p>
                <p class="text-red-700 text-sm mt-1">These dates conflict with an existing booking. Please select different dates.</p>
            </div>
        @elseif($start_date || $end_date)
            <p class="text-gray-500 text-sm mt-2">Please select both dates to check availability.</p>
        @endif
    </div>
</div>
