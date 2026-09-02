<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100 font-sans antialiased selection:bg-blue-600 selection:text-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Raja POS - Retail Management System' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                        mono: ['Poppins', 'Courier New', 'monospace'],
                    },
                    colors: {
                        navy: {
                            800: '#1E293B',
                            900: '#0F172A',
                            950: '#0B132B',
                        },
                        brand: {
                            blue: '#2563EB',
                            'blue-hover': '#1D4ED8',
                            amber: '#F59E0B',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
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
<body class="h-full bg-slate-100 flex flex-col text-slate-800 overflow-hidden">

    <!-- Topbar Header Navigation -->
    <header class="bg-navy-900 text-white px-5 py-3 flex items-center justify-between shadow-sm z-30 flex-shrink-0 border-b border-slate-800">
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2">
                <span class="bg-amber-500 text-navy-950 font-extrabold px-3 py-1 rounded-full text-xs tracking-wider uppercase shadow-sm">RAJA POS</span>
                <span class="text-xs font-semibold text-slate-200 hidden sm:inline border-l border-slate-700 pl-3">
                    Retail Management System
                </span>
            </div>
        </div>

        <div class="flex items-center gap-4 text-xs">
            <a href="/pos" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl font-bold transition flex items-center gap-1.5 shadow-sm active:scale-95">
                <span>Layar Kasir POS</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </a>

            <div class="flex items-center gap-2 bg-slate-800 px-3.5 py-1.5 rounded-xl border border-slate-700">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                <span class="font-semibold text-slate-100">{{ auth()->user()->name }}</span>
                <span class="text-slate-400 font-normal">({{ auth()->user()->role?->name ?? 'Kasir' }})</span>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-slate-400 hover:text-rose-400 font-semibold transition">
                    Keluar
                </button>
            </form>
        </div>
    </header>

    <!-- Main Workspace Container -->
    <div class="flex-1 flex overflow-hidden">

        <!-- Sidebar Navigation (Strict Operational Hierarchy) -->
        <aside class="w-64 bg-white border-r border-slate-200 flex flex-col flex-shrink-0 z-20 shadow-sm">
            <div class="p-4 flex-1 overflow-y-auto space-y-6">

                <!-- 1. Dashboard -->
                <div>
                    <a href="/admin/dashboard" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition {{ request()->is('admin/dashboard') || request()->is('admin') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>
                </div>

                <!-- 2. Operasional POS -->
                <div class="space-y-1">
                    <div class="px-3.5 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                        Operasional POS
                    </div>
                    <a href="/admin/sales" class="flex items-center gap-3 px-3.5 py-2 rounded-xl font-semibold text-xs transition {{ request()->is('admin/sales') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        Riwayat Penjualan
                    </a>
                    <a href="/admin/trash" class="flex items-center gap-3 px-3.5 py-2 rounded-xl font-semibold text-xs transition {{ request()->is('admin/trash') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Sampah Transaksi
                    </a>
                </div>

                <!-- 3. Stok & Inventaris -->
                <div class="space-y-1">
                    <div class="px-3.5 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                        Stok & Inventaris
                    </div>
                    <a href="/admin/inventories" class="flex items-center gap-3 px-3.5 py-2 rounded-xl font-semibold text-xs transition {{ request()->is('admin/inventories') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Stok Fisik
                    </a>
                    <a href="/admin/stock-opname" class="flex items-center gap-3 px-3.5 py-2 rounded-xl font-semibold text-xs transition {{ request()->is('admin/stock-opname') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        Sesi Stock Opname
                    </a>
                </div>

                <!-- 4. Katalog & Produk -->
                <div class="space-y-1">
                    <div class="px-3.5 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                        Katalog & Produk
                    </div>
                    <a href="/admin/products" class="flex items-center gap-3 px-3.5 py-2 rounded-xl font-semibold text-xs transition {{ request()->is('admin/products') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        Master Produk
                    </a>
                    <a href="/admin/categories" class="flex items-center gap-3 px-3.5 py-2 rounded-xl font-semibold text-xs transition {{ request()->is('admin/categories') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Kategori Produk
                    </a>
                    <a href="/admin/brands" class="flex items-center gap-3 px-3.5 py-2 rounded-xl font-semibold text-xs transition {{ request()->is('admin/brands') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h1m-1-4h.01M9 16h.01M9 12h.01M9 8h.01M15 16h.01M15 12h.01M15 8h.01"></path>
                        </svg>
                        Brand / Merek
                    </a>
                </div>

                <!-- 5. Keuangan & Rekening -->
                <div class="space-y-1">
                    <div class="px-3.5 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                        Keuangan & Rekening
                    </div>
                    <a href="/admin/balances" class="flex items-center gap-3 px-3.5 py-2 rounded-xl font-semibold text-xs transition {{ request()->is('admin/balances') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Akun Saldo Kas & Bank
                    </a>
                </div>

                <!-- 6. Pengaturan Sistem -->
                <div class="space-y-1">
                    <div class="px-3.5 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                        Pengaturan Sistem
                    </div>
                    <a href="/admin/settings" class="flex items-center gap-3 px-3.5 py-2 rounded-xl font-semibold text-xs transition {{ request()->is('admin/settings') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Pengaturan Toko
                    </a>
                </div>

            </div>
        </aside>

        <!-- Main Content Body -->
        <main class="flex-1 overflow-y-auto p-6 bg-slate-50">
            {{ $slot }}
        </main>

    </div>

    <!-- Notification Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2 pointer-events-none"></div>

    @livewireScripts
    <script>
        window.addEventListener('notify', event => {
            const data = event.detail;
            const container = document.getElementById('toast-container');

            const toast = document.createElement('div');
            const isDanger = data.type === 'danger';
            const isWarning = data.type === 'warning';

            const bgClass = isDanger ? 'bg-rose-600 text-white' : (isWarning ? 'bg-amber-500 text-white' : 'bg-slate-900 text-white');

            toast.className = `${bgClass} px-4 py-3 rounded-xl shadow-xl text-xs font-semibold flex items-center gap-2 transform transition-all duration-200 translate-y-[-8px] opacity-0 pointer-events-auto border border-white/10 font-sans tracking-wide`;
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
