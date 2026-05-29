<x-app-layout title="Owner Dashboard">
    <div class="max-w-7xl mx-auto px-4 py-10">
        <h1 class="text-2xl font-bold text-gray-900 mb-8">Owner Dashboard</h1>

        {{-- Live Metrics Cards --}}
        @livewire('owner-dashboard')

        {{-- Overdue Alert --}}
        @if($overdue->count())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-8">
            <p class="font-semibold text-red-800">⚠ {{ $overdue->count() }} Overdue Rental(s)</p>
            @foreach($overdue as $b)
            <p class="text-sm text-red-700 mt-1">
                Booking #{{ $b->id }} — {{ $b->equipment->name }} (due {{ $b->end_date->format('M d') }}) — {{ $b->user->name }}
            </p>
            @endforeach
        </div>
        @endif

        {{-- Dynamic Booking Manager Component --}}
        @livewire('owner-booking-manager')
    </div>
</x-app-layout>
