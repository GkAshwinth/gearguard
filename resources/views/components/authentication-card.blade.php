<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-slate-950">
    <div>
        {{ $logo }}
    </div>

    <div class="w-full sm:max-w-md mt-6 px-8 py-8 bg-slate-900 border border-slate-800 shadow-2xl overflow-hidden rounded-2xl">
        {{ $slot }}
    </div>
</div>
