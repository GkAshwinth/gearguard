@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-slate-800/80 focus:border-indigo-500 focus:ring-indigo-500 bg-slate-800/80 text-slate-100 placeholder-slate-500 rounded-xl shadow-sm']) !!}>
