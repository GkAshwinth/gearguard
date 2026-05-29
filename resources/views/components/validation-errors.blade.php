@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'bg-red-950/40 border border-red-800/80 rounded-xl p-4 mb-4 text-red-200 text-sm']) }}>
        <div class="font-semibold text-red-400">{{ __('Whoops! Something went wrong.') }}</div>

        <ul class="mt-2 list-disc list-inside text-xs text-red-300/90 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
