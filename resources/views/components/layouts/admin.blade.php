<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F3F6F4] font-sans antialiased selection:bg-[#3F7A5D] selection:text-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Raja POS - Retail Management System' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Public Sans"', 'Poppins', '-apple-system', 'sans-serif'],
                        mono: ['Poppins', 'Courier New', 'monospace'],
                    },
                    colors: {
                        emco: {
                            primary: '#3F7A5D',       // EMCO 49/70 Deep Jade Emerald
                            'primary-hover': '#32634B',
                            'primary-light': '#E3EEE8', // EMCO 120 Light Sage
                            sand: '#C2AC7C',          // EMCO 79 Warm Sand Ochre
                            gold: '#D9A21B',          // Warm Golden Yellow Accent
                            mint: '#A9D1A0',          // EMCO 46 Fresh Mint
                            dark: '#232E28',          // Deep Forest Dark
                            body: '#F3F6F4',          // Soft Tinted Cream/Sage BG
                            muted: '#718379',
                        }
                    },
                    boxShadow: {
                        'emco': 'none',
                        'emco-hover': 'none',
                        'emco-primary': 'none',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Public Sans', 'Poppins', sans-serif;
            background-color: #F3F6F4;
            font-size: 15px;
        }
        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @livewireStyles
</head>
<body x-data="{ mobileMenuOpen: false }" class="h-full bg-[#F3F6F4] flex flex-col lg:flex-row overflow-hidden text-[#232E28]">

    <!-- Mobile/Tablet Drawer Backdrop Overlay -->
    <div
        x-show="mobileMenuOpen"
        x-transition:enter="transition-opacity ease-linear duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="mobileMenuOpen = false"
        class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 lg:hidden"
        style="display: none;"
    ></div>

    <!-- Responsive Sidebar Navigation (Desktop Fixed, Tablet/Mobile Slide-Over Drawer) -->
    <aside
        :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed lg:static inset-y-0 left-0 w-72 bg-white border-r border-[#E3EEE8] flex flex-col flex-shrink-0 z-50 transition-transform duration-300 ease-in-out h-full"
    >
        <!-- Sidebar Brand Header -->
        <div class="h-20 px-6 flex items-center justify-between border-b border-[#E3EEE8]">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-2xl bg-[#3F7A5D] text-white font-extrabold flex items-center justify-center text-lg shadow-sm">
                    R
                </div>
                <div>
                    <div class="font-extrabold text-lg text-[#232E28] tracking-tight">
                        RAJA POS
                    </div>
                    <div class="text-xs text-[#718379] font-bold mt-0.5">Retail Management System</div>
                </div>
            </div>
            <!-- Close Button for Mobile -->
            <button @click="mobileMenuOpen = false" class="lg:hidden text-slate-400 hover:text-slate-600 p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Sidebar Navigation Groups -->
        <div class="p-5 flex-1 overflow-y-auto space-y-5 text-sm font-semibold">
            @php
                $groups = [
                    'Dashboard' => [
                        ['label' => 'Dashboard', 'href' => '/admin/dashboard', 'active' => request()->is('admin') || request()->is('admin/dashboard'), 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ],
                    'Operasional POS' => [
                        ['label' => 'Riwayat Penjualan', 'href' => '/admin/sales', 'active' => request()->is('admin/sales'), 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
                        ['label' => 'Sampah Transaksi', 'href' => '/admin/trash', 'active' => request()->is('admin/trash'), 'icon' => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'],
                    ],
                    'Stok & Inventaris' => [
                        ['label' => 'Stok Fisik', 'href' => '/admin/inventories', 'active' => request()->is('admin/inventories'), 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                        ['label' => 'Pergerakan Stok', 'href' => '/admin/inventory-movements', 'active' => request()->is('admin/inventory-movements'), 'icon' => 'M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4'],
                        ['label' => 'Stock Opname', 'href' => '/admin/stock-opname', 'active' => request()->is('admin/stock-opname'), 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2m-6 9l2 2 4-4'],
                    ],
                    'Katalog & Produk' => [
                        ['label' => 'Master Produk', 'href' => '/admin/products', 'active' => request()->is('admin/products'), 'icon' => 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5'],
                        ['label' => 'Kategori Produk', 'href' => '/admin/categories', 'active' => request()->is('admin/categories'), 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'],
                        ['label' => 'Brand / Merek', 'href' => '/admin/brands', 'active' => request()->is('admin/brands'), 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5'],
                    ],
                    'Keuangan & Saldo' => [
                        ['label' => 'Akun Saldo', 'href' => '/admin/balances', 'active' => request()->is('admin/balances'), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 9v1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['label' => 'Mutasi Saldo', 'href' => '/admin/balances#mutasi-saldo', 'active' => request()->is('admin/balances'), 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4'],
                    ],
                    'Laporan' => [
                        ['label' => 'Laporan Penjualan', 'href' => '/admin/reports/sales', 'active' => request()->is('admin/reports/sales'), 'icon' => 'M3 3v18h18M7 16l3-3 3 2 4-6'],
                        ['label' => 'Laporan Inventory', 'href' => '/admin/reports/inventory', 'active' => request()->is('admin/reports/inventory'), 'icon' => 'M20 7l-8-4-8 4m16 0v10l-8 4-8-4V7'],
                        ['label' => 'Laporan Pembayaran', 'href' => '/admin/reports/payment', 'active' => request()->is('admin/reports/payment'), 'icon' => 'M3 10h18M7 15h.01M11 15h2M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                        ['label' => 'Laporan Saldo', 'href' => '/admin/reports/balance', 'active' => request()->is('admin/reports/balance'), 'icon' => 'M3 6h18M3 10h18M5 6v14h14V6'],
                        ['label' => 'Laporan Produk', 'href' => '/admin/reports/product', 'active' => request()->is('admin/reports/product'), 'icon' => 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5'],
                    ],
                    'Pengaturan Owner' => [
                        ['label' => 'User', 'href' => '/admin/settings/users', 'active' => request()->is('admin/settings/users'), 'icon' => 'M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-4a4 4 0 100-8 4 4 0 000 8z'],
                        ['label' => 'Role & Permission', 'href' => '/admin/settings/roles', 'active' => request()->is('admin/settings/roles'), 'icon' => 'M9 12l2 2 4-4m5-4v6c0 5-3.5 9-8 10-4.5-1-8-5-8-10V6l8-3 8 3z'],
                        ['label' => 'Metode Pembayaran', 'href' => '/admin/settings/payment-methods', 'active' => request()->is('admin/settings/payment-methods'), 'icon' => 'M3 10h18M7 15h.01M11 15h2M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                        ['label' => 'Lokasi Toko', 'href' => '/admin/settings/locations', 'active' => request()->is('admin/settings/locations'), 'icon' => 'M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z'],
                        ['label' => 'Pengaturan Toko', 'href' => '/admin/settings/store-settings', 'active' => request()->is('admin/settings') || request()->is('admin/settings/store-settings'), 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066 1.724 1.724 0 012.37 2.37 1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573 1.724 1.724 0 01-2.37 2.37 1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066 1.724 1.724 0 01-2.37-2.37 1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573 1.724 1.724 0 012.37-2.37 1.724 1.724 0 002.572-1.065z'],
                    ],
                ];
            @endphp

            @foreach($groups as $group => $items)
                <div class="space-y-1.5">
                    <div class="px-4 text-xs font-extrabold uppercase tracking-wider text-[#718379] mb-1.5">{{ $group }}</div>
                    @foreach($items as $item)
                        <a href="{{ $item['href'] }}" class="flex items-center gap-3.5 px-4 py-2.5 rounded-2xl transition-all {{ $item['active'] ? 'bg-[#3F7A5D] text-white font-bold' : 'text-[#52645B] hover:bg-[#F3F6F4] border border-transparent hover:border-[#E3EEE8]' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"></path></svg>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            @endforeach
        </div>
    </aside>

    <!-- Main Right Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Topbar Header -->
        <header class="p-3 sm:p-5 flex-shrink-0">
            <div class="bg-white rounded-2xl border border-[#E3EEE8] px-4 sm:px-6 py-3 sm:py-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <!-- Mobile Hamburger Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 rounded-xl bg-[#F3F6F4] border border-[#E3EEE8] text-[#232E28] hover:bg-[#E3EEE8] transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <div class="hidden sm:flex items-center gap-2 text-[#718379] font-medium text-xs sm:text-sm">
                        <svg class="w-4 h-4 text-[#718379]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span>Cari data operasional...</span>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:gap-4 text-xs sm:text-sm font-semibold">
                    <a href="/pos" class="bg-[#3F7A5D] hover:bg-[#32634B] text-white px-3 sm:px-5 py-2 sm:py-2.5 rounded-xl transition flex items-center gap-1.5 text-xs sm:text-sm font-bold active:scale-95">
                        <span>Layar Kasir</span>
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>

                    <div class="flex items-center gap-2.5 bg-[#F3F6F4] px-4 py-2 rounded-xl border border-[#E3EEE8]">
                        <div class="w-7 h-7 rounded-full bg-[#3F7A5D] text-white font-bold flex items-center justify-center text-xs">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span class="text-[#232E28] font-extrabold text-sm">{{ auth()->user()->name }}</span>
                        <span class="text-[#718379] font-normal text-xs">({{ auth()->user()->role?->name ?? 'Kasir' }})</span>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-[#718379] hover:text-rose-600 font-bold transition text-sm">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Scrollable Body -->
        <main class="flex-1 overflow-y-auto px-7 pb-7 pt-2">
            {{ $slot }}
        </main>

    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3 pointer-events-none"></div>

    @livewireScripts
    <script>
        window.addEventListener('notify', event => {
            const data = event.detail;
            const container = document.getElementById('toast-container');

            const toast = document.createElement('div');
            const isDanger = data.type === 'danger';
            const isWarning = data.type === 'warning';

            const bgClass = isDanger ? 'bg-rose-600 text-white' : (isWarning ? 'bg-amber-600 text-white' : 'bg-[#232E28] text-white');

            toast.className = `${bgClass} px-5 py-3.5 rounded-2xl text-sm font-bold flex items-center gap-2.5 transform transition-all duration-200 translate-y-[-8px] opacity-0 pointer-events-auto border border-white/10 font-sans tracking-wide`;
            toast.innerHTML = `
                <span>${isDanger ? '⚠️' : (isWarning ? '⚠️' : '✓')}</span>
                <span>${data.message}</span>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('translate-y-[-8px]', 'opacity-0');
            }, 10);

            setTimeout(() => {
                toast.classList.add('translate-y-[-8px]', 'opacity-0');
                setTimeout(() => toast.remove(), 200);
            }, 3000);
        });
    </script>
</body>
</html>


