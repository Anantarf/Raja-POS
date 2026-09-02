<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F5F5F9] font-sans antialiased selection:bg-[#696CFF] selection:text-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Raja POS - Retail Management System' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
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
                        sneat: {
                            primary: '#696CFF',
                            'primary-hover': '#5F61E6',
                            'primary-light': '#E7E7FF',
                            dark: '#232333',
                            body: '#F5F5F9',
                            muted: '#A1ACB8',
                        }
                    },
                    boxShadow: {
                        'sneat': '0 2px 14px 0 rgba(161, 172, 184, 0.18)',
                        'sneat-hover': '0 4px 20px 0 rgba(161, 172, 184, 0.28)',
                        'sneat-primary': '0 4px 12px 0 rgba(105, 108, 255, 0.4)',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Public Sans', 'Poppins', sans-serif;
            background-color: #F5F5F9;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
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
<body class="h-full bg-[#F5F5F9] flex flex-col text-[#566A7F] overflow-hidden">

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

            const bgClass = isDanger ? 'bg-rose-600 text-white' : (isWarning ? 'bg-amber-500 text-white' : 'bg-[#232333] text-white');

            toast.className = `${bgClass} px-4 py-3 rounded-2xl shadow-sneat text-xs font-bold flex items-center gap-2 transform transition-all duration-200 translate-y-[-8px] opacity-0 pointer-events-auto border border-white/10 font-sans tracking-wide`;
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
