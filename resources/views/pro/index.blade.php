<x-app-layout title="GearGuard Pro">
    <div class="max-w-4xl mx-auto px-4 py-16 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-yellow-600 mb-6">
            Upgrade to GearGuard Pro
        </h1>
        <p class="text-xl text-slate-300 mb-12 max-w-2xl mx-auto leading-relaxed">
            Take your production to the next level. GearGuard Pro is the ultimate subscription for professional videographers and agencies who rent frequently.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
            <!-- Benefit 1 -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-8 shadow-xl hover:border-yellow-500/50 transition">
                <div class="bg-yellow-500/10 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="h-8 w-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Zero Deposits</h3>
                <p class="text-slate-400 text-sm leading-relaxed">Skip the massive security holds. Pro members get instant approval with no upfront deposits required.</p>
            </div>

            <!-- Benefit 2 -->
            <div class="bg-slate-900 border border-yellow-600/50 rounded-2xl p-8 shadow-2xl shadow-yellow-900/20 transform md:-translate-y-4">
                <div class="bg-yellow-500/10 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="h-8 w-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">15% Flat Discount</h3>
                <p class="text-slate-400 text-sm leading-relaxed">Enjoy an automatic 15% discount off the daily rate of every single piece of equipment you rent.</p>
            </div>

            <!-- Benefit 3 -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-8 shadow-xl hover:border-yellow-500/50 transition">
                <div class="bg-yellow-500/10 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="h-8 w-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Priority Support</h3>
                <p class="text-slate-400 text-sm leading-relaxed">Get a dedicated 24/7 hotline and priority equipment replacement if anything goes wrong on set.</p>
            </div>
        </div>

        <div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-10 max-w-2xl mx-auto">
            <h2 class="text-3xl font-bold text-white mb-2">LKR 4,999 <span class="text-lg text-slate-400 font-normal">/ month</span></h2>
            <p class="text-slate-400 mb-8">Cancel anytime. Pays for itself in just one average rental.</p>

            @if(auth()->check() && auth()->user()->is_pro)
                <div class="bg-emerald-500/10 border border-emerald-500/50 rounded-xl py-4 px-6 text-emerald-400 font-bold text-lg inline-flex items-center gap-2">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    You are an active Pro Member!
                </div>
            @else
                <form action="{{ route('pro.subscribe') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-400 hover:to-yellow-500 text-white font-bold text-lg py-4 px-12 rounded-full shadow-lg hover:shadow-yellow-500/25 transition transform hover:-translate-y-1">
                        Subscribe Now
                    </button>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
