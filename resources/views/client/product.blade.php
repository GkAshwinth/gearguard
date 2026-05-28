<x-app-layout title="{{ $equipment->name }}">
    <div class="max-w-6xl mx-auto px-4 py-10">
        <a href="{{ route('equipment.index') }}" class="text-sm text-sky-600 hover:underline mb-6 inline-block">← Back to Browse</a>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            {{-- Image --}}
            <div class="rounded-2xl overflow-hidden shadow-md h-96">
                <img src="{{ $equipment->image_url }}" alt="{{ $equipment->name }}" class="w-full h-full object-cover">
            </div>

            {{-- Details + Booking --}}
            <div>
                <span class="bg-sky-50 text-sky-600 text-xs font-semibold px-3 py-1 rounded-full">{{ $equipment->category }}</span>
                <h1 class="text-3xl font-bold text-gray-900 mt-3">{{ $equipment->name }}</h1>
                <p class="text-gray-500 mt-2">{{ $equipment->description }}</p>

                <div class="mt-4 text-3xl font-bold text-sky-600">
                    LKR {{ number_format($equipment->daily_rate) }}
                    <span class="text-base font-normal text-gray-400">/day</span>
                </div>

                <hr class="my-6">

                {{-- Livewire Booking Checker Component --}}
                @livewire('booking-checker', ['equipment' => $equipment])
            </div>
        </div>

        {{-- Busy Dates Info --}}
        @if($busyDates->count())
        <div class="mt-10 bg-amber-50 border border-amber-200 rounded-xl p-5">
            <h3 class="font-semibold text-amber-900 mb-3">Already Booked Dates</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($busyDates as $period)
                    <span class="bg-amber-100 text-amber-800 text-sm px-3 py-1 rounded-full">
                        {{ \Carbon\Carbon::parse($period->start_date)->format('M d') }}
                        → {{ \Carbon\Carbon::parse($period->end_date)->format('M d, Y') }}
                    </span>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
