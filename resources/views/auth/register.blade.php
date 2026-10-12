<x-layout title="Register - Raimart">
    <section class="mx-auto max-w-xl px-4 pt-2 sm:pt-4 md:pt-6 pb-8 sm:pb-12 sm:px-6">
        <div class="rounded-2xl bg-white p-6 sm:p-9 shadow-sm border border-brand-navy/10 ring-1 ring-black/5">
            <h1 class="text-center font-serif text-2xl sm:text-3xl font-bold text-brand-navy">Create Account</h1>
            <p class="mt-1.5 text-center text-sm text-brand-navy/60">Join Raimart for faster checkout & easy order tracking</p>

            @if ($errors->any())
                <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-600">
                    <div class="flex items-start gap-2.5">
                        <i class="fa-solid fa-circle-exclamation text-red-500 mt-0.5 shrink-0"></i>
                        <ul class="list-disc pl-4 space-y-1 font-medium text-xs sm:text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="mt-6 space-y-4 sm:space-y-5">
                @csrf

                <div>
                    <label for="register-name" class="block text-sm font-semibold text-brand-navy">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" id="register-name" name="name" value="{{ old('name') }}" required autofocus
                        placeholder="e.g. Rahim Ahmed"
                        class="mt-1.5 w-full rounded-xl border border-brand-navy/15 bg-gray-50/50 px-4 py-2.5 text-sm focus:border-brand-orange focus:bg-white focus:outline-none transition shadow-2xs">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="register-email" class="block text-sm font-semibold text-brand-navy">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" id="register-email" name="email" value="{{ old('email') }}" required
                            placeholder="you@example.com"
                            class="mt-1.5 w-full rounded-xl border border-brand-navy/15 bg-gray-50/50 px-4 py-2.5 text-sm focus:border-brand-orange focus:bg-white focus:outline-none transition shadow-2xs">
                    </div>

                    <div>
                        <label for="register-phone" class="block text-sm font-semibold text-brand-navy">Phone <span class="text-xs font-normal text-brand-navy/40">(optional)</span></label>
                        <input type="tel" id="register-phone" name="phone" value="{{ old('phone') }}"
                            placeholder="01700000000"
                            class="mt-1.5 w-full rounded-xl border border-brand-navy/15 bg-gray-50/50 px-4 py-2.5 text-sm focus:border-brand-orange focus:bg-white focus:outline-none transition shadow-2xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="register-password" class="block text-sm font-semibold text-brand-navy">Password <span class="text-red-500">*</span></label>
                        <div class="relative mt-1.5">
                            <input type="password" id="register-password" name="password" required
                                placeholder="Minimum 8 characters"
                                class="w-full rounded-xl border border-brand-navy/15 bg-gray-50/50 px-4 py-2.5 pr-11 text-sm focus:border-brand-orange focus:bg-white focus:outline-none transition shadow-2xs">
                            <button type="button" onclick="togglePasswordVisibility('register-password', this)" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-brand-navy/40 hover:text-brand-orange cursor-pointer transition-colors" aria-label="Toggle password visibility">
                                <i class="fa-solid fa-eye text-sm eye-open"></i>
                                <i class="fa-solid fa-eye-slash text-sm eye-closed" style="display: none;"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="register-password-confirmation" class="block text-sm font-semibold text-brand-navy">Confirm Password <span class="text-red-500">*</span></label>
                        <div class="relative mt-1.5">
                            <input type="password" id="register-password-confirmation" name="password_confirmation" required
                                placeholder="Re-enter password"
                                class="w-full rounded-xl border border-brand-navy/15 bg-gray-50/50 px-4 py-2.5 pr-11 text-sm focus:border-brand-orange focus:bg-white focus:outline-none transition shadow-2xs">
                            <button type="button" onclick="togglePasswordVisibility('register-password-confirmation', this)" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-brand-navy/40 hover:text-brand-orange cursor-pointer transition-colors" aria-label="Toggle confirm password visibility">
                                <i class="fa-solid fa-eye text-sm eye-open"></i>
                                <i class="fa-solid fa-eye-slash text-sm eye-closed" style="display: none;"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full rounded-xl bg-brand-orange py-3 text-sm font-bold text-white shadow-md shadow-brand-orange/20 transition-all duration-200 hover:bg-brand-navy hover:shadow-lg cursor-pointer flex items-center justify-center gap-2">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>Create Account</span>
                    </button>
                </div>
            </form>

            <div class="relative my-6 sm:my-7">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-brand-navy/10"></div></div>
                <div class="relative flex justify-center text-xs uppercase"><span class="bg-white px-3 text-brand-navy/40 font-medium">Or</span></div>
            </div>

            <a href="{{ route('auth.google') }}"
                class="flex w-full items-center justify-center gap-3 rounded-xl border border-brand-navy/15 bg-white py-2.5 px-4 text-sm font-semibold text-brand-navy transition hover:bg-gray-50 hover:border-brand-navy/30 shadow-2xs cursor-pointer">
                <svg class="h-4 w-4" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span>Continue with Google</span>
            </a>

            <p class="mt-6 text-center text-sm text-brand-navy/60">
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold text-brand-orange hover:underline">Sign in</a>
            </p>
        </div>
    </section>

    <script>
        // Inline fallback for togglePasswordVisibility if not already available
        if (typeof window.togglePasswordVisibility !== 'function') {
            window.togglePasswordVisibility = function (inputOrId, button) {
                const input = typeof inputOrId === 'string'
                    ? (document.getElementById(inputOrId) || button.closest('.relative')?.querySelector('input'))
                    : inputOrId;
                if (!input) return;

                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';

                const openIcon = button.querySelector('.eye-open');
                const closedIcon = button.querySelector('.eye-closed');

                if (openIcon && closedIcon) {
                    if (isPassword) {
                        openIcon.style.display = 'none';
                        closedIcon.style.display = '';
                        button.classList.add('text-brand-orange');
                    } else {
                        openIcon.style.display = '';
                        closedIcon.style.display = 'none';
                        button.classList.remove('text-brand-orange');
                    }
                }
            };
        }
    </script>
</x-layout>
