<x-app-layout title="Owner Dashboard">
    <div class="max-w-7xl mx-auto px-4 py-10">
        <h1 class="text-2xl font-bold text-gray-900 mb-8">Owner Dashboard</h1>

        {{-- Live Metrics Cards (now contains reactive overdue warnings) --}}
        @livewire('owner-dashboard')

        {{-- Dynamic Booking Manager Component --}}
        @livewire('owner-booking-manager')
    </div>
</x-app-layout>
