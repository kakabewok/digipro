<div>
    @if($isMaintenance)
        {{-- MAINTENANCE MODE --}}
        <div class="flex min-h-screen flex-col items-center justify-center bg-white px-6 dark:bg-gray-950">
            @if($siteLogo)
                <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="mb-6 h-12" />
            @else
                <span class="mb-6 text-2xl font-bold text-gray-900 dark:text-white">{{ $siteName }}</span>
            @endif
            <div class="mx-auto max-w-sm text-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto mb-4 h-12 w-12 text-gray-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
                </svg>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">Sedang Dalam Pemeliharaan</h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Kami sedang melakukan pemeliharaan sistem. Silakan kembali beberapa saat lagi.</p>
            </div>
        </div>
    @else
        {{-- NAVBAR --}}
        <nav x-data="{ mobileOpen: false }" class="sticky top-0 z-50 border-b border-gray-200 bg-white/90 backdrop-blur-sm dark:border-gray-800 dark:bg-gray-950/90">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3 sm:px-6">
                {{-- Left: Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    @if($siteLogo)
                        <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="h-8" />
                    @else
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">{{ $siteName }}</span>
                    @endif
                </a>

                {{-- Center: Nav links (desktop) --}}
                <div class="hidden items-center gap-6 md:flex">
                    <a href="#" class="text-sm font-medium text-gray-700 hover:text-primary dark:text-gray-300 dark:hover:text-primary">Beranda</a>
                    <a href="#produk" class="text-sm font-medium text-gray-700 hover:text-primary dark:text-gray-300 dark:hover:text-primary">Produk</a>
                </div>

                {{-- Right: Actions --}}
                <div class="flex items-center gap-2">
                    {{-- Dark mode toggle --}}
                    <button @click="dark = !dark" class="rounded-md p-2 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800" title="Toggle dark mode">
                        <svg x-show="dark" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                        </svg>
                        <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 8.002-4.248 1 1 0 0 0-1-1.75Z" />
                        </svg>
                    </button>

                    {{-- Auth buttons (desktop) --}}
                    <div class="hidden items-center gap-2 md:flex">
                        @guest
                            <a href="{{ route('login') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Masuk</a>
                            <a href="{{ route('register') }}" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-hover">Daftar</a>
                        @else
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ auth()->user()->name }}</span>
                            <a href="{{ route('dashboard') }}" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-hover">Dashboard</a>
                        @endguest
                    </div>

                    {{-- Mobile hamburger --}}
                    <button @click="mobileOpen = !mobileOpen" class="rounded-md p-2 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 md:hidden">
                        <svg x-show="!mobileOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        <svg x-show="mobileOpen" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Mobile menu --}}
            <div x-show="mobileOpen" x-cloak x-transition.opacity class="border-t border-gray-200 bg-white px-4 pb-4 dark:border-gray-800 dark:bg-gray-950 md:hidden">
                <div class="flex flex-col gap-2 py-3">
                    <a href="#" @click="mobileOpen = false" class="rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800">Beranda</a>
                    <a href="#produk" @click="mobileOpen = false" class="rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800">Produk</a>
                    <hr class="border-gray-200 dark:border-gray-800" />
                    @guest
                        <a href="{{ route('login') }}" class="rounded-md border border-gray-300 px-3 py-2 text-center text-sm font-medium text-gray-700 dark:border-gray-700 dark:text-gray-300">Masuk</a>
                        <a href="{{ route('register') }}" class="rounded-md bg-primary px-3 py-2 text-center text-sm font-medium text-white hover:bg-primary-hover">Daftar</a>
                    @else
                        <a href="{{ route('dashboard') }}" class="rounded-md bg-primary px-3 py-2 text-center text-sm font-medium text-white hover:bg-primary-hover">Dashboard</a>
                    @endguest
                </div>
            </div>
        </nav>

        {{-- HERO SECTION --}}
        <section class="bg-white px-4 py-16 dark:bg-gray-950 md:py-24">
            <div class="mx-auto max-w-3xl text-center">
                <span class="text-xs font-semibold uppercase tracking-widest text-primary">Platform Produk Digital</span>
                <h1 class="mt-4 text-4xl font-bold leading-tight tracking-tight text-gray-900 dark:text-white md:text-5xl">
                    Produk Digital Terbaik, Tersedia Instan
                </h1>
                <p class="mx-auto mt-4 max-w-xl text-lg text-gray-500 dark:text-gray-400">
                    Selamat datang di {{ $siteName }}. Beli produk digital premium dengan harga terjangkau, proses instan dan otomatis.
                </p>
                <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <a href="#produk" class="rounded-md bg-primary px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-hover">Lihat Produk</a>
                    @guest
                        <a href="{{ route('login') }}" class="rounded-md border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Masuk</a>
                    @else
                        <a href="{{ route('dashboard') }}" class="rounded-md border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Dashboard</a>
                    @endguest
                </div>
            </div>
        </section>

        {{-- STATS BAR --}}
        <section class="border-y border-gray-200 bg-gray-50 px-4 py-8 dark:border-gray-800 dark:bg-gray-900">
            <div class="mx-auto grid max-w-4xl grid-cols-3 divide-x divide-gray-200 dark:divide-gray-800">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalProducts }}</div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Produk Aktif</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalOrders }}</div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Transaksi Selesai</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalMembers }}</div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Member Terdaftar</div>
                </div>
            </div>
        </section>

        {{-- PRODUCT SECTION --}}
        <section id="produk" class="bg-white px-4 py-12 dark:bg-gray-950 md:py-16">
            <div class="mx-auto max-w-6xl">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Katalog Produk</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Temukan produk digital yang kamu butuhkan</p>
                </div>

                {{-- Filter bar --}}
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    {{-- Search --}}
                    <div class="relative w-full sm:max-w-xs">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        <input
                            wire:model.live.debounce.300ms="search"
                            type="text"
                            placeholder="Cari produk..."
                            class="w-full rounded-md border border-gray-300 bg-white py-2 pl-9 pr-3 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:placeholder-gray-500"
                        />
                    </div>

                    {{-- Category tabs --}}
                    <div class="flex flex-wrap gap-2">
                        <button
                            wire:click="filterCategory(null)"
                            class="rounded-md px-3 py-1.5 text-xs font-medium transition-colors {{ $categoryFilter === null ? 'bg-primary text-white' : 'border border-gray-300 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800' }}"
                        >Semua</button>
                        @foreach($categories as $cat)
                            <button
                                wire:click="filterCategory({{ $cat->id }})"
                                class="rounded-md px-3 py-1.5 text-xs font-medium transition-colors {{ $categoryFilter === $cat->id ? 'bg-primary text-white' : 'border border-gray-300 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800' }}"
                            >{{ $cat->name }}</button>
                        @endforeach
                    </div>
                </div>

                {{-- Loading skeleton --}}
                <div wire:loading.flex class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
                    @foreach(range(1, 8) as $i)
                        <div class="rounded-md bg-gray-100 dark:bg-gray-800 animate-pulse h-64"></div>
                    @endforeach
                </div>

                {{-- Product grid --}}
                <div wire:loading.remove>
                    @if($products->isEmpty())
                        <div class="flex flex-col items-center justify-center py-16 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mb-4 h-12 w-12 text-gray-300 dark:text-gray-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada produk ditemukan</p>
                        </div>
                    @else
                        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
                            @foreach($products as $product)
                                <div class="overflow-hidden rounded-md border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                                    {{-- Thumbnail --}}
                                    <div class="relative aspect-video bg-gray-100 dark:bg-gray-800">
                                        @if($product->thumbnail)
                                            <img src="{{ Storage::url($product->thumbnail) }}" alt="{{ $product->name }}" class="h-full w-full object-cover" />
                                        @else
                                            <div class="flex h-full w-full items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 text-gray-400 dark:text-gray-500">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                                </svg>
                                            </div>
                                        @endif
                                        {{-- Category badge --}}
                                        @if($product->category)
                                            <span class="absolute left-2 top-2 rounded-md bg-primary/10 px-2 py-0.5 text-xs font-medium text-primary dark:bg-primary/20">
                                                {{ $product->category->name }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Card body --}}
                                    <div class="p-4">
                                        <h3 class="line-clamp-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $product->name }}</h3>
                                        <p class="mt-1 text-base font-bold text-primary">Rp {{ number_format($product->price_customer, 0, ',', '.') }}</p>
                                        <p class="mt-1 {{ $product->available_stock_count > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-500' }} text-xs">
                                            {{ $product->available_stock_count > 0 ? 'Tersedia' : 'Habis' }}
                                        </p>
                                        @if($product->available_stock_count > 0)
                                            <button
                                                wire:click="beli({{ $product->id }})"
                                                class="mt-3 w-full rounded-md bg-primary py-2 text-sm font-medium text-white hover:bg-primary-hover"
                                            >Beli Sekarang</button>
                                        @else
                                            <button disabled class="mt-3 w-full cursor-not-allowed rounded-md bg-gray-100 py-2 text-sm font-medium text-gray-400 dark:bg-gray-800">Beli Sekarang</button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-8">
                            {{ $products->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- HOW IT WORKS --}}
        <section class="border-t border-gray-200 bg-white px-4 py-12 dark:border-gray-800 dark:bg-gray-950 md:py-16">
            <div class="mx-auto max-w-4xl">
                <div class="mb-8 text-center">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Cara Kerja</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tiga langkah mudah untuk mendapatkan produk digital</p>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    {{-- Step 1 --}}
                    <div class="rounded-md border border-gray-200 bg-gray-50 p-6 text-center dark:border-gray-800 dark:bg-gray-900">
                        <div class="mx-auto mb-4 flex h-10 w-10 items-center justify-center rounded-md bg-primary/10 dark:bg-primary/20">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-primary">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                            </svg>
                        </div>
                        <h3 class="mb-1 text-sm font-semibold text-gray-900 dark:text-white">Daftar Akun</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Buat akun gratis dalam hitungan detik</p>
                    </div>
                    {{-- Step 2 --}}
                    <div class="rounded-md border border-gray-200 bg-gray-50 p-6 text-center dark:border-gray-800 dark:bg-gray-900">
                        <div class="mx-auto mb-4 flex h-10 w-10 items-center justify-center rounded-md bg-primary/10 dark:bg-primary/20">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-primary">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                            </svg>
                        </div>
                        <h3 class="mb-1 text-sm font-semibold text-gray-900 dark:text-white">Isi Saldo</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Deposit saldo via QRIS dengan mudah</p>
                    </div>
                    {{-- Step 3 --}}
                    <div class="rounded-md border border-gray-200 bg-gray-50 p-6 text-center dark:border-gray-800 dark:bg-gray-900">
                        <div class="mx-auto mb-4 flex h-10 w-10 items-center justify-center rounded-md bg-primary/10 dark:bg-primary/20">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-primary">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                            </svg>
                        </div>
                        <h3 class="mb-1 text-sm font-semibold text-gray-900 dark:text-white">Beli & Terima Instan</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Produk dikirim otomatis setelah pembayaran</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- FOOTER --}}
        <footer class="border-t border-gray-200 bg-white px-4 py-6 dark:border-gray-800 dark:bg-gray-950">
            <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-2 sm:flex-row">
                <span class="text-xs text-gray-400 dark:text-gray-600">{{ $siteName }} &copy; {{ date('Y') }}</span>
                <div class="flex items-center gap-3">
                    <a href="#" class="text-xs text-gray-400 hover:text-gray-600 dark:text-gray-600 dark:hover:text-gray-400">Beranda</a>
                    <span class="text-xs text-gray-300 dark:text-gray-700">&middot;</span>
                    <a href="#produk" class="text-xs text-gray-400 hover:text-gray-600 dark:text-gray-600 dark:hover:text-gray-400">Produk</a>
                    <span class="text-xs text-gray-300 dark:text-gray-700">&middot;</span>
                    @guest
                        <a href="{{ route('login') }}" class="text-xs text-gray-400 hover:text-gray-600 dark:text-gray-600 dark:hover:text-gray-400">Masuk</a>
                    @else
                        <a href="{{ route('dashboard') }}" class="text-xs text-gray-400 hover:text-gray-600 dark:text-gray-600 dark:hover:text-gray-400">Dashboard</a>
                    @endguest
                </div>
            </div>
        </footer>
    @endif
</div>
