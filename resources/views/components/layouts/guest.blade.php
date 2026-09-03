<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F3F6F4] font-sans antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Raja Aksesoris</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Public Sans"', 'Poppins', 'sans-serif'],
                        mono: ['Poppins', 'monospace'],
                    }
                }
            }
        }
    </script>
    <style>
        html {
            font-size: 110%;
        }
    </style>
    @livewireStyles
</head>
<body class="h-full bg-[#F3F6F4] flex items-center justify-center p-4 text-[#232E28] relative overflow-hidden">
    <!-- Google Antigravity Inspired Interactive Particle Canvas -->
    <canvas id="antigravity-canvas" class="fixed inset-0 pointer-events-none z-0"></canvas>

    <!-- Main Login Card Container -->
    <div class="relative z-10 w-full flex justify-center">
        {{ $slot }}
    </div>

    @livewireScripts

    <script>
        (function() {
            const canvas = document.getElementById('antigravity-canvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');

            let width = canvas.width = window.innerWidth;
            let height = canvas.height = window.innerHeight;

            // Handle window resize
            window.addEventListener('resize', () => {
                width = canvas.width = window.innerWidth;
                height = canvas.height = window.innerHeight;
                initParticles();
            });

            // Raja Aksesoris EMCO Palette Variations
            const colors = [
                '#3F7A5D', // Deep Jade Emerald
                '#32634B', // Darker Jade
                '#C2AC7C', // Warm Sand Ochre
                '#D9A21B', // Golden Yellow Accent
                '#A9D1A0', // Fresh Mint
                '#718379', // Slate Sage
                '#A3B8AD'  // Soft Sage Tint
            ];

            let mouse = { x: width * 0.7, y: height * 0.35, radius: 180 };

            window.addEventListener('mousemove', (e) => {
                mouse.x = e.clientX;
                mouse.y = e.clientY;
            });

            class Particle {
                constructor() {
                    this.reset(true);
                }

                reset(initial = false) {
                    this.x = initial ? Math.random() * width : (Math.random() < 0.5 ? -20 : width + 20);
                    this.y = initial ? Math.random() * height : Math.random() * height;
                    this.size = Math.random() * 3 + 1.5;
                    this.length = Math.random() > 0.35 ? Math.random() * 9 + 3 : 0; // Antigravity dash particles
                    this.angle = Math.random() * Math.PI * 2;
                    this.speed = Math.random() * 0.5 + 0.2;
                    this.vx = Math.cos(this.angle) * this.speed;
                    this.vy = Math.sin(this.angle) * this.speed;
                    this.color = colors[Math.floor(Math.random() * colors.length)];
                    this.alpha = Math.random() * 0.6 + 0.3;
                    this.spinSpeed = (Math.random() - 0.5) * 0.03;
                }

                update() {
                    this.x += this.vx;
                    this.y += this.vy;
                    this.angle += this.spinSpeed;

                    // Mouse interaction - gentle antigravity repulsion
                    const dx = mouse.x - this.x;
                    const dy = mouse.y - this.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);

                    if (dist < mouse.radius && dist > 0) {
                        const force = (mouse.radius - dist) / mouse.radius;
                        const forceX = (dx / dist) * force * 1.8;
                        const forceY = (dy / dist) * force * 1.8;
                        this.x -= forceX;
                        this.y -= forceY;
                    }

                    // Reset when moving offscreen
                    if (this.x < -50 || this.x > width + 50 || this.y < -50 || this.y > height + 50) {
                        this.reset();
                    }
                }

                draw() {
                    ctx.save();
                    ctx.globalAlpha = this.alpha;
                    ctx.fillStyle = this.color;
                    ctx.strokeStyle = this.color;
                    ctx.lineWidth = this.size;

                    if (this.length > 0) {
                        // Render dash particle like Google Antigravity
                        ctx.translate(this.x, this.y);
                        ctx.rotate(this.angle);
                        ctx.beginPath();
                        ctx.moveTo(-this.length / 2, 0);
                        ctx.lineTo(this.length / 2, 0);
                        ctx.lineCap = 'round';
                        ctx.stroke();
                    } else {
                        // Render round dot
                        ctx.beginPath();
                        ctx.arc(this.x, this.y, this.size / 2, 0, Math.PI * 2);
                        ctx.fill();
                    }

                    ctx.restore();
                }
            }

            let particles = [];
            function initParticles() {
                particles = [];
                const particleCount = Math.floor((width * height) / 8000);
                for (let i = 0; i < Math.min(particleCount, 160); i++) {
                    particles.push(new Particle());
                }
            }

            initParticles();

            function animate() {
                ctx.clearRect(0, 0, width, height);

                // Radial ambient lighting in background
                const bgGradient = ctx.createRadialGradient(
                    width * 0.75, height * 0.35, 40,
                    width * 0.75, height * 0.35, width * 0.55
                );
                bgGradient.addColorStop(0, 'rgba(227, 238, 232, 0.45)');
                bgGradient.addColorStop(1, 'rgba(243, 246, 244, 0)');
                ctx.fillStyle = bgGradient;
                ctx.fillRect(0, 0, width, height);

                particles.forEach(p => {
                    p.update();
                    p.draw();
                });

                requestAnimationFrame(animate);
            }

            animate();
        })();
    </script>
</body>
</html>
