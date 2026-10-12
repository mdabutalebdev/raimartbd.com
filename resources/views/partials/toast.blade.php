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
    .animate-toast-in {
        animation: toastDropIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>

{{-- Cart toast container (Add to Cart / Removed from Cart - shifted left away from the Close button) --}}
<div id="toast-container-cart" class="fixed top-3 sm:top-3.5 right-20 sm:right-[85px] z-[999999] pointer-events-none flex flex-col items-end gap-2 max-w-xs sm:max-w-sm w-auto px-2"></div>

{{-- Favorite & General toast container (Add to Favorite / Removed from Favorite - below header on right side) --}}
<div id="toast-container-favorite" class="fixed top-[80px] md:top-[135px] right-4 sm:right-6 lg:right-8 z-[999999] pointer-events-none flex flex-col items-end gap-2.5 max-w-md w-auto px-4">
    @if ($hasToast)
        <div id="session-toast"
            class="animate-toast-in pointer-events-auto flex items-center gap-2.5 rounded-[10px] {{ $toastType === 'error' ? 'bg-[#FEF2F2] border border-[#FECACA] text-[#DC2626]' : 'bg-[#F0FDF4] border border-[#BBF7D0] text-[#15803d]' }} px-4 py-2.5 sm:px-5 sm:py-3 shadow-md shadow-black/5 transition-all duration-300 select-none cursor-pointer">
            
            <div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full {{ $toastType === 'error' ? 'bg-[#DC2626]' : 'bg-[#15803d]' }} text-white">
                @if ($toastType === 'error')
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                @else
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                @endif
            </div>

            <span class="text-sm font-medium tracking-normal whitespace-nowrap">{{ $toastMsg }}</span>
        </div>
    @endif
</div>

<script>
    function dismissToast(toastEl) {
        if (!toastEl) return;
        toastEl.style.opacity = '0';
        toastEl.style.transform = 'translateY(-14px) scale(0.96)';
        setTimeout(() => {
            if (toastEl && toastEl.parentElement) {
                toastEl.remove();
            }
        }, 220);
    }

    // Auto-dismiss session toast after 3.5 seconds
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
        }, 3500);

        toast.addEventListener('mouseenter', () => clearTimeout(dismissTimeout));
        toast.addEventListener('mouseleave', () => {
            dismissTimeout = setTimeout(() => dismissToast(toast), 1500);
        });
    }

    // Global showToast helper:
    // - Add to Cart / Cart Removed -> 'cart' container (top right in red-box area at top-3.5 right-6)
    // - Add to Favorite / Favorite Removed -> 'favorite' container (below header on right at top-[135px] right-6)
    // - Success actions -> Soft light green (#F0FDF4, border #BBF7D0, text #15803d)
    // - Removed actions -> Soft light red (#FEF2F2, border #FECACA, text #DC2626)
    window.showToast = function (message, type = 'success', position = null) {
        const isCartAction = (type === 'cart' || type === 'cart-removed');
        const targetPos = position || (isCartAction ? 'cart' : 'favorite');

        const containerId = targetPos === 'cart' ? 'toast-container-cart' : 'toast-container-favorite';
        let container = document.getElementById(containerId);
        if (!container) {
            container = document.createElement('div');
            container.id = containerId;
            container.className = targetPos === 'cart'
                ? 'fixed top-3 sm:top-3.5 right-20 sm:right-[85px] z-[999999] pointer-events-none flex flex-col items-end gap-2 max-w-xs sm:max-w-sm w-auto px-2'
                : 'fixed top-[80px] md:top-[135px] right-4 sm:right-6 lg:right-8 z-[999999] pointer-events-none flex flex-col items-end gap-2.5 max-w-md w-auto px-4';
            document.body.appendChild(container);
        }

        // Limit to max 2 simultaneous toasts in this container
        while (container.children.length >= 2) {
            dismissToast(container.firstElementChild);
        }

        const isRemoved = type === 'wishlist-removed' || type === 'cart-removed' || type === 'removed';
        const isError = type === 'error';

        let cardClasses = '';
        let iconBg = '';
        let iconSvg = '';

        if (isError) {
            cardClasses = 'bg-[#FEF2F2] border border-[#FECACA] text-[#DC2626]';
            iconBg = 'bg-[#DC2626]';
            iconSvg = `<svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>`;
        } else if (isRemoved) {
            cardClasses = 'bg-[#FEF2F2] border border-[#FECACA] text-[#DC2626]';
            iconBg = 'bg-[#DC2626]';
            iconSvg = `<svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>`;
        } else {
            cardClasses = 'bg-[#F0FDF4] border border-[#BBF7D0] text-[#15803d]';
            iconBg = 'bg-[#15803d]';
            iconSvg = `<svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>`;
        }

        const toast = document.createElement('div');
        toast.className = `animate-toast-in pointer-events-auto flex items-center gap-2.5 rounded-[10px] ${cardClasses} px-4 py-2 sm:px-4.5 sm:py-2.5 shadow-md shadow-black/5 transition-all duration-300 select-none cursor-pointer`;

        toast.innerHTML = `
            <div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full ${iconBg} text-white">
                ${iconSvg}
            </div>
            <span class="text-sm font-medium tracking-normal whitespace-nowrap">${message}</span>
        `;

        toast.addEventListener('click', () => dismissToast(toast));

        container.appendChild(toast);

        let dismissTimeout = setTimeout(() => {
            dismissToast(toast);
        }, 3000);

        toast.addEventListener('mouseenter', () => clearTimeout(dismissTimeout));
        toast.addEventListener('mouseleave', () => {
            dismissTimeout = setTimeout(() => dismissToast(toast), 1500);
        });
    };
</script>
