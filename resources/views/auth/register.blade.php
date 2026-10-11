<x-layout title="Register - Raimart">
    <section class="mx-auto max-w-md px-4 py-14 sm:px-6 lg:px-8">
        <div class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-brand-navy/5">
            <h1 class="text-center font-serif text-2xl font-bold">Create Account</h1>
            <p class="mt-1 text-center text-sm text-brand-navy/50">Join Raimart for faster checkout</p>

            @if ($errors->any())
                <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                    <ul class="list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label class="text-sm font-medium">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                        class="mt-1 w-full rounded-lg border border-brand-navy/15 px-4 py-2.5 text-sm focus:border-brand-orange focus:outline-none">
                </div>

                <div>
                    <label class="text-sm font-medium">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="mt-1 w-full rounded-lg border border-brand-navy/15 px-4 py-2.5 text-sm focus:border-brand-orange focus:outline-none">
                </div>

                <div>
                    <label class="text-sm font-medium">Phone (optional)</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="mt-1 w-full rounded-lg border border-brand-navy/15 px-4 py-2.5 text-sm focus:border-brand-orange focus:outline-none">
                </div>

                <div x-data="{ show: false }">
                    <label class="text-sm font-medium">Password</label>
                    <div class="relative mt-1">
                        <input :type="show ? 'text' : 'password'" name="password" required
                            class="w-full rounded-lg border border-brand-navy/15 px-4 py-2.5 pr-10 text-sm focus:border-brand-orange focus:outline-none">
                        <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-brand-navy/50 hover:text-brand-orange">
                            <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <div x-data="{ show: false }">
                    <label class="text-sm font-medium">Confirm Password</label>
                    <div class="relative mt-1">
                        <input :type="show ? 'text' : 'password'" name="password_confirmation" required
                            class="w-full rounded-lg border border-brand-navy/15 px-4 py-2.5 pr-10 text-sm focus:border-brand-orange focus:outline-none">
                        <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-brand-navy/50 hover:text-brand-orange">
                            <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full rounded-lg bg-brand-orange py-2.5 text-sm font-semibold text-white transition hover:bg-brand-navy cursor-pointer">
                    Create Account
                </button>
            </form>

            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-brand-navy/10"></div></div>
                <div class="relative flex justify-center text-xs uppercase"><span class="bg-white px-3 text-brand-navy/40 font-medium">Or</span></div>
            </div>

            <a href="{{ route('auth.google') }}"
                class="flex w-full items-center justify-center gap-3 rounded-lg border border-brand-navy/15 bg-white py-2.5 px-4 text-sm font-semibold text-brand-navy transition hover:bg-gray-50 hover:border-brand-navy/30 shadow-xs cursor-pointer">
                <svg class="h-4 w-4" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                Continue with Google
            </a>

            <p class="mt-6 text-center text-sm text-brand-navy/60">
                Already have an account?
                <a href="{{ route('login') }}" class="font-medium text-brand-orange hover:underline">Sign in</a>
            </p>
        </div>
    </section>
</x-layout>
