<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F3F6F4] font-sans antialiased selection:bg-[#3F7A5D] selection:text-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Raja Aksesoris - Retail Management System' }}</title>
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
        html {
            font-size: 110%;
        }
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
                        RAJA AKSESORIS
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
        <div class="flex-1 overflow-y-auto px-4 py-5 space-y-6">
            <!-- Operasional Kasir Group -->
            <div>
                <div class="px-3 text-[11px] font-extrabold text-[#718379] uppercase tracking-wider mb-2">Operasional Kasir</div>
                <nav class="space-y-1">
                    <a href="/admin/dashboard" class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold transition {{ request()->is('admin', 'admin/dashboard') ? 'bg-[#3F7A5D] text-white shadow-emco-primary' : 'text-[#52645B] hover:bg-[#F3F6F4] hover:text-[#232E28]' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="/pos" class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 transition border border-emerald-200/80">
                        <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 002 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <span>Kasir</span>
                    </a>
                </nav>
            </div>

            <!-- Katalog & Inventaris Group -->
            <div>
                <div class="px-3 text-[11px] font-extrabold text-[#718379] uppercase tracking-wider mb-2">Katalog &amp; Inventaris</div>
                <nav class="space-y-1">
                    <a href="/admin/products" class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold transition {{ request()->is('admin/products*') ? 'bg-[#3F7A5D] text-white shadow-emco-primary' : 'text-[#52645B] hover:bg-[#F3F6F4] hover:text-[#232E28]' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span>Master Produk</span>
                    </a>
                    <a href="/admin/inventories" class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold transition {{ request()->is('admin/inventories*') || request()->is('admin/stock-opname*') ? 'bg-[#3F7A5D] text-white shadow-emco-primary' : 'text-[#52645B] hover:bg-[#F3F6F4] hover:text-[#232E28]' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span>Stok & Opname</span>
                    </a>
                    <a href="/admin/inventory-movements" class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold transition {{ request()->is('admin/inventory-movements*') ? 'bg-[#3F7A5D] text-white shadow-emco-primary' : 'text-[#52645B] hover:bg-[#F3F6F4] hover:text-[#232E28]' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                        <span>Mutasi Stok</span>
                    </a>
                </nav>
            </div>

            <!-- Keuangan & Saldo Group -->
            <div>
                <div class="px-3 text-[11px] font-extrabold text-[#718379] uppercase tracking-wider mb-2">Keuangan &amp; Saldo</div>
                <nav class="space-y-1">
                    <a href="/admin/sales" class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold transition {{ request()->is('admin/sales*') ? 'bg-[#3F7A5D] text-white shadow-emco-primary' : 'text-[#52645B] hover:bg-[#F3F6F4] hover:text-[#232E28]' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>Riwayat Transaksi</span>
                    </a>
                    <a href="/admin/balances" class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold transition {{ request()->is('admin/balances*') ? 'bg-[#3F7A5D] text-white shadow-emco-primary' : 'text-[#52645B] hover:bg-[#F3F6F4] hover:text-[#232E28]' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Kas & Saldo Provider</span>
                    </a>
                    <a href="/admin/trash" class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 transition border border-rose-200/60">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <span>Sampah Transaksi</span>
                    </a>
                </nav>
            </div>

            <!-- Laporan Group -->
            <div>
                <div class="px-3 text-[11px] font-extrabold text-[#718379] uppercase tracking-wider mb-2">Laporan</div>
                <nav class="space-y-1">
                    <a href="/admin/reports" class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold transition {{ request()->is('admin/reports*') ? 'bg-[#3F7A5D] text-white shadow-emco-primary' : 'text-[#52645B] hover:bg-[#F3F6F4] hover:text-[#232E28]' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>Laporan Laba & Margin</span>
                    </a>
                </nav>
            </div>

            <!-- Pengaturan Owner Group -->
            <div>
                <div class="px-3 text-[11px] font-extrabold text-[#718379] uppercase tracking-wider mb-2">Pengaturan Owner</div>
                <nav class="space-y-1">
                    <a href="/admin/settings" class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold transition {{ request()->is('admin/settings*') ? 'bg-[#3F7A5D] text-white shadow-emco-primary' : 'text-[#52645B] hover:bg-[#F3F6F4] hover:text-[#232E28]' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Pengaturan Toko</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- User Profile Footer -->
        <div class="p-4 border-t border-[#E3EEE8] bg-[#F3F6F4]/50">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-2xl bg-[#E3EEE8] text-[#3F7A5D] font-mono font-extrabold text-sm flex items-center justify-center border border-[#3F7A5D]/20">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <div class="font-extrabold text-xs text-[#232E28] truncate">{{ auth()->user()->name ?? 'User' }}</div>
                        <div class="text-[10px] text-[#718379] font-bold uppercase tracking-wider">{{ auth()->user()->role->name ?? 'OWNER' }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Application Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden">
        <!-- Top Navbar -->
        <header class="h-20 bg-white border-b border-[#E3EEE8] px-4 sm:px-8 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-4">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-slate-600 hover:text-slate-900 rounded-xl bg-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <div class="hidden sm:block">
                    <h2 class="font-extrabold text-base text-[#232E28] tracking-tight">{{ $title ?? 'Admin Portal' }}</h2>
                    <p class="text-xs text-[#718379] font-medium">RAJA AKSESORIS BANGO</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="/pos" class="px-3.5 py-2 bg-[#3F7A5D] hover:bg-[#32634B] text-white font-bold rounded-xl text-xs shadow-sm transition flex items-center gap-1.5 shrink-0 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 002 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <span>Buka Kasir</span>
                </a>
            </div>
        </header>

        <!-- Main Page Content Body -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-8">
            {{ $slot }}
        </main>
    </div>

    <!-- Livewire Scripts -->
    @livewireScripts
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('notify', (event) => {
                const message = event.message || event[0]?.message || 'Berhasil';
                const type = event.type || event[0]?.type || 'success';

                let container = document.getElementById('toast-container');
                if (!container) {
                    container = document.createElement('div');
                    container.id = 'toast-container';
                    container.className = 'fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none';
                    document.body.appendChild(container);
                }

                const toast = document.createElement('div');
                const bgColor = type === 'success' ? 'bg-[#3F7A5D]' : (type === 'danger' ? 'bg-rose-600' : 'bg-amber-600');
                toast.className = `${bgColor} text-white px-4 py-3 rounded-2xl shadow-xl text-xs font-extrabold transition-all duration-300 transform translate-y-2 opacity-0 pointer-events-auto flex items-center gap-2`;
                toast.innerHTML = `<span>${message}</span>`;

                container.appendChild(toast);

                setTimeout(() => {
                    toast.classList.remove('translate-y-2', 'opacity-0');
                }, 10);

                setTimeout(() => {
                    toast.classList.add('translate-y-2', 'opacity-0');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            });
        });
    </script>
</body>
</html>
