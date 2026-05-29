<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <a href="{{ route('home') }}" class="flex justify-center mb-6 hover:opacity-80 transition">
                <x-application-mark class="h-16 w-auto" />
            </a>
        </x-slot>

        <h2 class="text-3xl font-extrabold text-white text-center mb-2">Sign in to GearGuard</h2>
        <p class="text-sm text-slate-400 text-center mb-8">
            Or <a href="{{ route('register') }}" class="text-orange-500 hover:text-orange-400 font-semibold">create a new account</a>
        </p>

        <x-validation-errors class="mb-6" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <input id="email" class="block w-full border border-slate-800 focus:border-orange-500 focus:ring-orange-500 bg-slate-800/60 text-slate-100 placeholder-slate-400 rounded-xl px-4 py-3 shadow-sm" type="email" name="email" :value="old('email')" placeholder="Email address" required autofocus autocomplete="username" />
            </div>

            <div x-data="{ show: false }">
                <div class="relative">
                    <input id="password" class="block w-full border border-slate-800 focus:border-orange-500 focus:ring-orange-500 bg-slate-800/60 text-slate-100 placeholder-slate-400 rounded-xl px-4 py-3 pr-10 shadow-sm" x-bind:type="show ? 'text' : 'password'" name="password" placeholder="Password" required autocomplete="current-password" />
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                        <svg x-show="!show" class="h-5 w-5 text-slate-400 hover:text-slate-200 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="show" style="display: none;" class="h-5 w-5 text-slate-400 hover:text-slate-200 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between mt-6">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-slate-300 font-semibold">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm text-orange-500 hover:text-orange-400 font-semibold transition" href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            <div class="mt-6">
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-6 py-3.5 bg-orange-600 hover:bg-orange-500 border border-transparent rounded-xl font-bold text-base text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 focus:ring-offset-slate-900 disabled:opacity-50 transition shadow-lg">
                    <svg class="h-4 w-4 text-orange-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    {{ __('Sign in') }}
                </button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
