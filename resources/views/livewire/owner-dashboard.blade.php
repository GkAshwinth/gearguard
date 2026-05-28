<div wire:poll.15s class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    
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
