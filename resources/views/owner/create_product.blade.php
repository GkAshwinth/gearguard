<x-app-layout title="Add Equipment">
    <div class="max-w-2xl mx-auto px-4 py-10">
        <a href="{{ route('owner.inventory') }}" class="text-sm text-sky-600 hover:underline mb-6 inline-block">← Back to Inventory</a>
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Add New Equipment</h1>

        <form action="{{ route('owner.equipment.store') }}" method="POST" enctype="multipart/form-data"
              class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
            @csrf

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <ul class="text-sm text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Equipment Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                <select name="category" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <option value="">Select category</option>
                    @foreach(['Cameras','Lenses','Lighting','Audio','Drones','Other'] as $cat)
                        <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Daily Rate (LKR) *</label>
                <input type="number" name="daily_rate" value="{{ old('daily_rate') }}" min="1" step="0.01" required
                       class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Equipment Image</label>
                <input type="file" name="image" accept="image/*"
                       class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                <p class="text-xs text-gray-400 mt-1">Max 4MB. JPEG, PNG, WebP accepted.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <option value="available">Available</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('owner.inventory') }}"
                   class="flex-1 text-center border border-gray-200 text-gray-600 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="flex-1 bg-sky-600 text-white py-2.5 rounded-xl font-semibold hover:bg-sky-700 transition text-sm">
                    Add Equipment
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
