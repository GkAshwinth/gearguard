<x-app-layout title="Welcome">
    {{-- Hero Section --}}
    <section class="bg-gradient-to-br from-sky-600 to-sky-800 text-white py-24">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h1 class="text-5xl font-bold mb-6">Professional Gear.<br>Rented Smarter.</h1>
            <p class="text-xl text-sky-100 mb-10 max-w-2xl mx-auto">
                GearGuard is the rental management platform for photographers, filmmakers, and creative professionals in Sri Lanka.
            </p>
            <div class="flex gap-4 justify-center">
                <a href="{{ route('equipment.index') }}"
                   class="bg-white text-sky-700 font-semibold px-8 py-3 rounded-xl hover:bg-sky-50 transition text-lg">
                    Browse Gear
                </a>
                @guest
                <a href="{{ route('register') }}"
                   class="border-2 border-white text-white font-semibold px-8 py-3 rounded-xl hover:bg-white hover:text-sky-700 transition text-lg">
                    Get Started Free
                </a>
                @endguest
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <section class="max-w-7xl mx-auto px-4 py-16">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            @foreach([['24+', 'Items Available'], ['100%', 'Conflict-Free'], ['2s', 'Availability Check'], ['24/7', 'Access']] as [$stat, $label])
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="text-3xl font-bold text-sky-600">{{ $stat }}</div>
                <div class="text-gray-500 text-sm mt-1">{{ $label }}</div>
            </div>
            @endforeach
        </div>
    </section>

    {{-- Featured Gear --}}
    <section class="max-w-7xl mx-auto px-4 pb-16">
        <h2 class="text-2xl font-bold mb-8 text-gray-900">Popular Equipment</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach(\App\Models\Equipment::available()->take(8)->get() as $item)
            <a href="{{ route('equipment.show', $item) }}"
               class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-md transition group">
                <div class="h-48 overflow-hidden">
                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                </div>
                <div class="p-4">
                    <span class="text-xs text-sky-600 font-medium bg-sky-50 px-2 py-1 rounded-full">{{ $item->category }}</span>
                    <h3 class="font-semibold text-gray-900 mt-2">{{ $item->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ Str::limit($item->description, 50) }}</p>
                    <p class="text-sky-600 font-bold mt-2">{{ $item->daily_rate }}<span class="text-gray-400 font-normal text-xs">/day</span></p>
                </div>
            </a>
            @endforeach
        </div>
        <div class="text-center mt-8">
            <a href="{{ route('equipment.index') }}" class="bg-sky-600 text-white px-8 py-3 rounded-xl font-semibold hover:bg-sky-700 transition">
                View All Equipment →
            </a>
        </div>
    </section>
</x-app-layout>
