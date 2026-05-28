<x-app-layout title="My Bookings">
    <div class="max-w-5xl mx-auto px-4 py-10">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">My Bookings</h1>
        <p class="text-gray-500 mb-8">Track the status of your rental requests.</p>

        @if($bookings->count())
        <div class="space-y-4">
            @foreach($bookings as $booking)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="flex flex-col sm:flex-row gap-4">
                    <img src="{{ $booking->equipment->image_url }}"
                         alt="{{ $booking->equipment->name }}"
                         class="w-24 h-24 object-cover rounded-xl flex-shrink-0">

                    <div class="flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $booking->equipment->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $booking->equipment->category }}</p>
                            </div>
                            @php
                                $colors = ['pending'=>'yellow','approved'=>'green','rejected'=>'red','completed'=>'blue','cancelled'=>'gray'];
                                $c = $colors[$booking->status] ?? 'gray';
                            @endphp
                            <span class="bg-{{ $c }}-100 text-{{ $c }}-800 text-xs font-semibold px-3 py-1 rounded-full capitalize">
                                {{ $booking->status }}
                            </span>
                        </div>

                        <div class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-2 text-sm">
                            <div>
                                <p class="text-gray-400 text-xs">Start</p>
                                <p class="font-medium">{{ $booking->start_date->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-400 text-xs">End</p>
                                <p class="font-medium">{{ $booking->end_date->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-400 text-xs">Duration</p>
                                <p class="font-medium">{{ $booking->days }} days</p>
                            </div>
                            <div>
                                <p class="text-gray-400 text-xs">Total</p>
                                <p class="font-medium text-sky-600">LKR {{ number_format($booking->total_cost) }}</p>
                            </div>
                        </div>
                    </div>

                    @if($booking->status === 'pending')
                    <form action="{{ route('booking.cancel', $booking) }}" method="POST" class="flex-shrink-0">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                onclick="return confirm('Cancel this booking?')"
                                class="text-sm text-red-500 hover:text-red-700 border border-red-200 px-3 py-1 rounded-lg hover:bg-red-50 transition">
                            Cancel
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8">{{ $bookings->links() }}</div>

        @else
        <div class="text-center py-24 text-gray-400">
            <p class="text-5xl mb-4">📋</p>
            <p class="text-xl font-semibold">No bookings yet</p>
            <a href="{{ route('equipment.index') }}" class="mt-4 inline-block bg-sky-600 text-white px-6 py-2 rounded-xl text-sm font-medium hover:bg-sky-700 transition">
                Browse Equipment
            </a>
        </div>
        @endif
    </div>
</x-app-layout>
