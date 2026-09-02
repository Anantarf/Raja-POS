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
    @livewireStyles
</head>
<body class="h-full bg-[#F3F6F4] flex overflow-hidden text-[#232E28]">

    <!-- Sidebar Navigation -->
    <aside class="w-72 bg-white border-r border-[#E3EEE8] flex flex-col flex-shrink-0 z-30">
        <!-- Sidebar Brand Header -->
        <div class="h-20 px-6 flex items-center gap-3.5 border-b border-[#E3EEE8]">
            <div class="w-10 h-10 rounded-2xl bg-[#3F7A5D] text-white font-extrabold flex items-center justify-center text-lg">
                R
            </div>
            <div>
                <div class="font-extrabold text-lg text-[#232E28] tracking-tight flex items-center gap-2">
                    <span>RAJA POS</span>
                    <span class="text-xs bg-[#D9A21B]/15 text-[#9E7511] px-2 py-0.5 rounded-md font-extrabold border border-[#D9A21B]/40">EMCO GOLD</span>
                </div>
                <div class="text-xs text-[#718379] font-bold mt-0.5">Ritel Management System</div>
            </div>
        </div>

        <!-- Sidebar Navigation Groups -->
        <div class="p-5 flex-1 overflow-y-auto space-y-6 text-sm font-semibold">

            <!-- 1. Dashboard -->
            <div>
                <a href="/admin/dashboard" class="flex items-center gap-3.5 px-4 py-3 rounded-2xl font-bold transition-all {{ request()->is('admin/dashboard') || request()->is('admin') ? 'bg-[#3F7A5D] text-white text-base' : 'text-[#52645B] hover:bg-[#F3F6F4] border border-transparent hover:border-[#E3EEE8]' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>
            </div>

            <!-- 2. Operasional POS -->
            <div class="space-y-1.5">
                <div class="px-4 text-xs font-extrabold uppercase tracking-wider text-[#718379] mb-1.5">
                    OPERASIONAL POS
                </div>
                <a href="/admin/sales" class="flex items-center gap-3.5 px-4 py-2.5 rounded-2xl transition-all {{ request()->is('admin/sales') ? 'bg-[#3F7A5D] text-white font-bold' : 'text-[#52645B] hover:bg-[#F3F6F4] border border-transparent hover:border-[#E3EEE8]' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <span>Riwayat Penjualan</span>
                </a>
                <a href="/admin/trash" class="flex items-center gap-3.5 px-4 py-2.5 rounded-2xl transition-all {{ request()->is('admin/trash') ? 'bg-[#3F7A5D] text-white font-bold' : 'text-[#52645B] hover:bg-[#F3F6F4] border border-transparent hover:border-[#E3EEE8]' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    <span>Sampah Transaksi</span>
                </a>
            </div>

            <!-- 3. Stok & Inventaris -->
            <div class="space-y-1.5">
                <div class="px-4 text-xs font-extrabold uppercase tracking-wider text-[#718379] mb-1.5">
                    STOK & INVENTARIS
                </div>
                <a href="/admin/inventories" class="flex items-center gap-3.5 px-4 py-2.5 rounded-2xl transition-all {{ request()->is('admin/inventories') ? 'bg-[#3F7A5D] text-white font-bold' : 'text-[#52645B] hover:bg-[#F3F6F4] border border-transparent hover:border-[#E3EEE8]' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span>Stok Fisik</span>
                </a>
                <a href="/admin/stock-opname" class="flex items-center gap-3.5 px-4 py-2.5 rounded-2xl transition-all {{ request()->is('admin/stock-opname') ? 'bg-[#3F7A5D] text-white font-bold' : 'text-[#52645B] hover:bg-[#F3F6F4] border border-transparent hover:border-[#E3EEE8]' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    <span>Sesi Stock Opname</span>
                </a>
            </div>

            <!-- 4. Katalog & Produk -->
            <div class="space-y-1.5">
                <div class="px-4 text-xs font-extrabold uppercase tracking-wider text-[#718379] mb-1.5">
                    KATALOG & PRODUK
                </div>
                <a href="/admin/products" class="flex items-center gap-3.5 px-4 py-2.5 rounded-2xl transition-all {{ request()->is('admin/products') ? 'bg-[#3F7A5D] text-white font-bold' : 'text-[#52645B] hover:bg-[#F3F6F4] border border-transparent hover:border-[#E3EEE8]' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <span>Master Produk</span>
                </a>
                <a href="/admin/categories" class="flex items-center gap-3.5 px-4 py-2.5 rounded-2xl transition-all {{ request()->is('admin/categories') ? 'bg-[#3F7A5D] text-white font-bold' : 'text-[#52645B] hover:bg-[#F3F6F4] border border-transparent hover:border-[#E3EEE8]' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <span>Kategori Produk</span>
                </a>
                <a href="/admin/brands" class="flex items-center gap-3.5 px-4 py-2.5 rounded-2xl transition-all {{ request()->is('admin/brands') ? 'bg-[#3F7A5D] text-white font-bold' : 'text-[#52645B] hover:bg-[#F3F6F4] border border-transparent hover:border-[#E3EEE8]' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h1m-1-4h.01M9 16h.01M9 12h.01M9 8h.01M15 16h.01M15 12h.01M15 8h.01"></path>
                    </svg>
                    <span>Brand / Merek</span>
                </a>
            </div>

            <!-- 5. Keuangan & Rekening -->
            <div class="space-y-1.5">
                <div class="px-4 text-xs font-extrabold uppercase tracking-wider text-[#718379] mb-1.5">
                    KEUANGAN & REKENING
                </div>
                <a href="/admin/balances" class="flex items-center gap-3.5 px-4 py-2.5 rounded-2xl transition-all {{ request()->is('admin/balances') ? 'bg-[#3F7A5D] text-white font-bold' : 'text-[#52645B] hover:bg-[#F3F6F4] border border-transparent hover:border-[#E3EEE8]' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Akun Saldo Kas & Bank</span>
                </a>
            </div>

            <!-- 6. Pengaturan Sistem -->
            <div class="space-y-1.5">
                <div class="px-4 text-xs font-extrabold uppercase tracking-wider text-[#718379] mb-1.5">
                    PENGATURAN SISTEM
                </div>
                <a href="/admin/settings" class="flex items-center gap-3.5 px-4 py-2.5 rounded-2xl transition-all {{ request()->is('admin/settings') ? 'bg-[#3F7A5D] text-white font-bold' : 'text-[#52645B] hover:bg-[#F3F6F4] border border-transparent hover:border-[#E3EEE8]' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    </svg>
                    <span>Pengaturan Toko</span>
                </a>
            </div>

        </div>
    </aside>

    <!-- Main Right Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Topbar Header -->
        <header class="p-5 flex-shrink-0">
            <div class="bg-white rounded-2xl border border-[#E3EEE8] px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-[#718379]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span class="text-sm text-[#718379] font-medium">Cari sesuatu (Ctrl + K)...</span>
                </div>

                <div class="flex items-center gap-4 text-sm font-semibold">
                    <a href="/pos" class="bg-[#3F7A5D] hover:bg-[#32634B] text-white px-5 py-2.5 rounded-xl transition flex items-center gap-2 text-sm font-bold active:scale-95">
                        <span>Layar Kasir POS</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
