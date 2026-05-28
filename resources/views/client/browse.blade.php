<x-app-layout title="Browse Equipment">
    <div class="max-w-7xl mx-auto px-4 py-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Browse Equipment</h1>
                <p class="text-gray-500">Premium equipment available for rental</p>
            </div>
            <div class="w-full md:w-auto">
                <livewire:quick-stats />
            </div>
        </div>

        @livewire('equipment-catalog')
    </div>
</x-app-layout>
