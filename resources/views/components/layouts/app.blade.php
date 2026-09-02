<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100 font-sans antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Raja POS - Retail Management System' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                        mono: ['Courier New', 'Courier', 'monospace'],
                    },
                    colors: {
                        navy: {
                            900: '#0F172A',
                        },
                        brand: {
                            blue: '#2563EB',
                            'blue-dark': '#1D4ED8',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        ::-webkit-scrollbar-track {
            background: #F1F5F9;
        }
        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }
    </style>
    @livewireStyles
</head>
<body class="h-full bg-slate-100 flex flex-col text-slate-800 selection:bg-blue-600 selection:text-white overflow-hidden">

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

            const bgClass = isDanger ? 'bg-red-600 text-white' : (isWarning ? 'bg-amber-500 text-white' : 'bg-slate-900 text-white');

            toast.className = `${bgClass} px-4 py-3 rounded-lg shadow-lg text-xs font-semibold flex items-center gap-2 transform transition-all duration-200 translate-y-[-10px] opacity-0 pointer-events-auto border border-slate-700/20`;
            toast.innerHTML = `
                <span>${isDanger ? '⚠️' : (isWarning ? '⚠️' : '✓')}</span>
                <span>${data.message}</span>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('translate-y-[-10px]', 'opacity-0');
            }, 10);

            setTimeout(() => {
                toast.classList.add('translate-y-[-10px]', 'opacity-0');
                setTimeout(() => toast.remove(), 200);
            }, 3000);
        });
    </script>
</body>
</html>
