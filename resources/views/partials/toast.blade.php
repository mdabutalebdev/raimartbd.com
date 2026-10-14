@php
    $hasToast = session()->has('success') || session()->has('status') || session()->has('error');
    $toastType = session()->has('error') ? 'error' : 'success';
    $toastMsg = session('success') ?? session('status') ?? session('error');
@endphp

<style>
    @keyframes toastDropIn {
        0% {
            opacity: 0;
            transform: translateY(-20px) scale(0.96);
        }
        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    @keyframes toastSlideInRight {
        0% {
            opacity: 0;
            transform: translateX(24px) scale(0.96);
        }
        100% {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
    }
    @keyframes toastFadeOutUp {
        0% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        100% {
            opacity: 0;
            transform: translateY(-16px) scale(0.95);
        }
    }
    @keyframes toastFadeOutRight {
        0% {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
        100% {
            opacity: 0;
            transform: translateX(24px) scale(0.95);
        }
    }
    @keyframes toastShake {
        0%, 100% { transform: translateY(0) scale(1); }
        20%, 60% { transform: translateY(0) scale(1) translateX(-6px); }
        40%, 80% { transform: translateY(0) scale(1) translateX(6px); }
    }
    .animate-toast-in {
        animation: toastDropIn 0.26s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    .animate-toast-in-right {
        animation: toastSlideInRight 0.26s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    .animate-toast-out {
        animation: toastFadeOutUp 0.25s cubic-bezier(0.4, 0, 0.2, 1) forwards !important;
    }
    .animate-toast-out-right {
        animation: toastFadeOutRight 0.25s cubic-bezier(0.4, 0, 0.2, 1) forwards !important;
    }
    .animate-toast-shake {
        animation: toastShake 0.32s ease-in-out;
    }

    #toast-container-center {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 99999999;
        pointer-events: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        width: max-content;
        max-width: 92vw;
    }

    /* Exactly matching reference Image 2: Soft pinkish-white background, subtle rose border, dark red circle with white ! and crisp dark red text */
    .stock-toast-card {
        background: #FFF1F2;
        border: 1px solid #FECDD3;
        color: #991B1B;
        border-radius: 10px;
        padding: 8px 18px;
        box-shadow: 0 4px 14px -2px rgba(153, 27, 27, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        display: flex;
        align-items: center;
        gap: 10px;
        pointer-events: auto;
        cursor: pointer;
        user-select: none;
        transition: box-shadow 0.2s ease;
    }

    .stock-toast-icon {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background-color: #991B1B;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
</style>

{{-- Center / Top toast container (Stock errors & System alerts - exactly matching reference Image 2) --}}
<div id="toast-container-center">
    @if ($hasToast && $toastType === 'error')
        <div id="session-toast" class="stock-toast-card animate-toast-in">
            <div class="stock-toast-icon">
                <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M12 8v4m0 4h.01"></path>
                </svg>
            </div>
            <span class="text-xs sm:text-sm font-semibold tracking-normal text-[#991B1B] whitespace-nowrap">{{ $toastMsg }}</span>
        </div>
    @endif
</div>

{{-- Cart toast container (Add to Cart / Removed from Cart - shifted left away from the Close button) --}}
<div id="toast-container-cart" class="fixed top-3 sm:top-3.5 right-20 sm:right-[85px] z-[999999] pointer-events-none flex flex-col items-end gap-2 max-w-xs sm:max-w-sm w-auto px-2"></div>

{{-- Favorite & General toast container (Add to Favorite / Removed from Favorite - below header on right side) --}}
<div id="toast-container-favorite" class="fixed top-[80px] md:top-[135px] right-4 sm:right-6 lg:right-8 z-[999999] pointer-events-none flex flex-col items-end gap-2.5 max-w-md w-auto px-4">
    @if ($hasToast && $toastType !== 'error')
        <div id="session-toast"
            class="animate-toast-in-right pointer-events-auto flex items-center gap-2.5 rounded-[10px] bg-[#F0FDF4] border border-[#BBF7D0] text-[#15803d] px-4 py-2.5 sm:px-5 sm:py-3 shadow-md shadow-black/5 transition-all duration-300 select-none cursor-pointer">
            <div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#15803d] text-white">
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <span class="text-sm font-medium tracking-normal whitespace-nowrap">{{ $toastMsg }}</span>
        </div>
    @endif
</div>

<script>
    function dismissToast(toastEl) {
        if (!toastEl || toastEl._isDismissing) return;
        toastEl._isDismissing = true;
        if (toastEl._dismissTimeout) {
            clearTimeout(toastEl._dismissTimeout);
        }

        const isCenter = toastEl.closest('#toast-container-center');
        toastEl.classList.remove('animate-toast-in', 'animate-toast-in-right', 'animate-toast-shake');
        toastEl.classList.add(isCenter ? 'animate-toast-out' : 'animate-toast-out-right');

        setTimeout(() => {
            if (toastEl && toastEl.parentElement) {
                toastEl.remove();
            }
        }, 250);
    }

    // Auto-dismiss session toast after 1.2 seconds
    document.addEventListener('DOMContentLoaded', () => {
        initSessionToast();
    });
    window.addEventListener('livewire:navigated', () => {
        initSessionToast();
    });

    function initSessionToast() {
        const toast = document.getElementById('session-toast');
        if (!toast || toast.dataset.initialized) return;
        toast.dataset.initialized = 'true';

        toast.addEventListener('click', () => dismissToast(toast));

        let dismissTimeout = setTimeout(() => {
            dismissToast(toast);
        }, 1200);

        toast.addEventListener('mouseenter', () => clearTimeout(dismissTimeout));
        toast.addEventListener('mouseleave', () => {
            dismissTimeout = setTimeout(() => dismissToast(toast), 600);
        });
    }

    // Global showToast helper:
    // - Stock Error -> 'center' container (top center with clean red border and red circle exclamation icon, matching Image 2)
    // - Add to Cart / Cart Removed -> 'cart' container (top right in drawer header at top-3.5 right-6)
    // - Add to Favorite / Favorite Removed -> 'favorite' container (below header on right at top-[135px] right-6)
    window.showToast = function (message, type = 'success', position = null) {
        const isCartAction = (type === 'cart' || type === 'cart-removed');
        const isStockError = (type === 'stock-error' || (type === 'error' && (!position || position === 'center')));

        let targetPos = position;
        if (!targetPos) {
            if (isStockError) targetPos = 'center';
            else if (isCartAction) targetPos = 'cart';
            else targetPos = 'favorite';
        }

        const containerMap = {
            center: 'toast-container-center',
            cart: 'toast-container-cart',
            favorite: 'toast-container-favorite'
        };

        const containerId = containerMap[targetPos] || 'toast-container-center';
        let container = document.getElementById(containerId);
        if (!container) {
            container = document.createElement('div');
            container.id = containerId;
            if (targetPos === 'center') {
                container.style.cssText = 'position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:99999999;pointer-events:none;display:flex;flex-direction:column;align-items:center;gap:10px;width:max-content;max-width:92vw;';
            } else if (targetPos === 'cart') {
                container.className = 'fixed top-3 sm:top-3.5 right-20 sm:right-[85px] z-[999999] pointer-events-none flex flex-col items-end gap-2 max-w-xs sm:max-w-sm w-auto px-2';
            } else {
                container.className = 'fixed top-[80px] md:top-[135px] right-4 sm:right-6 lg:right-8 z-[999999] pointer-events-none flex flex-col items-end gap-2.5 max-w-md w-auto px-4';
            }
            document.body.appendChild(container);
        }

        // Center container (Stock errors): Single alert at a time.
        // If exact message is already displaying in center, shake it and reset dismiss timer!
        if (targetPos === 'center' && container.firstElementChild) {
            const existingToast = container.firstElementChild;
            const existingMsg = existingToast.querySelector('span')?.textContent?.trim();
            if (existingMsg === message.trim()) {
                existingToast.classList.remove('animate-toast-shake', 'animate-toast-out');
                void existingToast.offsetWidth;
                existingToast.classList.add('animate-toast-shake');
                if (existingToast._dismissTimeout) {
                    clearTimeout(existingToast._dismissTimeout);
                }
                existingToast._dismissTimeout = setTimeout(() => dismissToast(existingToast), 1100);
                return;
            }
            // If different message in center, clear old one synchronously so they don't stack
            while (container.firstChild) {
                container.removeChild(container.firstChild);
            }
        } else {
            // For other containers (cart, favorite), limit to max 2 simultaneous toasts
            while (container.children.length >= 2) {
                const oldest = container.firstElementChild;
                if (!oldest) break;
                oldest.remove();
            }
        }

        const isRemoved = type === 'wishlist-removed' || type === 'cart-removed' || type === 'removed';
        const isError = type === 'error' || type === 'stock-error';

        const toast = document.createElement('div');

        if (type === 'stock-error' || (isError && targetPos === 'center')) {
            // Exactly matching Image 2: Soft pink background, red border, dark red alert circle with exclamation, crisp red text
            toast.className = 'stock-toast-card animate-toast-in';
            toast.innerHTML = `
                <div class="stock-toast-icon">
                    <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M12 8v4m0 4h.01"></path>
                    </svg>
                </div>
                <span class="text-xs sm:text-sm font-semibold tracking-normal text-[#991B1B] whitespace-nowrap">${message}</span>
            `;
        } else {
            let cardClasses = '';
            let iconBg = '';
            let iconSvg = '';

            if (isError) {
                cardClasses = 'bg-[#FFF1F2] border border-[#FECDD3] text-[#991B1B] shadow-md shadow-black/5';
                iconBg = 'bg-[#991B1B]';
                iconSvg = `<svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>`;
            } else if (isRemoved) {
                cardClasses = 'bg-[#FEF2F2] border border-[#FECACA] text-[#DC2626] shadow-md shadow-black/5';
                iconBg = 'bg-[#DC2626]';
                iconSvg = `<svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>`;
            } else {
                cardClasses = 'bg-[#F0FDF4] border border-[#BBF7D0] text-[#15803d] shadow-md shadow-black/5';
                iconBg = 'bg-[#15803d]';
                iconSvg = `<svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>`;
            }

            toast.className = `animate-toast-in-right pointer-events-auto flex items-center gap-2.5 rounded-[10px] ${cardClasses} px-4 py-2 sm:px-5 sm:py-2.5 select-none cursor-pointer`;
            toast.innerHTML = `
                <div class="flex h-4.5 w-4.5 shrink-0 items-center justify-center rounded-full ${iconBg} text-white">
                    ${iconSvg}
                </div>
                <span class="text-xs sm:text-sm font-medium tracking-normal whitespace-nowrap">${message}</span>
            `;
        }

        toast.addEventListener('click', () => dismissToast(toast));

        container.appendChild(toast);

        // Stays for 1 second (1100ms) as requested, then dismisses smoothly
        toast._dismissTimeout = setTimeout(() => {
            dismissToast(toast);
        }, 1100);

        toast.addEventListener('mouseenter', () => clearTimeout(toast._dismissTimeout));
        toast.addEventListener('mouseleave', () => {
            toast._dismissTimeout = setTimeout(() => dismissToast(toast), 600);
        });
    };
</script>
