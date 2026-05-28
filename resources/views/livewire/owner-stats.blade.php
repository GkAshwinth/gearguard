<div wire:poll.10s class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    
    {{-- Pending Approvals Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm relative overflow-hidden flex flex-col justify-between">
        <div class="absolute top-0 left-0 w-1.5 h-full bg-yellow-400"></div>
        <div>
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pending Approvals</h3>
            <p class="mt-2 text-3xl font-extrabold text-yellow-600">{{ $pendingCount }}</p>
        </div>
        <p class="text-xs text-gray-400 mt-4 flex items-center">
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-yellow-400 mr-1.5 animate-pulse"></span>
            Requires your attention
        </p>
    </div>

    {{-- Active Rentals Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm relative overflow-hidden flex flex-col justify-between">
        <div class="absolute top-0 left-0 w-1.5 h-full bg-green-500"></div>
        <div>
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Active Rentals</h3>
            <p class="mt-2 text-3xl font-extrabold text-green-600">{{ $activeRentals }}</p>
        </div>
        <p class="text-xs text-gray-400 mt-4 flex items-center">
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
            Currently checked out
        </p>
    </div>

    {{-- Total Revenue Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm relative overflow-hidden flex flex-col justify-between">
        <div class="absolute top-0 left-0 w-1.5 h-full bg-sky-600"></div>
        <div>
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Revenue</h3>
            <p class="mt-2 text-3xl font-extrabold text-sky-600">LKR {{ number_format($totalRevenue, 2) }}</p>
        </div>
        <p class="text-xs text-gray-400 mt-4 flex items-center">
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-sky-600 mr-1.5"></span>
            From completed bookings
        </p>
    </div>

</div>
