<x-app-layout title="Owner Dashboard">
    <div class="max-w-7xl mx-auto px-4 py-10" x-data="{ tab: 'bookings' }">
        <div class="flex items-center justify-between mb-8 border-b border-gray-700/50 pb-4">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Owner Dashboard</h1>
            
            <div class="flex space-x-2 bg-slate-900/50 p-1 rounded-lg border border-slate-700/50">
                <button @click="tab = 'bookings'" :class="tab === 'bookings' ? 'bg-indigo-600 text-white shadow' : 'text-gray-400 hover:text-gray-200'" class="px-4 py-2 text-sm font-medium rounded-md transition-all">
                    Confirm Bookings
                </button>
                <button @click="tab = 'products'" :class="tab === 'products' ? 'bg-indigo-600 text-white shadow' : 'text-gray-400 hover:text-gray-200'" class="px-4 py-2 text-sm font-medium rounded-md transition-all">
                    Manage Products
                </button>
            </div>
        </div>

        <div x-show="tab === 'bookings'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            {{-- Live Metrics Cards --}}
            @livewire('owner-dashboard')

            {{-- Dynamic Booking Manager Component --}}
            @livewire('owner-booking-manager')
        </div>

        <div style="display: none;" x-show="tab === 'products'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            {{-- Dynamic Equipment Manager Component --}}
            @livewire('owner-equipment-manager')
        </div>
    </div>
</x-app-layout>
