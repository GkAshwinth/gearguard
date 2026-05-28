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

        {{-- Pending Bookings --}}
        <section class="mb-10">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Pending Approvals
                @if($pending->count())
                    <span class="bg-yellow-100 text-yellow-800 text-sm px-2 py-0.5 rounded-full ml-2">{{ $pending->count() }}</span>
                @endif
            </h2>

            @if($pending->count())
            <div class="space-y-3">
                @foreach($pending as $booking)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-col sm:flex-row sm:items-center gap-4">
                    <img src="{{ $booking->equipment->image_url }}" class="w-16 h-16 object-cover rounded-lg flex-shrink-0" alt="">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900">{{ $booking->equipment->name }}</p>
                        <p class="text-sm text-gray-500">
                            By {{ $booking->user->name }} ({{ $booking->user->email }})
                        </p>
                        <p class="text-sm text-gray-500">
                            {{ $booking->start_date->format('M d') }} → {{ $booking->end_date->format('M d, Y') }}
                            · LKR {{ number_format($booking->total_cost) }}
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <form action="{{ route('owner.booking.status', $booking) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="bg-green-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-green-700 transition font-medium">
                                Approve
                            </button>
                        </form>
                        <form action="{{ route('owner.booking.status', $booking) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="bg-red-50 text-red-600 text-sm px-4 py-2 rounded-lg hover:bg-red-100 border border-red-200 transition font-medium">
                                Reject
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-400 text-sm bg-gray-50 rounded-xl p-6 text-center">No pending bookings. You're all caught up!</p>
            @endif
        </section>

        {{-- All Bookings Table --}}
        <section>
            <h2 class="text-lg font-bold text-gray-900 mb-4">All Bookings</h2>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                        <tr>
                            <th class="px-4 py-3 text-left">#</th>
                            <th class="px-4 py-3 text-left">Equipment</th>
                            <th class="px-4 py-3 text-left">Customer</th>
                            <th class="px-4 py-3 text-left">Dates</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($all as $booking)
                        @php
                            $colors = ['pending'=>'yellow','approved'=>'green','rejected'=>'red','completed'=>'blue','cancelled'=>'gray'];
                            $c = $colors[$booking->status] ?? 'gray';
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-400">{{ $booking->id }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $booking->equipment->name }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $booking->user->name }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $booking->start_date->format('M d') }} → {{ $booking->end_date->format('M d') }}</td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900">LKR {{ number_format($booking->total_cost) }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="bg-{{ $c }}-100 text-{{ $c }}-800 text-xs font-semibold px-2 py-1 rounded-full capitalize">
                                    {{ $booking->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($booking->status === 'approved')
                                <form action="{{ route('owner.booking.status', $booking) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="completed">
                                    <button class="text-xs text-blue-600 hover:underline">Mark Completed</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $all->links() }}</div>
        </section>
    </div>
</x-app-layout>
