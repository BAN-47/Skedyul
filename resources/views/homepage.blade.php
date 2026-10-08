<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKEDYUL</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght@6..144,1..1000&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>

<body class="font-sans text-white antialiased">

    {{-- Page wrapper with background --}}
    <div class="relative min-h-screen overflow-hidden"
        style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #1a2d5a 100%);">

        {{-- Background glow --}}
        <div class="pointer-events-none absolute inset-0"
            style="background: radial-gradient(ellipse at 20% 50%, rgba(37,99,235,.3) 0%, transparent 60%),
                               radial-gradient(ellipse at 80% 10%, rgba(8,145,178,.2) 0%, transparent 50%);">
        </div>

        {{-- Grid overlay --}}
        <div class="pointer-events-none absolute inset-0"
            style="background-image: linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
                                     linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
                   background-size: 40px 40px;">
        </div>

        {{-- Decorative circles --}}
        <div class="pointer-events-none absolute -top-20 -right-16 h-72 w-72 rounded-full border border-white/[.07] bg-white/[.04]"></div>
        <div class="pointer-events-none absolute bottom-16 right-20 h-44 w-44 rounded-full border border-white/[.07] bg-white/[.04]"></div>
        <div class="pointer-events-none absolute bottom-48 left-8 h-20 w-20 rounded-full border border-white/[.07] bg-white/[.04]"></div>

        {{-- Content (sits above the background) --}}
        <div class="relative z-10">

            {{-- HEADER --}}
            <header class="border-b border-white/10 bg-[#A9A9A9] backdrop-blur-md">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                    <a href="/" class="text-3xl font-extrabold leading-none tracking-tight text-white sm:text-4xl lg:text-5xl">
                        SKED<span class="text-blue-400">YUL</span>
                    </a>

                    <nav class="flex items-center gap-4 text-sm font-medium sm:gap-6 lg:gap-8">
                        <a href="/" class="hidden text-white/80 transition hover:text-white hover:underline hover:underline-offset-8 md:inline">Home</a>
                        <a href="/about" class="hidden text-white/80 transition hover:text-white hover:underline hover:underline-offset-8 md:inline">About Us</a>
                        <a href="/features" class="hidden text-white/80 transition hover:text-white hover:underline hover:underline-offset-8 md:inline">Features</a>
                        <a href="/contact" class="hidden text-white/80 transition hover:text-white hover:underline hover:underline-offset-8 md:inline">Contact</a>
                        <a href="/login" class="inline-flex items-center rounded-lg bg-blue-600 px-6 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-600/30 transition hover:-translate-y-px hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-slate-900">
                            LOG IN
                        </a>
                    </nav>
                </div>
            </header>

            {{-- HERO TEXT --}}
            <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
                <h1 class="font-['Google_Sans_Flex',sans-serif] text-5xl font-bold uppercase leading-[1.05] tracking-tight text-white sm:text-6xl lg:text-7xl">
                    ACADEMIC<br>
                    <span class="bg-gradient-to-r from-blue-300 to-cyan-300 bg-clip-text text-transparent">SCHEDULING.</span>
                </h1>
                <h2 class="mt-6 font-['Montserrat',sans-serif] text-lg font-semibold uppercase tracking-widest text-white/80 sm:text-xl lg:text-2xl">
                    MADE CLEARER. MADE BETTER.
                </h2>
            </div>

        </div>
    </div>

</body>

</html>