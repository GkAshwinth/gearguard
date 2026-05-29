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
                        <span class="font-semibold text-white">{{ $equipment->daily_rate }}</span>
                    </div>
                    
                    <hr class="border-slate-800">
                    
                    @if(auth()->user()->is_pro)
                        <div class="flex justify-between items-center text-sm font-bold text-slate-400 line-through">
                            <span>Subtotal</span>
                            <span>LKR {{ number_format($originalTotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm font-bold text-emerald-400 mt-2">
                            <span class="flex items-center gap-1.5">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                PRO Discount (15%)
                            </span>
                            <span>- LKR {{ number_format($discount, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between items-center text-lg font-bold text-indigo-400 mt-3">
                        <span>Total</span>
                        <span>LKR {{ number_format($total, 2) }}</span>
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

            {{-- Mock Checkout Form --}}
            <form action="{{ route('bookings.store') }}" method="POST" class="space-y-5 mt-6">
                @csrf
                <input type="hidden" name="equipment_id" value="{{ $equipment->id }}">
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Name on Card</label>
                    <input type="text" required class="w-full bg-white border border-slate-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-slate-900" placeholder="John Doe">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Card Number</label>
                    <input type="text" required pattern="[0-9\s]{16,19}" title="Please enter a valid 16-digit card number" class="w-full bg-white border border-slate-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-slate-900" placeholder="0000 0000 0000 0000">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Expiry Date</label>
                        <input type="text" required pattern="(0[1-9]|1[0-2])\/[0-9]{2}" title="Please enter a valid expiry date (MM/YY)" class="w-full bg-white border border-slate-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-slate-900" placeholder="MM/YY">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">CVV</label>
                        <input type="text" required pattern="[0-9]{3,4}" title="Please enter a 3 or 4 digit CVV" class="w-full bg-white border border-slate-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-slate-900" placeholder="123">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit"
                            class="w-full py-3 bg-[#ea580c] hover:bg-[#c2410c] rounded-md font-bold text-lg text-white transition shadow-sm">
                        Pay LKR {{ number_format($total, 2) }}
                    </button>
                </div>

                <div class="text-center text-xs text-slate-500 mt-4 leading-relaxed">
                    By clicking the button, you agree to our <a href="#" class="underline hover:text-slate-300">Terms & Conditions</a>.
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
