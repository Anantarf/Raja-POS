<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F3F6F4] font-sans antialiased selection:bg-[#3F7A5D] selection:text-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Raja Aksesoris - Retail Management System' }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: #F3F6F4;
            line-height: 1.5;
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
    <script>
        window.webBluetoothThermalPrinter = {
            device: null,
            characteristic: null,

            isSupported() {
                return typeof navigator !== 'undefined' && 'bluetooth' in navigator;
            },

            getSavedDeviceName() {
                return localStorage.getItem('web_bt_printer_name') || '';
            },

            async pairDevice() {
                if (!this.isSupported()) {
                    alert('Web Bluetooth API tidak didukung browser ini. Harap gunakan Google Chrome, Microsoft Edge, atau Opera.');
                    return false;
                }
                try {
                    const device = await navigator.bluetooth.requestDevice({
                        acceptAllDevices: true,
                        optionalServices: [
                            '000018f0-0000-1000-8000-00805f9b34fb',
                            '0000ffe0-0000-1000-8000-00805f9b34fb',
                            '0000ff00-0000-1000-8000-00805f9b34fb',
                            '49535343-fe7d-41a5-8b22-3c9700d378d2',
                            'e7810a71-73ae-499d-8c15-faa9aef0c3f2'
                        ]
                    });

                    this.device = device;
                    const name = device.name || 'Printer Bluetooth';
                    localStorage.setItem('web_bt_printer_name', name);
                    localStorage.setItem('web_bt_printer_id', device.id);

                    const connected = await this.connect();
                    if (connected) {
                        return name;
                    }
                    return false;
                } catch (e) {
                    console.warn('Web Bluetooth cancelled:', e);
                    return false;
                }
            },

            async connect() {
                if (!this.device) {
                    if (!this.isSupported()) return false;
                    if (navigator.bluetooth && typeof navigator.bluetooth.getDevices === 'function') {
                        try {
                            const devices = await navigator.bluetooth.getDevices();
                            if (devices && devices.length > 0) {
                                const savedId = localStorage.getItem('web_bt_printer_id');
                                const found = savedId ? devices.find(d => d.id === savedId) : devices[0];
                                if (found) {
                                    this.device = found;
                                }
                            }
                        } catch (e) {
                            console.warn('getDevices auto-reconnect error:', e);
                        }
                    }
                    if (!this.device) {
                        try {
                            return await this.pairDevice();
                        } catch (e) {
                            return false;
                        }
                    }
                }
                try {
                    if (this.device.gatt && this.device.gatt.connected && this.characteristic) {
                        return true;
                    }
                    const server = await this.device.gatt.connect();
                    const services = await server.getPrimaryServices();
                    for (const service of services) {
                        const characteristics = await service.getCharacteristics();
                        for (const char of characteristics) {
                            if (char.properties.write || char.properties.writeWithoutResponse) {
                                this.characteristic = char;
                                return true;
                            }
                        }
                    }
                    return false;
                } catch (e) {
                    console.error('GATT Connection Error:', e);
                    return false;
                }
            },

            encodeEscPos(data) {
                const encoder = new TextEncoder();
                const bytes = [];
                // 58mm thermal printers physically print 30 chars/line in Font A; 80mm supports 48 chars.
                const maxCols = (data.paperWidth === '80mm') ? 48 : 30;

                const append = (str) => {
                    const arr = encoder.encode(str);
                    for (let i = 0; i < arr.length; i++) bytes.push(arr[i]);
                };
                const raw = (...rawBytes) => {
                    for (let b of rawBytes) bytes.push(b);
                };

                const wordWrap = (str, overrideCols) => {
                    if (!str) return '';
                    const cols = overrideCols || maxCols;
                    const paragraphs = String(str).split('\n');
                    const resultLines = [];

                    const isOrphanPrefix = (w) => /^(\(?rp\.?|\(?idr\.?|\(?no\.?)$/i.test(w);

                    paragraphs.forEach(para => {
                        const words = para.trim().split(/\s+/);
                        let currentLine = [];

                        words.forEach(word => {
                            if (!word) return;
                            const lineStr = currentLine.join(' ');
                            const candidate = lineStr ? lineStr + ' ' + word : word;

                            if (candidate.length <= cols) {
                                currentLine.push(word);
                            } else {
                                if (currentLine.length > 1 && isOrphanPrefix(currentLine[currentLine.length - 1])) {
                                    const orphan = currentLine.pop();
                                    resultLines.push(currentLine.join(' '));
                                    currentLine = [orphan, word];
                                } else if (currentLine.length > 0) {
                                    resultLines.push(currentLine.join(' '));
                                    while (word.length > cols) {
                                        resultLines.push(word.substring(0, cols));
                                        word = word.substring(cols);
                                    }
                                    currentLine = [word];
                                } else {
                                    while (word.length > cols) {
                                        resultLines.push(word.substring(0, cols));
                                        word = word.substring(cols);
                                    }
                                    currentLine = [word];
                                }
                            }
                        });
                        if (currentLine.length > 0) {
                            resultLines.push(currentLine.join(' '));
                        }
                    });

                    return resultLines.join('\n');
                };

                const formatRow = (left, right) => {
                    left = String(left || '');
                    right = String(right || '');
                    if (left.length + right.length + 1 <= maxCols) {
                        let spaceCount = maxCols - left.length - right.length;
                        return left + ' '.repeat(spaceCount) + right;
                    }
                    let spaceCount = Math.max(0, maxCols - right.length);
                    return left + '\n' + ' '.repeat(spaceCount) + right;
                };

                // Initialize printer
                raw(0x1B, 0x40);

                // Center align
                raw(0x1B, 0x61, 0x01);

                // Store Name (Bold + Double Width & Double Height with proper line spacing)
                raw(0x1B, 0x45, 0x01, 0x1D, 0x21, 0x11);
                append(wordWrap(data.storeName || 'RAJA AKSESORIS', Math.floor(maxCols / 2)) + '\n');

                // Reset Text Size & Bold + Reset Line Spacing
                raw(0x1D, 0x21, 0x00, 0x1B, 0x45, 0x00, 0x1B, 0x32);
                append('\n');

                if (data.tagline) append(wordWrap(data.tagline) + '\n');
                if (data.address) append(wordWrap(data.address) + '\n');
                if (data.phone) append(wordWrap('Telp: ' + data.phone) + '\n');

                append('-'.repeat(maxCols) + '\n');

                // Left align
                raw(0x1B, 0x61, 0x00);
                append(wordWrap('No  : ' + (data.invoiceNumber || '-')) + '\n');
                append(wordWrap('Tgl : ' + (data.date || '-')) + '\n');
                if (data.cashier) append(wordWrap('Kasir: ' + data.cashier) + '\n');

                append('-'.repeat(maxCols) + '\n');

                if (data.items && data.items.length) {
                    data.items.forEach(item => {
                        append(wordWrap(item.name || '') + '\n');
                        let lineQtyPrice = ' ' + item.qty + ' x ' + item.price;
                        append(formatRow(lineQtyPrice, item.subtotal) + '\n');
                    });
                }

                append('-'.repeat(maxCols) + '\n');

                // Totals section (Left-Right 2-column format)
                raw(0x1B, 0x45, 0x01); // Bold on
                append(formatRow('TOTAL', data.total || '0') + '\n');
                raw(0x1B, 0x45, 0x00); // Bold off

                if (data.payments) {
                    data.payments.forEach(p => {
                        append(formatRow(p.method, p.amount) + '\n');
                    });
                }

                if (data.change && data.change !== 'Rp 0' && data.change !== 'Rp 0.00' && data.change !== 'Rp0') {
                    append(formatRow('KEMBALI', data.change) + '\n');
                }

                append('-'.repeat(maxCols) + '\n');

                // Center align for footer
                raw(0x1B, 0x61, 0x01);
                append(wordWrap(data.footer || 'Terima Kasih!') + '\n\n\n\n');

                // Paper Feed & Cut
                raw(0x1B, 0x64, 0x04);

                return new Uint8Array(bytes);
            },

            async printReceipt(receiptData) {
                let connected = await this.connect();
                if (!connected) {
                    const devName = await this.pairDevice();
                    if (!devName) return false;
                }

                try {
                    const dataBytes = this.encodeEscPos(receiptData);
                    const chunkSize = 100;
                    for (let i = 0; i < dataBytes.length; i += chunkSize) {
                        const chunk = dataBytes.slice(i, i + chunkSize);
                        if (this.characteristic.properties.writeWithoutResponse) {
                            await this.characteristic.writeValueWithoutResponse(chunk);
                        } else {
                            await this.characteristic.writeValue(chunk);
                        }
                    }
                    return true;
                } catch (e) {
                    console.error('Web Bluetooth Print Error:', e);
                    alert('Gagal mengirim data cetak ke Bluetooth printer: ' + e.message);
                    return false;
                }
            }
        };
    </script>
</body>
</html>
