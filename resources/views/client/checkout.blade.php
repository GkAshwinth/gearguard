<x-app-layout title="Confirm Booking">
    <div class="max-w-4xl mx-auto px-4 py-12 grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
        
        {{-- Left Column: Equipment & Details Summary --}}
        <div>
            <h1 class="text-3xl font-extrabold text-white mb-6">Confirm Your Booking</h1>

            <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-xl p-6 mb-6">
                <div class="flex gap-4">
                    <img src="{{ $equipment->image_url }}" alt="{{ $equipment->name }}"
                         class="w-24 h-24 object-cover rounded-xl border border-slate-800">
                    <div>
                        <span class="text-xs text-indigo-400 font-bold bg-indigo-950/60 border border-indigo-900/50 px-2.5 py-1 rounded-full uppercase tracking-wider">{{ $equipment->category }}</span>
                        <h2 class="text-xl font-bold text-white mt-3">{{ $equipment->name }}</h2>
                        <p class="text-slate-400 text-sm mt-1 line-clamp-2 leading-relaxed">{{ $equipment->description }}</p>
                    </div>
                </div>

                <hr class="border-slate-800 my-6">

                <div class="space-y-4 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400">Start date</span>
                        <span class="font-semibold text-white">{{ \Carbon\Carbon::parse($startDate)->format('D, M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400">End date</span>
                        <span class="font-semibold text-white">{{ \Carbon\Carbon::parse($endDate)->format('D, M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400">Duration</span>
                        <span class="font-semibold text-white">{{ $days }} day{{ $days > 1 ? 's' : '' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400">Daily rate</span>
                        <span class="font-semibold text-white">LKR {{ number_format($equipment->daily_rate) }}</span>
                    </div>
                    
                    <hr class="border-slate-800">
                    
                    <div class="flex justify-between items-center text-lg font-bold text-indigo-400">
                        <span>Total</span>
                        <span>LKR {{ number_format($total) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-indigo-950/40 border border-indigo-900/40 rounded-2xl p-5 text-sm text-indigo-300 leading-relaxed shadow-sm">
                <span class="font-bold text-indigo-200">ℹ Approval Required:</span> Your booking will be set to <strong class="text-indigo-200">pending</strong> until approved by the admin. You will see status updates in your client dashboard.
            </div>
        </div>

        {{-- Right Column: Screenshot #4 Payment Details Card --}}
        <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-8">
            <h2 class="text-2xl font-bold text-white mb-6">Payment Details</h2>

            {{-- Method Tabs --}}
            <div class="grid grid-cols-2 gap-4 mb-6">
                <button type="button" class="w-full py-3 text-center rounded-xl font-bold bg-[#5c54f1] text-white transition shadow-lg">
                    Credit Card
                </button>
                <button type="button" class="w-full py-3 text-center rounded-xl font-bold bg-slate-800/80 border border-slate-700/50 text-slate-400 hover:text-slate-200 transition">
                    Cash on Delivery
                </button>
            </div>

            {{-- Stripe Checkout Form --}}
            <form action="{{ route('bookings.store') }}" method="POST" class="space-y-6 mt-6">
                @csrf
                <input type="hidden" name="equipment_id" value="{{ $equipment->id }}">
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">

                <div class="bg-slate-800/50 border border-slate-700/50 rounded-xl p-5 text-center">
                    <svg class="h-10 w-10 text-slate-400 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <h3 class="text-white font-semibold mb-1">Secure Payment via Stripe</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        You will be redirected to Stripe's highly secure payment gateway to complete your transaction. No card details are stored on our servers.
                    </p>
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 px-6 py-4 bg-[#635BFF] hover:bg-[#5249E5] border border-transparent rounded-xl font-bold text-lg text-white focus:outline-none focus:ring-2 focus:ring-[#635BFF] focus:ring-offset-2 focus:ring-offset-slate-900 transition shadow-lg">
                        Proceed to Secure Checkout (LKR {{ number_format($total) }})
                    </button>
                </div>

                <div class="text-center text-xs text-slate-500 mt-4 leading-relaxed">
                    By clicking the button, you agree to our <a href="#" class="underline hover:text-slate-300">Terms & Conditions</a>.
                    <div class="flex items-center justify-center gap-1.5 mt-3 text-slate-400 font-medium">
                        <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span>Protected by Stripe</span>
                    </div>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
