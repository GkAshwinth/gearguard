<x-app-layout title="Confirm Booking">
    <div class="max-w-2xl mx-auto px-4 py-10">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Confirm Your Booking</h1>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <div class="flex gap-4">
                <img src="{{ $equipment->image_url }}" alt="{{ $equipment->name }}"
                     class="w-24 h-24 object-cover rounded-xl">
                <div>
                    <span class="text-xs text-sky-600 font-medium bg-sky-50 px-2 py-1 rounded-full">{{ $equipment->category }}</span>
                    <h2 class="text-xl font-bold text-gray-900 mt-1">{{ $equipment->name }}</h2>
                    <p class="text-gray-500 text-sm">{{ $equipment->description }}</p>
                </div>
            </div>

            <hr class="my-5">

            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Start date</span>
                    <span class="font-medium">{{ \Carbon\Carbon::parse($startDate)->format('D, M d, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">End date</span>
                    <span class="font-medium">{{ \Carbon\Carbon::parse($endDate)->format('D, M d, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Duration</span>
                    <span class="font-medium">{{ $days }} day{{ $days > 1 ? 's' : '' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Daily rate</span>
                    <span class="font-medium">LKR {{ number_format($equipment->daily_rate) }}</span>
                </div>
                <hr>
                <div class="flex justify-between text-lg font-bold text-sky-600">
                    <span>Total</span>
                    <span>LKR {{ number_format($total) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 text-sm text-amber-800">
            <strong>Note:</strong> Your booking will be <strong>pending</strong> until approved by the admin.
            You will see status updates in your dashboard.
        </div>

        <form action="{{ route('booking.store') }}" method="POST">
            @csrf
            <input type="hidden" name="equipment_id" value="{{ $equipment->id }}">
            <input type="hidden" name="start_date" value="{{ $startDate }}">
            <input type="hidden" name="end_date" value="{{ $endDate }}">

            <div class="flex gap-3">
                <a href="{{ route('equipment.show', $equipment) }}"
                   class="flex-1 text-center border border-gray-200 text-gray-600 py-3 rounded-xl font-medium hover:bg-gray-50 transition">
                    ← Back
                </a>
                <button type="submit"
                        class="flex-1 bg-sky-600 text-white py-3 rounded-xl font-semibold hover:bg-sky-700 transition">
                    Confirm Booking
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
