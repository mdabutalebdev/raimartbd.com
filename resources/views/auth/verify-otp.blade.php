<x-layout title="Verify OTP - Raimart">
    <section class="mx-auto max-w-md px-4 pt-4 pb-12 sm:py-16 sm:px-6 lg:px-8">
        <div class="rounded-2xl bg-white p-6 sm:p-7 shadow-sm ring-1 ring-brand-navy/5">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-brand-orange/10 text-brand-orange mb-3">
                <i class="fa-solid fa-envelope-circle-check text-xl"></i>
            </div>

            <h1 class="text-center font-serif text-2xl font-bold text-brand-navy">Verify Your Code</h1>
            <p class="mt-1.5 text-center text-sm text-brand-navy/60">
                Enter the 6-digit OTP code sent to <br>
                <strong class="text-brand-navy font-semibold">{{ $email }}</strong>
                <a href="{{ route('password.request') }}" class="block text-xs text-brand-orange hover:underline mt-0.5">Change email?</a>
            </p>

            @if ($errors->any())
                <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-600 flex items-start gap-2.5">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5 shrink-0 text-xs"></i>
                    <span class="text-xs leading-relaxed">{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- 6-Box OTP Form with 43s timer, copy-paste and limit support --}}
            <div x-data="{
                digits: ['', '', '', '', '', ''],
                countdown: {{ $isLocked ? 0 : 43 }},
                canResend: {{ $isLocked ? 'false' : 'false' }},
                timer: null,
                init() {
                    @if(! $isLocked)
                    this.timer = setInterval(() => {
                        if (this.countdown > 0) {
                            this.countdown--;
                        } else {
                            this.canResend = true;
                            clearInterval(this.timer);
                        }
                    }, 1000);
                    @endif
                    this.$nextTick(() => {
                        if (this.$refs.box0) this.$refs.box0.focus();
                    });
                },
                handleInput(e, index) {
                    const raw = e.target.value;
                    const val = raw.replace(/[^0-9]/g, '');
                    if (val.length > 1) {
                        this.handlePasteString(val);
                        return;
                    }
                    this.digits[index] = val;
                    if (val && index < 5) {
                        this.$refs['box' + (index + 1)].focus();
                    }
                },
                handleKeyDown(e, index) {
                    if (e.key === 'Backspace') {
                        if (!this.digits[index] && index > 0) {
                            this.$refs['box' + (index - 1)].focus();
                        } else {
                            this.digits[index] = '';
                        }
                    } else if (e.key === 'ArrowLeft' && index > 0) {
                        this.$refs['box' + (index - 1)].focus();
                    } else if (e.key === 'ArrowRight' && index < 5) {
                        this.$refs['box' + (index + 1)].focus();
                    }
                },
                handlePaste(e) {
                    e.preventDefault();
                    const text = (e.clipboardData || window.clipboardData).getData('text');
                    this.handlePasteString(text);
                },
                handlePasteString(str) {
                    const cleaned = str.replace(/[^0-9]/g, '').slice(0, 6);
                    for (let i = 0; i < 6; i++) {
                        this.digits[i] = cleaned[i] || '';
                    }
                    const nextIndex = Math.min(cleaned.length, 5);
                    if (this.$refs['box' + nextIndex]) {
                        this.$refs['box' + nextIndex].focus();
                    }
                },
                get fullOtp() {
                    return this.digits.join('');
                }
            }" class="mt-5">

                <form x-ref="otpForm" action="{{ route('password.verify') }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <input type="hidden" name="otp" :value="fullOtp">

                    <div>
                        <label class="block text-center text-xs font-semibold uppercase tracking-wider text-brand-navy/50 mb-2.5">
                            Enter 6-Digit Code
                        </label>
                        <div class="flex items-center justify-center gap-2 sm:gap-2.5" @paste="handlePaste($event)">
                            <template x-for="(digit, index) in digits" :key="index">
                                <input
                                    type="text"
                                    inputmode="numeric"
                                    maxlength="1"
                                    autocomplete="off"
                                    :x-ref="'box' + index"
                                    :value="digits[index]"
                                    @input="handleInput($event, index)"
                                    @keydown="handleKeyDown($event, index)"
                                    class="h-12 w-11 sm:h-13 sm:w-12 text-center text-xl sm:text-2xl font-bold text-brand-navy bg-gray-50 focus:bg-white rounded-xl border border-brand-navy/15 focus:border-brand-orange focus:ring-2 focus:ring-brand-orange/20 outline-none transition shadow-xs"
                                >
                            </template>
                        </div>
                    </div>

                    <button
                        type="submit"
                        :disabled="fullOtp.length !== 6"
                        :class="fullOtp.length === 6 ? 'bg-brand-orange hover:bg-brand-navy cursor-pointer shadow-sm hover:shadow' : 'bg-gray-300 cursor-not-allowed text-gray-500'"
                        class="w-full rounded-xl py-3 text-sm font-semibold text-white transition flex items-center justify-center gap-2"
                    >
                        <i class="fa-solid fa-circle-check text-xs"></i>
                        Verify Code
                    </button>
                </form>

                {{-- Resend OTP Section with 43s timer & 5-times lockout --}}
                <div class="mt-5 pt-4 border-t border-brand-navy/10 text-center text-xs">
                    @if($isLocked)
                        <div class="text-red-500 font-medium bg-red-50 py-2 px-3 rounded-lg border border-red-100 inline-block">
                            <i class="fa-solid fa-clock-rotate-left mr-1"></i> Resend limit reached (5/5). Try again in <span class="font-bold">{{ $lockoutMinutes }} minutes</span>.
                        </div>
                    @else
                        <template x-if="!canResend">
                            <p class="text-gray-500">
                                Didn't receive code? Resend available in <span class="font-semibold text-brand-orange" x-text="countdown + 's'"></span>
                            </p>
                        </template>
                        <template x-if="canResend">
                            <form action="{{ route('password.resend') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="email" value="{{ $email }}">
                                <button type="submit" class="font-semibold text-brand-orange hover:underline cursor-pointer inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-rotate-right text-[11px]"></i>
                                    Resend OTP Code
                                    @if(isset($resendsLeft) && $resendsLeft < 5)
                                        <span class="text-[10px] text-gray-400 font-normal">({{ $resendsLeft }} left)</span>
                                    @endif
                                </button>
                            </form>
                        </template>
                    @endif
                </div>

                <div class="mt-3.5 text-center">
                    <a href="{{ route('login') }}" class="font-medium text-brand-navy/60 hover:text-brand-orange transition inline-flex items-center gap-1.5 text-xs">
                        <i class="fa-solid fa-arrow-left text-[10px]"></i>
                        Back to Login
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-layout>
