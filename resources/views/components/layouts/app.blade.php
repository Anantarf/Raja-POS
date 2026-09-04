<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F3F6F4] font-sans antialiased selection:bg-[#3F7A5D] selection:text-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Raja Aksesoris - Retail Management System' }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html {
            font-size: 110%;
        }
        body {
            font-family: 'Public Sans', 'Poppins', sans-serif;
            background-color: #F3F6F4;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
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
<body class="h-full bg-[#F3F6F4] flex flex-col text-[#232E28] overflow-hidden">

    {{ $slot }}

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

            const iconSpan = document.createElement('span');
            iconSpan.className = 'inline-flex items-center justify-center shrink-0';
            const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('class', 'w-4 h-4');
            svg.setAttribute('fill', 'none');
            svg.setAttribute('stroke', 'currentColor');
            svg.setAttribute('viewBox', '0 0 24 24');
            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.setAttribute('stroke-linecap', 'round');
            path.setAttribute('stroke-linejoin', 'round');
            path.setAttribute('stroke-width', '2.5');
            if (isDanger || isWarning) {
                path.setAttribute('d', 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z');
            } else {
                path.setAttribute('d', 'M5 13l4 4L19 7');
            }
            svg.appendChild(path);
            iconSpan.appendChild(svg);
            const msgSpan = document.createElement('span');
            msgSpan.textContent = data.message || '';

            toast.appendChild(iconSpan);
            toast.appendChild(msgSpan);

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
