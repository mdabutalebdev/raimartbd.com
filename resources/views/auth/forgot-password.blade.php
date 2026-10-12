<x-layout title="Forgot Password - Raimart">
    <section class="mx-auto max-w-md px-4 pt-4 pb-12 sm:py-16 sm:px-6 lg:px-8">
        <div class="rounded-2xl bg-white p-6 sm:p-8 shadow-sm ring-1 ring-brand-navy/5">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-brand-orange/10 text-brand-orange mb-4">
                <i class="fa-solid fa-key text-2xl"></i>
            </div>

            <h1 class="text-center font-serif text-2xl font-bold text-brand-navy">Forgot Password?</h1>
            <p class="mt-2 text-center text-sm text-brand-navy/60 leading-relaxed">
                Enter your registered email address and we'll send a 6-digit OTP code to reset your password.
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

            <form action="{{ route('password.email') }}" method="POST" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label for="email" class="text-sm font-medium text-brand-navy">Email Address</label>
                    <div class="relative mt-1">
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="you@example.com"
                            class="w-full rounded-xl border border-brand-navy/15 px-4 py-2.5 text-sm focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                    </div>
                </div>

                <button type="submit" class="w-full rounded-xl bg-brand-orange py-3 text-sm font-semibold text-white transition hover:bg-brand-navy shadow-sm hover:shadow cursor-pointer flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                    Send OTP Code
                </button>
            </form>

            <div class="mt-6 text-center text-sm">
                <a href="{{ route('login') }}" class="font-medium text-brand-navy/70 hover:text-brand-orange transition inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    Back to Login
                </a>
            </div>
        </div>
    </section>
</x-layout>
