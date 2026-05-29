<x-app-layout title="Welcome">
    {{-- Premium Hero Section (Screenshot #2 Split Style) --}}
    <section class="relative bg-[#0b0f19] border-b border-slate-900 overflow-hidden min-h-[500px] flex items-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full py-16 sm:py-24 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            {{-- Left column: Typography + Actions --}}
            <div class="z-10">
                <h1 class="text-4xl sm:text-6xl font-extrabold text-white tracking-tight leading-tight">
                    Premium Gear for<br>
                    <span class="text-[#5c54f1]">Professional Creators</span>
                </h1>
                <p class="mt-6 text-lg sm:text-xl text-slate-400 max-w-lg leading-relaxed">
                    Rent the latest cameras, lenses, lighting, and audio equipment. Verified quality, affordable daily rates, and instant booking.
                </p>
                <div class="mt-10 flex flex-wrap gap-4">
                    <a href="{{ route('equipment.index') }}"
                       class="bg-[#5c54f1] hover:bg-[#4b44d3] text-white font-bold px-8 py-4 rounded-xl shadow-lg transition text-base">
                        Browse Equipment
                    </a>
                    @guest
                    <a href="{{ route('register') }}"
                       class="bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/50 text-white font-bold px-8 py-4 rounded-xl transition text-base">
                        Join Now
                    </a>
                    @endguest
                </div>
            </div>
            
            {{-- Right column: Large Graphic / Crop Image (matching cat/gray snout aesthetic) --}}
            <div class="relative hidden lg:block h-[450px] rounded-3xl overflow-hidden shadow-2xl border border-slate-800">
                <img src="https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&w=1000&q=80" 
                     class="w-full h-full object-cover grayscale opacity-90 contrast-125" 
                     alt="Professional Equipment Detail">
                <div class="absolute inset-0 bg-gradient-to-r from-[#0b0f19] via-transparent to-transparent"></div>
            </div>
        </div>
    </section>

    {{-- Gear Categories Section (Screenshot #2 Style) --}}
    <section class="bg-[#0b0f19] py-20 border-b border-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-extrabold text-white text-center mb-12">Gear Categories</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Categories Loop --}}
                @foreach([
                    ['Cameras', 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=600&q=80'],
                    ['Lenses', 'https://images.unsplash.com/photo-1617005082133-548c4dd27f35?auto=format&fit=crop&w=600&q=80'],
                    ['Lighting', 'https://images.unsplash.com/photo-1527011046414-4781f1f94f8c?auto=format&fit=crop&w=600&q=80'],
                    ['Audio', 'https://images.unsplash.com/photo-1590845947698-8924d7409b56?auto=format&fit=crop&w=600&q=80']
                ] as [$title, $image])
                <a href="{{ route('equipment.index', ['category' => $title]) }}" 
                   class="relative group h-64 rounded-2xl overflow-hidden shadow-md border border-slate-800/50 block">
                    <img src="{{ $image }}" 
                         alt="{{ $title }}" 
                         class="w-full h-full object-cover transition duration-500 group-hover:scale-105 filter brightness-75">
                    <div class="absolute inset-0 bg-slate-950/40 group-hover:bg-slate-950/20 transition duration-300"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-2xl font-extrabold text-white group-hover:text-indigo-300 transition duration-300">{{ $title }}</span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Popular Equipment Section --}}
    <section class="bg-[#0b0f19] py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-extrabold text-white text-center mb-12">Popular Equipment</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach(\App\Models\Equipment::available()->take(8)->get() as $item)
                <a href="{{ route('equipment.show', $item) }}"
                   class="bg-slate-900/40 hover:bg-slate-900/80 rounded-2xl overflow-hidden border border-slate-800/80 shadow-md hover:shadow-lg transition-all duration-300 group block">
                    <div class="h-48 overflow-hidden relative">
                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
                             class="w-full h-full object-cover group-hover:scale-102 transition duration-300">
                    </div>
                    <div class="p-5">
                        <span class="text-xs text-indigo-400 font-bold bg-indigo-950/60 border border-indigo-900/50 px-2.5 py-1 rounded-full uppercase tracking-wider">{{ $item->category }}</span>
                        <h3 class="font-bold text-white mt-3 text-lg group-hover:text-[#5c54f1] transition">{{ $item->name }}</h3>
                        <p class="text-sm text-slate-400 mt-2 line-clamp-2 leading-relaxed">{{ $item->description }}</p>
                        <div class="mt-4 pt-4 border-t border-slate-800 flex items-center justify-between">
                            <span class="text-slate-400 text-xs">Daily Rate</span>
                            <p class="text-[#5c54f1] font-extrabold text-base">LKR {{ $item->daily_rate }}<span class="text-slate-400 font-normal text-xs">/day</span></p>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            
            <div class="text-center mt-12">
                <a href="{{ route('equipment.index') }}" 
                   class="inline-flex items-center justify-center bg-slate-900/80 hover:bg-slate-800/80 border border-slate-700/50 text-white px-8 py-3.5 rounded-xl font-bold transition shadow-md">
                    View All Equipment <span class="ml-2">→</span>
                </a>
            </div>
        </div>
    </section>
</x-app-layout>
