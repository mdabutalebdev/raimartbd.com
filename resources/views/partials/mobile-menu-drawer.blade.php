<div id="mobile-menu-drawer" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-brand-navy/50" data-drawer-close></div>

    <div class="absolute inset-y-0 left-0 flex w-80 max-w-[85vw] flex-col bg-white shadow-xl">
        <div class="flex items-center justify-between border-b border-brand-navy/10 px-5 py-4">
            <h2 class="flex items-center gap-2 font-serif text-lg font-bold text-brand-navy">
                <i class="fa-solid fa-bars text-brand-orange"></i>
                Menu
            </h2>
            <button type="button" data-drawer-close aria-label="Close" class="flex h-8 w-8 items-center justify-center rounded-full text-brand-navy/50 hover:bg-brand-bg hover:text-brand-navy">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto">
            <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-3 border-b border-brand-navy/5 px-5 py-4 hover:bg-brand-bg">
                <i class="fa-solid fa-house w-6 text-center text-brand-navy/40"></i>
                <div class="font-medium text-brand-navy">Home</div>
                <i class="fa-solid fa-chevron-right ml-auto shrink-0 text-brand-navy/30 text-xs"></i>
            </a>
            <a href="{{ route('shop') }}" wire:navigate class="flex items-center gap-3 border-b border-brand-navy/5 px-5 py-4 hover:bg-brand-bg">
                <i class="fa-solid fa-bag-shopping w-6 text-center text-brand-navy/40"></i>
                <div class="font-medium text-brand-navy">Shop</div>
                <i class="fa-solid fa-chevron-right ml-auto shrink-0 text-brand-navy/30 text-xs"></i>
            </a>
            <a href="{{ route('offers') }}" wire:navigate class="flex items-center gap-3 border-b border-brand-navy/5 px-5 py-4 hover:bg-brand-bg">
                <i class="fa-solid fa-tags w-6 text-center text-brand-orange"></i>
                <div class="font-medium text-brand-orange">Offers</div>
                <i class="fa-solid fa-chevron-right ml-auto shrink-0 text-brand-navy/30 text-xs"></i>
            </a>
            <a href="{{ route('track') }}" wire:navigate class="flex items-center gap-3 border-b border-brand-navy/5 px-5 py-4 hover:bg-brand-bg">
                <i class="fa-solid fa-truck-fast w-6 text-center text-brand-navy/40"></i>
                <div class="font-medium text-brand-navy">Track Order</div>
                <i class="fa-solid fa-chevron-right ml-auto shrink-0 text-brand-navy/30 text-xs"></i>
            </a>
            <a href="{{ route('categories.index') }}" wire:navigate class="flex items-center gap-3 border-b border-brand-navy/5 px-5 py-4 hover:bg-brand-bg">
                <i class="fa-solid fa-list w-6 text-center text-brand-navy/40"></i>
                <div class="font-medium text-brand-navy">Categories</div>
                <i class="fa-solid fa-chevron-right ml-auto shrink-0 text-brand-navy/30 text-xs"></i>
            </a>
            <a href="{{ route('about') }}" wire:navigate class="flex items-center gap-3 border-b border-brand-navy/5 px-5 py-4 hover:bg-brand-bg">
                <i class="fa-solid fa-circle-info w-6 text-center text-brand-navy/40"></i>
                <div class="font-medium text-brand-navy">About Us</div>
                <i class="fa-solid fa-chevron-right ml-auto shrink-0 text-brand-navy/30 text-xs"></i>
            </a>
            <a href="{{ route('contact') }}" wire:navigate class="flex items-center gap-3 border-b border-brand-navy/5 px-5 py-4 hover:bg-brand-bg">
                <i class="fa-solid fa-headset w-6 text-center text-brand-navy/40"></i>
                <div class="font-medium text-brand-navy">Contact Us</div>
                <i class="fa-solid fa-chevron-right ml-auto shrink-0 text-brand-navy/30 text-xs"></i>
            </a>
        </div>
        
        @auth
        <div class="border-t border-brand-navy/10 p-4">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 rounded-md bg-red-50 text-red-600 py-2.5 text-center text-sm font-semibold hover:bg-red-100 transition">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    Logout
                </button>
            </form>
        </div>
        @endauth
    </div>
</div>
