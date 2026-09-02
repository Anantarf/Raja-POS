<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100 font-sans antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Raja POS - Retail Management System' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        navy: {
                            800: '#1E293B',
                            900: '#0F172A',
                            950: '#090D16',
                        },
                        brand: {
                            blue: '#2563EB',
                            'blue-hover': '#1D4ED8',
                            gold: '#F59E0B',
                            'gold-bright': '#FBBF24',
                        }
                    },
                    boxShadow: {
                        'glass': '0 8px 32px 0 rgba(15, 23, 42, 0.08)',
                        'glow-blue': '0 0 20px -3px rgba(37, 99, 235, 0.35)',
                        'glow-gold': '0 0 20px -3px rgba(245, 158, 11, 0.35)',
                    }
                }
            }
        }
    </script>
    <style>
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #F1F5F9;
        }
        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .glass-dark {
            background: rgba(15, 23, 42, 0.92);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
    </style>
    @livewireStyles
</head>
<body class="h-full bg-slate-100 flex flex-col text-slate-800 selection:bg-blue-500 selection:text-white overflow-hidden">

    {{ $slot }}

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

            toast.className = `${bgClass} px-4 py-3 rounded-xl shadow-2xl text-xs font-semibold flex items-center gap-2 transform transition-all duration-300 translate-y-[-10px] opacity-0 pointer-events-auto border border-white/10`;
            toast.innerHTML = `
                <span>${isDanger ? '🚫' : (isWarning ? '⚠️' : '✅')}</span>
                <span>${data.message}</span>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('translate-y-[-10px]', 'opacity-0');
            }, 10);

            setTimeout(() => {
                toast.classList.add('translate-y-[-10px]', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        });
    </script>
</body>
</html>
