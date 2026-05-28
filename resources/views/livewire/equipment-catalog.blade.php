<div>
    <div class="flex flex-col md:flex-row gap-4 mb-6">
        <input 
            wire:model.live.debounce.300ms="search" 
            type="text" 
            placeholder="Search equipment..." 
            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm"
        >
        <select 
            wire:model.live="category" 
            class="block w-full md:w-1/3 border-gray-300 rounded-md shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm"
        >
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
            @endforeach
        </select>
    </div>

    <div wire:loading class="text-sky-600 font-semibold mb-4 animate-pulse">
        Updating catalog...
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" wire:loading.class="opacity-50 transition-opacity duration-300">
        @forelse($equipments as $item)
            <div class="bg-white border rounded-2xl shadow-sm overflow-hidden group">
                <img src="{{ $item->image_url ?? asset('images/placeholder.jpg') }}" alt="{{ $item->name }}" class="h-48 w-full object-cover transition-transform duration-300 group-hover:scale-105">
                <div class="p-4">
                    <h3 class="text-lg font-bold text-gray-900">{{ $item->name }}</h3>
                    <p class="text-sm text-gray-500 mb-4">{{ $item->category }}</p>
                    <div class="flex justify-between items-center mt-auto">
                        <span class="text-sky-600 font-bold">{{ $item->daily_rate }}/day</span>
                        <a href="{{ route('equipment.show', $item->id) }}" class="bg-sky-600 text-white px-4 py-2 rounded-md hover:bg-sky-700 text-sm font-medium transition-colors">View</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500 bg-gray-50 rounded-2xl border border-dashed">
                No equipment found matching your criteria.
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $equipments->links() }}
    </div>
</div>
