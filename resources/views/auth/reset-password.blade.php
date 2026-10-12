<x-layout title="Set New Password - Raimart">
    <section class="mx-auto max-w-md px-4 pt-4 pb-12 sm:py-16 sm:px-6 lg:px-8">
        <div class="rounded-2xl bg-white p-6 sm:p-8 shadow-sm ring-1 ring-brand-navy/5">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 mb-4">
                <i class="fa-solid fa-lock text-2xl"></i>
            </div>

            <h1 class="text-center font-serif text-2xl font-bold text-brand-navy">Set New Password</h1>
            <p class="mt-2 text-center text-sm text-brand-navy/60 leading-relaxed">
                Code verified! Enter your new password below for <br>
                <strong class="text-brand-navy font-semibold">{{ $email }}</strong>
            </p>

            @if ($errors->any())
                <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-600 flex items-start gap-3">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5 shrink-0"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @if (session('status'))
                <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700 flex items-start gap-3">
                    <i class="fa-solid fa-circle-check mt-0.5 shrink-0"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" class="mt-6 space-y-5">
                @csrf

                <div>
                    <label for="password" class="text-sm font-medium text-brand-navy">New Password</label>
                    <div class="relative mt-1">
                        <input type="password" id="password" name="password" required minlength="8" autofocus
                            placeholder="At least 8 characters"
                            class="w-full rounded-xl border border-brand-navy/15 px-4 py-2.5 pr-11 text-sm focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                        <button type="button" onclick="togglePasswordVisibility('password', this)" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-brand-orange focus:outline-none cursor-pointer transition-colors" aria-label="Toggle password visibility">
                            <i class="fa-solid fa-eye text-sm eye-open"></i>
                            <i class="fa-solid fa-eye-slash text-sm eye-closed" style="display: none;"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="text-sm font-medium text-brand-navy">Confirm New Password</label>
                    <div class="relative mt-1">
                        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                            placeholder="Re-enter your new password"
                            class="w-full rounded-xl border border-brand-navy/15 px-4 py-2.5 pr-11 text-sm focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                        <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-brand-orange focus:outline-none cursor-pointer transition-colors" aria-label="Toggle confirm password visibility">
                            <i class="fa-solid fa-eye text-sm eye-open"></i>
                            <i class="fa-solid fa-eye-slash text-sm eye-closed" style="display: none;"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full rounded-xl bg-brand-orange py-3 text-sm font-semibold text-white transition hover:bg-brand-navy shadow-sm hover:shadow cursor-pointer flex items-center justify-center gap-2 mt-2">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    Update Password
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-brand-navy/10 text-center">
                <a href="{{ route('login') }}" class="font-medium text-brand-navy/60 hover:text-brand-orange transition inline-flex items-center gap-1.5 text-xs">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    Back to Login
                </a>
            </div>
        </div>
    </section>

    <script>
        function togglePasswordVisibility(inputId, button) {
            const input = document.getElementById(inputId) || button.closest('.relative')?.querySelector('input');
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
                    button.classList.remove('text-gray-400');
                } else {
                    openIcon.style.display = '';
                    closedIcon.style.display = 'none';
                    button.classList.remove('text-brand-orange');
                    button.classList.add('text-gray-400');
                }
            }
        }
    </script>
</x-layout>
