<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Raja POS - Retail Management System' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        navy: {
                            50: '#EEF4FF',
                            100: '#DCE8F8',
                            500: '#1E3A8A',
                            700: '#172554',
                            900: '#0F172A',
                        },
                        brand: {
                            blue: '#2563EB',
                            gold: '#D4A017',
                        }
                    }
                }
            }
        }
    </script>
    @livewireStyles
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-100 flex flex-col">

    {{ $slot }}

    @livewireScripts
    <script>
        window.addEventListener('notify', event => {
            const data = event.detail;
            alert(data.message);
        });
    </script>
</body>
</html>
