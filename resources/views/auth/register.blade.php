<x-layout title="Register - Raimart">
    <section class="mx-auto max-w-md px-4 pt-2 sm:pt-4 md:pt-6 pb-8 sm:pb-12 sm:px-6 lg:px-8">
        <div class="rounded-2xl bg-white p-6 sm:p-8 shadow-sm ring-1 ring-brand-navy/5">
            
            <!-- Card Header -->
            <div class="text-center mb-6">
                <h1 id="register-heading" class="font-serif text-2xl font-bold text-brand-navy">Create Account</h1>
                <p id="register-subheading" class="mt-1 text-sm text-brand-navy/50">Enter your email to get started</p>
            </div>

            <!-- 3-Step Refined Stepper (Sleek, lightweight, harmonious with Raimart theme) -->
            <div class="mb-6 px-1 sm:px-2">
                <div class="flex items-center justify-between relative">
                    <!-- Step 1 Node -->
                    <div class="flex flex-col items-center relative z-10 cursor-pointer" onclick="goToStep(1)">
                        <div id="step-node-1" class="w-7 h-7 rounded-full flex items-center justify-center font-medium text-xs transition-all duration-300 bg-brand-orange text-white">
                            <span id="step-icon-1">1</span>
                        </div>
                        <span id="step-label-1" class="text-[11px] font-medium text-brand-orange mt-1">Email</span>
                    </div>

                    <!-- Line 1 to 2 -->
                    <div class="flex-1 h-[2px] mx-2 bg-brand-navy/10 rounded-full overflow-hidden relative">
                        <div id="step-line-1" class="h-full bg-emerald-500 w-0 transition-all duration-300"></div>
                    </div>

                    <!-- Step 2 Node -->
                    <div class="flex flex-col items-center relative z-10">
                        <div id="step-node-2" class="w-7 h-7 rounded-full flex items-center justify-center font-normal text-xs transition-all duration-300 bg-white border border-brand-navy/15 text-brand-navy/50">
                            <span id="step-icon-2">2</span>
                        </div>
                        <span id="step-label-2" class="text-[11px] font-normal text-brand-navy/40 mt-1">Verify OTP</span>
                    </div>

                    <!-- Line 2 to 3 -->
                    <div class="flex-1 h-[2px] mx-2 bg-brand-navy/10 rounded-full overflow-hidden relative">
                        <div id="step-line-2" class="h-full bg-emerald-500 w-0 transition-all duration-300"></div>
                    </div>

                    <!-- Step 3 Node -->
                    <div class="flex flex-col items-center relative z-10">
                        <div id="step-node-3" class="w-7 h-7 rounded-full flex items-center justify-center font-normal text-xs transition-all duration-300 bg-white border border-brand-navy/15 text-brand-navy/50">
                            <span id="step-icon-3">3</span>
                        </div>
                        <span id="step-label-3" class="text-[11px] font-normal text-brand-navy/40 mt-1">Details</span>
                    </div>
                </div>
            </div>

            <!-- Dynamic Error Alert Box (Only shows on validation/server errors) -->
            <div id="step-alert" class="hidden mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600 transition-all duration-200">
                <div class="flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-exclamation text-red-500 mt-0.5 shrink-0 text-sm"></i>
                    <p id="step-alert-message" class="flex-1 text-xs sm:text-sm"></p>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- STEP 1: EMAIL ADDRESS                          -->
            <!-- ============================================== -->
            <div id="step-container-1" class="space-y-4 animate-fade-in">
                <form id="form-step-1" onsubmit="handleSendOtp(event)" class="space-y-4">
                    <div>
                        <label for="step1-email" class="block text-sm font-medium text-brand-navy">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <div class="relative mt-1">
                            <input type="email" id="step1-email" name="email" required autofocus
                                placeholder="you@example.com"
                                value="{{ session('reg_verified_email', old('email')) }}"
                                class="w-full rounded-lg border border-brand-navy/15 bg-white px-4 py-2.5 pl-10 text-sm focus:border-brand-orange focus:outline-none transition">
                            <i class="fa-regular fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-brand-navy/40 text-sm"></i>
                        </div>
                    </div>

                    <button type="submit" id="btn-send-otp" class="w-full rounded-lg bg-brand-orange py-2.5 text-sm font-semibold text-white transition hover:bg-brand-navy cursor-pointer flex items-center justify-center gap-2">
                        <span>Send OTP</span>
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
                    <span>Continue with Google</span>
                </a>

                <p class="pt-2 text-center text-sm text-brand-navy/60">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-medium text-brand-orange hover:underline">Sign In</a>
                </p>
            </div>

            <!-- ============================================== -->
            <!-- STEP 2: VERIFY OTP CODE                        -->
            <!-- ============================================== -->
            <div id="step-container-2" class="hidden space-y-4 animate-fade-in">
                <!-- Email indicator pill -->
                <div class="p-3 bg-brand-bg/50 border border-brand-orange/15 rounded-lg flex items-center justify-between text-xs sm:text-sm">
                    <div class="flex items-center gap-2 overflow-hidden">
                        <i class="fa-regular fa-envelope text-brand-orange text-sm shrink-0"></i>
                        <span class="text-gray-600 truncate">Sent to: <strong id="step2-display-email" class="text-brand-navy font-semibold"></strong></span>
                    </div>
                    <button type="button" onclick="goToStep(1)" class="shrink-0 text-brand-orange hover:text-brand-navy font-medium text-xs hover:underline cursor-pointer flex items-center gap-1">
                        <i class="fa-solid fa-pen-to-square text-[10px]"></i> Change
                    </button>
                </div>

                <form id="form-step-2" onsubmit="handleVerifyOtp(event)" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-brand-navy text-center mb-2.5">
                            Enter 6-Digit Verification Code
                        </label>
                        
                        <!-- 6-Box Segmented OTP Inputs -->
                        <div class="flex justify-center items-center gap-2 sm:gap-2.5" id="otp-inputs-wrapper">
                            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-box w-10 h-12 text-center text-xl font-bold font-mono rounded-lg border border-brand-navy/15 bg-white focus:border-brand-orange focus:outline-none transition" data-index="0" autofocus>
                            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-box w-10 h-12 text-center text-xl font-bold font-mono rounded-lg border border-brand-navy/15 bg-white focus:border-brand-orange focus:outline-none transition" data-index="1">
                            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-box w-10 h-12 text-center text-xl font-bold font-mono rounded-lg border border-brand-navy/15 bg-white focus:border-brand-orange focus:outline-none transition" data-index="2">
                            <span class="text-brand-navy/25 font-bold">-</span>
                            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-box w-10 h-12 text-center text-xl font-bold font-mono rounded-lg border border-brand-navy/15 bg-white focus:border-brand-orange focus:outline-none transition" data-index="3">
                            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-box w-10 h-12 text-center text-xl font-bold font-mono rounded-lg border border-brand-navy/15 bg-white focus:border-brand-orange focus:outline-none transition" data-index="4">
                            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-box w-10 h-12 text-center text-xl font-bold font-mono rounded-lg border border-brand-navy/15 bg-white focus:border-brand-orange focus:outline-none transition" data-index="5">
                        </div>
                    </div>

                    <button type="submit" id="btn-verify-otp" class="w-full rounded-lg bg-brand-orange py-2.5 text-sm font-semibold text-white transition hover:bg-brand-navy cursor-pointer flex items-center justify-center gap-2">
                        <span>Verify & Continue</span>
                    </button>
                </form>

                <!-- Resend Code Section -->
                <div class="text-center text-xs text-gray-500 pt-1 space-y-2">
                    <p>
                        Didn't receive the email?
                        <span id="resend-timer-box" class="text-brand-navy/70">
                            Resend code in <strong id="resend-countdown" class="text-brand-orange font-semibold">45</strong>s
                        </span>
                        <button type="button" id="btn-resend-otp" onclick="handleResendOtp()" class="hidden text-brand-orange font-semibold hover:underline cursor-pointer">
                            Resend Code
                        </button>
                    </p>
                    <div>
                        <button type="button" onclick="goToStep(1)" class="text-gray-400 hover:text-brand-navy text-xs inline-flex items-center gap-1 transition">
                            <i class="fa-solid fa-arrow-left text-[10px]"></i> Change email address
                        </button>
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- STEP 3: ACCOUNT DETAILS                        -->
            <!-- ============================================== -->
            <div id="step-container-3" class="hidden space-y-4 animate-fade-in">
                <!-- Verified Email Pill (Clean and subtle, no intrusive alert banner) -->
                <div class="p-2.5 sm:p-3 bg-emerald-50/70 border border-emerald-200/60 rounded-lg flex items-center justify-between text-xs sm:text-sm">
                    <div class="flex items-center gap-2 truncate">
                        <i class="fa-solid fa-circle-check text-emerald-500 text-sm shrink-0"></i>
                        <span class="text-emerald-900 font-medium truncate">Email Verified: <strong id="step3-display-email" class="font-semibold text-brand-navy"></strong></span>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-100 text-emerald-800 shrink-0">
                        <i class="fa-solid fa-check text-[9px]"></i> Verified
                    </span>
                </div>

                <form id="form-step-3" onsubmit="handleFinalRegister(event)" class="space-y-4" autocomplete="off">
                    @csrf
                    <!-- Explicit username field for browser credential managers so phone is never mistaken for email -->
                    <input type="email" id="final-email" name="email" value="{{ session('reg_verified_email', old('email')) }}"
                        autocomplete="username" class="sr-only" tabindex="-1" readonly aria-hidden="true">

                    <div>
                        <label for="register-name" class="block text-sm font-medium text-brand-navy">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <div class="relative mt-1">
                            <input type="text" id="register-name" name="name" required autofocus
                                autocomplete="name"
                                placeholder="e.g. Rahim Ahmed"
                                value="{{ old('name') }}"
                                class="w-full rounded-lg border border-brand-navy/15 bg-white px-4 py-2.5 pl-10 text-sm focus:border-brand-orange focus:outline-none transition">
                            <i class="fa-regular fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-brand-navy/40 text-sm"></i>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <label for="register-phone" class="block text-sm font-medium text-brand-navy">
                                Phone Number <span class="text-xs font-normal text-brand-navy/40">(optional)</span>
                            </label>
                            <span class="text-[11px] text-brand-navy/40 font-mono" id="phone-counter">0/11</span>
                        </div>
                        <div class="relative mt-1">
                            <input type="tel" id="register-phone" name="phone"
                                autocomplete="tel"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                maxlength="11"
                                placeholder="01700000000"
                                value="{{ old('phone') }}"
                                class="w-full rounded-lg border border-brand-navy/15 bg-white px-4 py-2.5 pl-10 text-sm focus:border-brand-orange focus:outline-none transition">
                            <i class="fa-solid fa-phone text-xs absolute left-3.5 top-1/2 -translate-y-1/2 text-brand-navy/40"></i>
                        </div>
                        <p id="phone-hint" class="mt-1 text-[11px] text-brand-navy/45">11-digit mobile number starting with 01 (e.g. 01700000000)</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label for="register-password" class="block text-sm font-medium text-brand-navy">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative mt-1">
                                <input type="password" id="register-password" name="password" required minlength="8"
                                    autocomplete="new-password"
                                    placeholder="Min 8 chars"
                                    class="w-full rounded-lg border border-brand-navy/15 bg-white px-4 py-2.5 pr-10 text-sm focus:border-brand-orange focus:outline-none transition">
                                <button type="button" onclick="togglePasswordVisibility('register-password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-brand-navy/40 hover:text-brand-orange cursor-pointer transition-colors" aria-label="Toggle password visibility">
                                    <i class="fa-solid fa-eye text-sm eye-open"></i>
                                    <i class="fa-solid fa-eye-slash text-sm eye-closed" style="display: none;"></i>
                                </button>
                            </div>
                            <p id="password-hint" class="mt-1 text-[11px] text-brand-navy/45">Minimum 8 characters</p>
                        </div>

                        <div>
                            <label for="register-password-confirmation" class="block text-sm font-medium text-brand-navy">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative mt-1">
                                <input type="password" id="register-password-confirmation" name="password_confirmation" required minlength="8"
                                    autocomplete="new-password"
                                    placeholder="Re-enter password"
                                    class="w-full rounded-lg border border-brand-navy/15 bg-white px-4 py-2.5 pr-10 text-sm focus:border-brand-orange focus:outline-none transition">
                                <button type="button" onclick="togglePasswordVisibility('register-password-confirmation', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-brand-navy/40 hover:text-brand-orange cursor-pointer transition-colors" aria-label="Toggle confirm password visibility">
                                    <i class="fa-solid fa-eye text-sm eye-open"></i>
                                    <i class="fa-solid fa-eye-slash text-sm eye-closed" style="display: none;"></i>
                                </button>
                            </div>
                            <p id="confirm-password-hint" class="mt-1 text-[11px] text-brand-navy/45"></p>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" id="btn-final-register" class="w-full rounded-lg bg-brand-orange py-2.5 text-sm font-semibold text-white transition hover:bg-brand-navy cursor-pointer flex items-center justify-center gap-2">
                            <span>Complete Registration</span>
                        </button>
                    </div>
                </form>

                <p class="pt-1 text-center text-xs text-brand-navy/45">
                    By registering, you agree to Raimart's <a href="{{ route('pages.show', 'terms-and-conditions') }}" class="underline hover:text-brand-orange">Terms of Service</a> & <a href="{{ route('pages.show', 'privacy-policy') }}" class="underline hover:text-brand-orange">Privacy Policy</a>.
                </p>
            </div>

        </div>
    </section>

    <style>
        .animate-fade-in {
            animation: fadeIn 0.2s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(2px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <script>
        const REGISTER_CONFIG = {
            sendOtpUrl: @json(route('register.send_otp')),
            verifyOtpUrl: @json(route('register.verify_otp')),
            registerUrl: @json(route('register')),
            csrfToken: @json(csrf_token()),
            initialVerifiedEmail: @json(session('reg_verified_email', '')),
        };

        let currentStep = 1;
        let verifiedEmail = REGISTER_CONFIG.initialVerifiedEmail || '';
        let countdownTimer = null;
        let resendSecondsRemaining = 0;

        const otpBoxes = document.querySelectorAll('.otp-box');

        // Setup OTP 6-box inputs
        otpBoxes.forEach((box, index) => {
            box.addEventListener('input', (e) => {
                const val = e.target.value.replace(/[^0-9]/g, '');
                e.target.value = val ? val.slice(-1) : '';

                if (val && index < otpBoxes.length - 1) {
                    otpBoxes[index + 1].focus();
                    otpBoxes[index + 1].select();
                }

                // If all 6 digits entered, auto submit verify
                const fullOtp = getFullOtp();
                if (fullOtp.length === 6) {
                    handleVerifyOtp();
                }
            });

            box.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace') {
                    if (!box.value && index > 0) {
                        otpBoxes[index - 1].focus();
                        otpBoxes[index - 1].select();
                    }
                } else if (e.key === 'ArrowLeft' && index > 0) {
                    otpBoxes[index - 1].focus();
                } else if (e.key === 'ArrowRight' && index < otpBoxes.length - 1) {
                    otpBoxes[index + 1].focus();
                }
            });

            box.addEventListener('paste', (e) => {
                e.preventDefault();
                const pastedData = (e.clipboardData || window.clipboardData).getData('text').trim().replace(/[^0-9]/g, '');
                if (!pastedData) return;

                const chars = pastedData.slice(0, 6).split('');
                chars.forEach((char, i) => {
                    if (otpBoxes[i]) otpBoxes[i].value = char;
                });

                if (chars.length === 6) {
                    otpBoxes[5].focus();
                    handleVerifyOtp();
                } else if (chars.length < 6 && otpBoxes[chars.length]) {
                    otpBoxes[chars.length].focus();
                }
            });
        });

        function getFullOtp() {
            let code = '';
            otpBoxes.forEach(b => code += b.value);
            return code;
        }

        function clearOtpInputs() {
            otpBoxes.forEach(b => b.value = '');
            if (otpBoxes[0]) otpBoxes[0].focus();
        }

        // Stepper Navigation: subtle, refined, lightweight
        function updateStepperUI(step) {
            currentStep = step;
            const heading = document.getElementById('register-heading');
            const subheading = document.getElementById('register-subheading');

            // Hide/Show containers
            document.getElementById('step-container-1').classList.toggle('hidden', step !== 1);
            document.getElementById('step-container-2').classList.toggle('hidden', step !== 2);
            document.getElementById('step-container-3').classList.toggle('hidden', step !== 3);

            // Hide previous alerts on step transition
            hideAlert();

            // Headings update
            if (step === 1) {
                heading.innerText = 'Create Account';
                subheading.innerText = 'Enter your email to get started';
            } else if (step === 2) {
                heading.innerText = 'Verify Your Email';
                subheading.innerText = 'Enter the 6-digit code sent to your email';
            } else if (step === 3) {
                heading.innerText = 'Account Details';
                subheading.innerText = 'Complete your profile to finish registration';
            }

            // Nodes & Lines
            const node1 = document.getElementById('step-node-1');
            const icon1 = document.getElementById('step-icon-1');
            const label1 = document.getElementById('step-label-1');
            const line1 = document.getElementById('step-line-1');

            const node2 = document.getElementById('step-node-2');
            const icon2 = document.getElementById('step-icon-2');
            const label2 = document.getElementById('step-label-2');
            const line2 = document.getElementById('step-line-2');

            const node3 = document.getElementById('step-node-3');
            const icon3 = document.getElementById('step-icon-3');
            const label3 = document.getElementById('step-label-3');

            // Reset base styles (clean, lightweight, not bold)
            [node1, node2, node3].forEach(n => {
                n.className = 'w-7 h-7 rounded-full flex items-center justify-center font-normal text-xs transition-all duration-300';
            });
            [label1, label2, label3].forEach(l => {
                l.className = 'text-[11px] font-normal text-brand-navy/40 mt-1';
            });

            if (step === 1) {
                node1.className += ' bg-brand-orange text-white font-medium';
                icon1.innerHTML = '1';
                label1.className = 'text-[11px] font-medium text-brand-orange mt-1';
                line1.style.width = '0%';

                node2.className += ' bg-white border border-brand-navy/15 text-brand-navy/50';
                icon2.innerHTML = '2';
                line2.style.width = '0%';

                node3.className += ' bg-white border border-brand-navy/15 text-brand-navy/50';
                icon3.innerHTML = '3';
            } else if (step === 2) {
                node1.className += ' bg-emerald-500 text-white';
                icon1.innerHTML = '<i class="fa-solid fa-check text-[10px]"></i>';
                label1.className = 'text-[11px] font-normal text-emerald-600 mt-1';
                line1.style.width = '100%';

                node2.className += ' bg-brand-orange text-white font-medium';
                icon2.innerHTML = '2';
                label2.className = 'text-[11px] font-medium text-brand-orange mt-1';
                line2.style.width = '0%';

                node3.className += ' bg-white border border-brand-navy/15 text-brand-navy/50';
                icon3.innerHTML = '3';
            } else if (step === 3) {
                node1.className += ' bg-emerald-500 text-white';
                icon1.innerHTML = '<i class="fa-solid fa-check text-[10px]"></i>';
                label1.className = 'text-[11px] font-normal text-emerald-600 mt-1';
                line1.style.width = '100%';

                node2.className += ' bg-emerald-500 text-white';
                icon2.innerHTML = '<i class="fa-solid fa-check text-[10px]"></i>';
                label2.className = 'text-[11px] font-normal text-emerald-600 mt-1';
                line2.style.width = '100%';

                node3.className += ' bg-brand-orange text-white font-medium';
                icon3.innerHTML = '3';
                label3.className = 'text-[11px] font-medium text-brand-orange mt-1';
            }
        }

        function goToStep(step) {
            if (step === 1) {
                updateStepperUI(1);
                setTimeout(() => document.getElementById('step1-email')?.focus(), 50);
            } else if (step === 2) {
                const email = document.getElementById('step1-email').value.trim();
                if (!email) {
                    showInlineError('Please enter your email address first.');
                    return;
                }
                updateStepperUI(2);
                setTimeout(() => otpBoxes[0]?.focus(), 50);
            } else if (step === 3) {
                if (!verifiedEmail) {
                    showInlineError('Please complete email verification first.');
                    return;
                }
                updateStepperUI(3);
                setTimeout(() => document.getElementById('register-name')?.focus(), 50);
            }
        }

        // Inline Error Display (Only used for errors, matching login page style)
        function showInlineError(message) {
            const alertBox = document.getElementById('step-alert');
            const alertMsg = document.getElementById('step-alert-message');
            alertMsg.innerHTML = message;
            alertBox.classList.remove('hidden');
        }

        function hideAlert() {
            document.getElementById('step-alert').classList.add('hidden');
        }

        // Button Loading State: Dynamic spinner insertion (Guaranteed ZERO spinner when idle)
        function setButtonLoading(buttonId, isLoading, loadingText = '', defaultText = '') {
            const btn = document.getElementById(buttonId);
            if (!btn) return;
            btn.disabled = isLoading;
            if (isLoading) {
                btn.classList.add('opacity-75', 'cursor-not-allowed');
                btn.dataset.defaultText = defaultText || btn.innerText.trim();
                btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin text-xs"></i> <span>${loadingText || 'Please wait...'}</span>`;
            } else {
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
                const text = defaultText || btn.dataset.defaultText || 'Submit';
                btn.innerHTML = `<span>${text}</span>`;
            }
        }

        // Resend Timer
        function startResendTimer(seconds = 45) {
            clearInterval(countdownTimer);
            resendSecondsRemaining = seconds;
            const timerBox = document.getElementById('resend-timer-box');
            const countdownEl = document.getElementById('resend-countdown');
            const resendBtn = document.getElementById('btn-resend-otp');

            timerBox.classList.remove('hidden');
            resendBtn.classList.add('hidden');
            countdownEl.innerText = resendSecondsRemaining;

            countdownTimer = setInterval(() => {
                resendSecondsRemaining--;
                if (resendSecondsRemaining <= 0) {
                    clearInterval(countdownTimer);
                    timerBox.classList.add('hidden');
                    resendBtn.classList.remove('hidden');
                } else {
                    countdownEl.innerText = resendSecondsRemaining;
                }
            }, 1000);
        }

        // STEP 1: Handle Send OTP
        async function handleSendOtp(event) {
            if (event) event.preventDefault();
            hideAlert();

            const emailInput = document.getElementById('step1-email');
            const email = emailInput.value.trim().toLowerCase();

            if (!email) {
                showInlineError('Please enter your email address.');
                emailInput.focus();
                return;
            }

            setButtonLoading('btn-send-otp', true, 'Sending OTP...', 'Send OTP');

            try {
                const response = await fetch(REGISTER_CONFIG.sendOtpUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': REGISTER_CONFIG.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    showInlineError(data.message || 'Failed to send OTP code. Please try again.');
                    setButtonLoading('btn-send-otp', false, '', 'Send OTP');
                    return;
                }

                // Success
                document.getElementById('step2-display-email').innerText = email;
                document.getElementById('step3-display-email').innerText = email;
                document.getElementById('final-email').value = email;

                updateStepperUI(2);
                clearOtpInputs();
                startResendTimer(data.cooldown || 45);

                // Show toast notification
                if (typeof window.showToast === 'function') {
                    window.showToast('Verification code sent to your email.', 'success');
                }

                setTimeout(() => {
                    if (otpBoxes[0]) otpBoxes[0].focus();
                }, 100);

            } catch (err) {
                showInlineError('Network or server error while sending OTP. Please check your connection.');
            } finally {
                setButtonLoading('btn-send-otp', false, '', 'Send OTP');
            }
        }

        // Resend OTP from Step 2
        async function handleResendOtp() {
            hideAlert();
            const email = document.getElementById('step1-email').value.trim();
            const resendBtn = document.getElementById('btn-resend-otp');
            resendBtn.disabled = true;
            resendBtn.innerHTML = 'Sending...';

            try {
                const response = await fetch(REGISTER_CONFIG.sendOtpUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': REGISTER_CONFIG.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    showInlineError(data.message || 'Could not resend OTP. Please try again later.');
                    resendBtn.disabled = false;
                    resendBtn.innerText = 'Resend Code';
                    return;
                }

                clearOtpInputs();
                startResendTimer(data.cooldown || 45);

                if (typeof window.showToast === 'function') {
                    window.showToast('New verification code sent to your email.', 'success');
                }
            } catch (err) {
                showInlineError('Failed to resend code due to network error.');
                resendBtn.disabled = false;
                resendBtn.innerText = 'Resend Code';
            }
        }

        // STEP 2: Handle Verify OTP
        async function handleVerifyOtp(event) {
            if (event) event.preventDefault();
            hideAlert();

            const email = document.getElementById('step1-email').value.trim().toLowerCase();
            const otp = getFullOtp();

            if (otp.length !== 6) {
                showInlineError('Please enter the complete 6-digit verification code.');
                return;
            }

            setButtonLoading('btn-verify-otp', true, 'Verifying code...', 'Verify & Continue');

            try {
                const response = await fetch(REGISTER_CONFIG.verifyOtpUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': REGISTER_CONFIG.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email, otp })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    showInlineError(data.message || 'The verification code entered is incorrect.');
                    setButtonLoading('btn-verify-otp', false, '', 'Verify & Continue');
                    return;
                }

                // Verified successfully!
                verifiedEmail = email;
                document.getElementById('step3-display-email').innerText = email;
                document.getElementById('final-email').value = email;

                // Move to Step 3 smoothly
                updateStepperUI(3);
                hideAlert(); // Keep card clean, no banner

                // Trigger Toast as requested
                if (typeof window.showToast === 'function') {
                    window.showToast('Email verified successfully!', 'success');
                }

                setTimeout(() => {
                    document.getElementById('register-name')?.focus();
                }, 100);

            } catch (err) {
                showInlineError('Could not verify code due to a connection error. Please try again.');
            } finally {
                setButtonLoading('btn-verify-otp', false, '', 'Verify & Continue');
            }
        }

        // STEP 3: Handle Final Registration
        async function handleFinalRegister(event) {
            event.preventDefault();
            hideAlert();

            const form = document.getElementById('form-step-3');
            const name = form.querySelector('[name="name"]').value.trim();
            const email = form.querySelector('[name="email"]').value.trim();
            const phone = form.querySelector('[name="phone"]').value.trim();
            const password = form.querySelector('[name="password"]').value;
            const password_confirmation = form.querySelector('[name="password_confirmation"]').value;

            if (!name) {
                showInlineError('Please enter your full name.');
                form.querySelector('[name="name"]').focus();
                return;
            }

            // Phone Number Validation: If provided, must be valid 11-digit Bangladeshi number
            if (phone) {
                const cleanPhone = phone.replace(/\D/g, '');
                if (cleanPhone.length !== 11 || !/^01[3-9]\d{8}$/.test(cleanPhone)) {
                    showInlineError('Please enter a valid 11-digit Bangladeshi phone number starting with 01 (e.g. 01700000000).');
                    form.querySelector('[name="phone"]').focus();
                    return;
                }
            }

            // Password Validation: Minimum 8 characters
            if (!password) {
                showInlineError('Please enter a password.');
                form.querySelector('[name="password"]').focus();
                return;
            }

            if (password.length < 8) {
                showInlineError('Password must be at least 8 characters long.');
                form.querySelector('[name="password"]').focus();
                return;
            }

            if (password !== password_confirmation) {
                showInlineError('Password confirmation does not match.');
                form.querySelector('[name="password_confirmation"]').focus();
                return;
            }

            setButtonLoading('btn-final-register', true, 'Creating account...', 'Complete Registration');

            try {
                const response = await fetch(REGISTER_CONFIG.registerUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': REGISTER_CONFIG.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name,
                        email,
                        phone: phone ? phone.replace(/\D/g, '') : null,
                        password,
                        password_confirmation
                    })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    showInlineError(data.message || 'Registration failed. Please check your details and try again.');
                    setButtonLoading('btn-final-register', false, '', 'Complete Registration');
                    return;
                }

                // Success! Show toast and redirect to dashboard
                if (typeof window.showToast === 'function') {
                    window.showToast('Account registered successfully! Redirecting...', 'success');
                }
                setTimeout(() => {
                    window.location.href = data.redirect || @json(route('user.dashboard'));
                }, 500);

            } catch (err) {
                showInlineError('An unexpected network error occurred while creating your account. Please try again.');
                setButtonLoading('btn-final-register', false, '', 'Complete Registration');
            }
        }

        // Live Phone Sanitizer & Formatter (Prevents browser from putting email into phone field)
        const phoneInput = document.getElementById('register-phone');
        const phoneCounter = document.getElementById('phone-counter');
        const phoneHint = document.getElementById('phone-hint');

        if (phoneInput) {
            const sanitizeAndValidatePhone = function () {
                // Strip all non-digits immediately
                let digits = this.value.replace(/\D/g, '').slice(0, 11);
                this.value = digits;

                if (phoneCounter) {
                    phoneCounter.innerText = `${digits.length}/11`;
                }

                if (digits.length === 0) {
                    phoneHint.innerText = '11-digit mobile number starting with 01 (e.g. 01700000000)';
                    phoneHint.className = 'mt-1 text-[11px] text-brand-navy/45';
                    phoneInput.classList.remove('border-red-400', 'border-emerald-500');
                } else if (digits.length === 11 && /^01[3-9]\d{8}$/.test(digits)) {
                    phoneHint.innerText = '✓ Valid Bangladeshi phone number';
                    phoneHint.className = 'mt-1 text-[11px] text-emerald-600 font-medium';
                    phoneInput.classList.remove('border-red-400');
                    phoneInput.classList.add('border-emerald-500');
                } else if (digits.length === 11 && !/^01[3-9]\d{8}$/.test(digits)) {
                    phoneHint.innerText = 'Number must start with 013-019';
                    phoneHint.className = 'mt-1 text-[11px] text-red-600 font-medium';
                    phoneInput.classList.add('border-red-400');
                    phoneInput.classList.remove('border-emerald-500');
                } else {
                    phoneHint.innerText = `${digits.length}/11 digits (e.g. 01700000000)`;
                    phoneHint.className = 'mt-1 text-[11px] text-brand-navy/45';
                    phoneInput.classList.remove('border-red-400', 'border-emerald-500');
                }
            };

            phoneInput.addEventListener('input', sanitizeAndValidatePhone);
            phoneInput.addEventListener('change', sanitizeAndValidatePhone);
            phoneInput.addEventListener('paste', () => setTimeout(() => sanitizeAndValidatePhone.call(phoneInput), 20));
        }

        // Live Password Validation & Confirmation Matching
        const passwordInput = document.getElementById('register-password');
        const passwordConfirmInput = document.getElementById('register-password-confirmation');
        const passwordHint = document.getElementById('password-hint');
        const confirmPasswordHint = document.getElementById('confirm-password-hint');

        function checkPasswordMatch() {
            if (!passwordConfirmInput || !confirmPasswordHint) return;
            const p1 = passwordInput ? passwordInput.value : '';
            const p2 = passwordConfirmInput.value;

            if (p2.length === 0) {
                confirmPasswordHint.innerText = '';
                passwordConfirmInput.classList.remove('border-red-400', 'border-emerald-500');
            } else if (p1 === p2 && p2.length >= 8) {
                confirmPasswordHint.innerText = '✓ Passwords match';
                confirmPasswordHint.className = 'mt-1 text-[11px] text-emerald-600 font-medium';
                passwordConfirmInput.classList.remove('border-red-400');
                passwordConfirmInput.classList.add('border-emerald-500');
            } else if (p1 !== p2) {
                confirmPasswordHint.innerText = 'Passwords do not match';
                confirmPasswordHint.className = 'mt-1 text-[11px] text-red-600 font-medium';
                passwordConfirmInput.classList.remove('border-emerald-500');
                passwordConfirmInput.classList.add('border-red-400');
            }
        }

        if (passwordInput) {
            passwordInput.addEventListener('input', function () {
                const len = this.value.length;
                if (len === 0) {
                    passwordHint.innerText = 'Minimum 8 characters';
                    passwordHint.className = 'mt-1 text-[11px] text-brand-navy/45';
                    passwordInput.classList.remove('border-amber-400', 'border-emerald-500', 'border-red-400');
                } else if (len < 8) {
                    passwordHint.innerText = `At least 8 characters required (${8 - len} more)`;
                    passwordHint.className = 'mt-1 text-[11px] text-amber-600 font-medium';
                    passwordInput.classList.remove('border-emerald-500', 'border-red-400');
                    passwordInput.classList.add('border-amber-400');
                } else {
                    passwordHint.innerText = '✓ Meets minimum 8 characters';
                    passwordHint.className = 'mt-1 text-[11px] text-emerald-600 font-medium';
                    passwordInput.classList.remove('border-amber-400', 'border-red-400');
                    passwordInput.classList.add('border-emerald-500');
                }
                checkPasswordMatch();
            });
        }

        if (passwordConfirmInput) {
            passwordConfirmInput.addEventListener('input', checkPasswordMatch);
        }

        // Password visibility toggle
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

        // Auto-initialize based on session state
        (() => {
            if (verifiedEmail) {
                document.getElementById('step3-display-email').innerText = verifiedEmail;
                document.getElementById('final-email').value = verifiedEmail;
                updateStepperUI(3);
            } else {
                updateStepperUI(1);
            }
        })();
    </script>
</x-layout>
