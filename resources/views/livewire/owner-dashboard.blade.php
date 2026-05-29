<div wire:poll.5s>
    {{-- Reactive Overdue Alert --}}
    @if($overdue->count())
    <div class="bg-red-50 border border-red-200 rounded-2xl p-6 mb-8 shadow-sm">
        <div class="flex items-center gap-2 text-red-800 font-bold mb-4">
            <svg class="h-5 w-5 text-red-600 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>{{ $overdue->count() }} Overdue Rental(s)</span>
        </div>
        <div class="divide-y divide-red-100">
            @foreach($overdue as $b)
            <div class="py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-sm text-red-700">
                <div class="flex items-center gap-3">
                    <span class="bg-red-100 text-red-800 text-xs px-2 py-0.5 rounded font-mono">#{{ $b->id }}</span>
                    <span class="font-semibold">{{ $b->equipment->name }}</span>
                    <span class="text-xs text-red-500">rented by {{ $b->user->name }} ({{ $b->user->email }})</span>
                </div>
                <span class="text-xs font-medium bg-red-200 text-red-900 px-3 py-1 rounded-full">
                    Due: {{ $b->end_date->format('M d, Y') }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-amber-400 relative overflow-hidden">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Pending Approvals</h3>
            <p class="mt-2 text-4xl font-bold text-gray-900">{{ $pendingCount }}</p>
            <p class="text-xs text-gray-400 mt-2">Requires your attention</p>
            @if($pendingCount > 0)
                <span class="absolute top-4 right-4 h-3 w-3 rounded-full bg-amber-400 animate-pulse"></span>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-emerald-500 relative overflow-hidden">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Active Rentals</h3>
            <p class="mt-2 text-4xl font-bold text-gray-900">{{ $activeRentals }}</p>
            <p class="text-xs text-gray-400 mt-2">Currently checked out</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-sky-600 relative overflow-hidden">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Total Revenue</h3>
            <p class="mt-2 text-4xl font-bold text-gray-900"><span class="text-2xl text-gray-500">LKR</span> {{ number_format($totalRevenue, 2) }}</p>
            <p class="text-xs text-gray-400 mt-2">From completed bookings</p>
        </div>
    </div>
</div>
