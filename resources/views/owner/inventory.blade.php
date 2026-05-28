<x-app-layout title="Inventory">
    <div class="max-w-7xl mx-auto px-4 py-10">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Inventory</h1>
                <p class="text-gray-500 text-sm">{{ $equipment->total() }} items total</p>
            </div>
            <a href="{{ route('owner.equipment.create') }}"
               class="bg-sky-600 text-white px-5 py-2.5 rounded-xl font-medium hover:bg-sky-700 transition text-sm">
                + Add Equipment
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3 text-left">Item</th>
                        <th class="px-4 py-3 text-left">Category</th>
                        <th class="px-4 py-3 text-right">Daily Rate</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Bookings</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($equipment as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $item->image_url }}" class="w-10 h-10 object-cover rounded-lg" alt="">
                                <span class="font-medium text-gray-900">{{ $item->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $item->category }}</td>
                        <td class="px-4 py-3 text-right font-medium">LKR {{ number_format($item->daily_rate) }}</td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $sc = ['available'=>'green','rented'=>'yellow','maintenance'=>'red'];
                                $s = $sc[$item->status] ?? 'gray';
                            @endphp
                            <span class="bg-{{ $s }}-100 text-{{ $s }}-800 text-xs font-semibold px-2 py-1 rounded-full capitalize">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-500">{{ $item->bookings_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('owner.equipment.edit', $item) }}"
                                   class="text-xs text-sky-600 border border-sky-200 px-3 py-1 rounded-lg hover:bg-sky-50 transition">
                                    Edit
                                </a>
                                <form action="{{ route('owner.equipment.destroy', $item) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Delete {{ $item->name }}? This cannot be undone.')"
                                            class="text-xs text-red-500 border border-red-200 px-3 py-1 rounded-lg hover:bg-red-50 transition">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $equipment->links() }}</div>
    </div>
</x-app-layout>
