@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-sm text-slate-300']) }}>
    {{ $value ?? $slot }}
</label>
