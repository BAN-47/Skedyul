<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

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
            <header class="fixed inset-x-0 top-0 z-50 -mb-20 border-b border-white/10 bg-[#A9A9A9] backdrop-blur-md">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                    <a href="#skedyul" class="text-3xl font-extrabold leading-none tracking-tight text-white sm:text-4xl lg:text-5xl">
                        SKED<span class="text-blue-400">YUL</span>
                    </a>

                    <nav class="flex items-center gap-4 text-sm font-medium sm:gap-6 lg:gap-8">
                        <a href="#home" class="hidden text-white/80 transition hover:text-white hover:underline hover:underline-offset-8 md:inline">Home</a>
                        <a href="#features" class="hidden text-white/80 transition hover:text-white hover:underline hover:underline-offset-8 md:inline">Features</a>
                        <a href="#about" class="hidden text-white/80 transition hover:text-white hover:underline hover:underline-offset-8 md:inline">About Us</a>
                        <a href="#contact" class="hidden text-white/80 transition hover:text-white hover:underline hover:underline-offset-8 md:inline">Contact</a>
                        <a href="/login" class="inline-flex items-center rounded-lg bg-blue-600 px-6 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-600/30 transition hover:-translate-y-px hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-slate-900">
                            LOG IN
                        </a>
                    </nav>
                </div>
            </header>

            {{-- HERO --}}
            <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 pt-20 pb-4 sm:px-6 lg:grid-cols-2 lg:px-8 lg:pt-28 lg:pb-6" id="home">
                <div>
                    <h1 class="font-['Google_Sans_Flex',sans-serif] text-5xl font-bold uppercase leading-[1.05] tracking-tight text-white sm:text-6xl lg:text-7xl">
                        Academic<br>
                        <span class="bg-gradient-to-r from-blue-300 to-cyan-300 bg-clip-text text-transparent">Scheduling.</span>
                    </h1>

                    <h2 class="mt-6 font-['Montserrat',sans-serif] text-lg font-semibold uppercase tracking-widest text-white/80 sm:text-xl lg:text-2xl">
                        Made clearer. Made better.
                    </h2>

                    <p class="mt-6 max-w-2xl border-l-4 border-blue-400 pl-5 font-['Montserrat',sans-serif] text-base leading-relaxed text-white/70 sm:text-lg">
                        Simplify your faculty scheduling with SKEDYUL,
                        a place where you can manage and organize your academic schedules
                        <span class="font-semibold text-blue-300">with ease.</span>
                    </p>

                    <a href="#" class="mt-8 inline-flex items-center rounded-lg bg-blue-600 px-6 py-3 text-lg font-semibold text-white shadow-lg shadow-blue-600/30 transition hover:-translate-y-px hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-slate-900">
                        Get Started with Us
                    </a>
                    <a href="#" class="mt-8 ml-6 inline-flex items-center rounded-lg bg-gray-400 px-6 py-3 text-lg font-semibold text-[#FFFFFF] shadow-lg shadow-blue-600/30 transition hover:-translate-y-px hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-slate-900">
                        See how it works
                    </a>
                </div>

                {{-- RIGHT: put your content here --}}
                <div class="flex ml-10 items-center justify-center animate-slide-in">
                    <div class="animate-float">
                        <img src="/images/skedyul calendar.svg" alt="Hero Image" class="w-full rounded-lg">
                    </div>
                </div>

            </div>

            <section id="features" class="mx-auto max-w-7xl scroll-mt-24 px-4 pb-16 sm:px-6 lg:px-8">
                <hr class="mb-8 border-t-[15px] border-white/20 sm:mb-12 lg:mb-16" id="features">

                <h2 class="font-['Montserrat',sans-serif] -mt-8 text-2xl font-semibold uppercase tracking-widest text-white/80 underline underline-offset-8 sm:text-2xl lg:text-3xl">
                    CORE FEATURES
                </h2>

                <p class="mt-6 border-l-4 border-blue-400 pl-5 text-justify max-w-l font-['Montserrat',sans-serif] text-base leading-relaxed text-white/70 sm:text-lg">
                    These core features are designed to enhance your academic scheduling experience, promising efficiency between faculty and students.
                    With SKEDYUL, you can easily manage your schedules, ensuring that everyone stays on track and informed.
                </p>

                {{-- CAROUSEL --}}
                <div class="relative mt-12">

                    <button type="button" id="features-prev" aria-label="Previous"
                        class="absolute left-2 top-1/2 z-20 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-white/20 bg-slate-900/70 text-xl text-white shadow-lg backdrop-blur transition hover:bg-blue-600 disabled:pointer-events-none disabled:opacity-30">
                        ‹
                    </button>

                    <button type="button" id="features-next" aria-label="Next"
                        class="absolute right-2 top-1/2 z-20 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-white/20 bg-slate-900/70 text-xl text-white shadow-lg backdrop-blur transition hover:bg-blue-600 disabled:pointer-events-none disabled:opacity-30">
                        ›
                    </button>

                    <div id="features-track"
                        class="-mt-10 flex snap-x snap-mandatory gap-6 overflow-x-auto scroll-smooth py-16 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">

                        {{-- Card 1 --}}
                        <div data-card class="flex h-[500px] w-[300px] shrink-0 cursor-pointer snap-center scale-90 flex-col overflow-hidden rounded-xl border border-white/10 bg-white/[.06] opacity-50 backdrop-blur transition-all duration-300 sm:h-[450px] lg:h-[450px] sm:w-[500px] lg:w-[500px]">
                            <div class="h-3/5 w-full overflow-hidden">
                                <img src="{{ asset('images/PBS schedule plotter.jpe') }}" alt="PBS Schedule Plotter" class="h-full w-full object-cover">
                            </div>
                            <div class="flex h-2/5 flex-col overflow-hidden p-5 text-center">
                                <h3 class="font-['Montserrat',sans-serif] text-base font-bold text-white">Program by Section (PBS)</h3>
                                <p class="mt-1.5 line-clamp-3 text-sm leading-relaxed text-white/60">Organize class schedules by program and section, with every subject, room, and time slot in one clear view.</p>
                            </div>
                        </div>

                        {{-- Card 2 --}}
                        <div data-card class="flex h-[500px] w-[300px] shrink-0 cursor-pointer snap-center scale-90 flex-col overflow-hidden rounded-xl border border-white/10 bg-white/[.06] opacity-50 backdrop-blur transition-all duration-300 sm:h-[450px] lg:h-[450px] sm:w-[500px] lg:w-[500px]">
                            <div class="h-3/5 w-full overflow-hidden">
                                <img src="{{ asset('images/PBT schedule.jpe') }}" alt="PBT Schedule" class="h-full w-full object-cover">
                            </div>
                            <div class="flex h-2/5 flex-col overflow-hidden p-5 text-center">
                                <h3 class="font-['Montserrat',sans-serif] text-base font-bold text-white">Program by Teacher (PBT)</h3>
                                <p class="mt-1.5 line-clamp-3 text-sm leading-relaxed text-white/60">View each teacher's weekly schedule, including subjects, rooms, and time slots, to track teaching loads at a glance.</p>
                            </div>
                        </div>

                        {{-- Card 3 --}}
                        <div data-card class="flex h-[500px] w-[300px] shrink-0 cursor-pointer snap-center scale-90 flex-col overflow-hidden rounded-xl border border-white/10 bg-white/[.06] opacity-50 backdrop-blur transition-all duration-300 sm:h-[450px] lg:h-[450px] sm:w-[500px] lg:w-[500px]">
                            <div class="h-3/5 w-full overflow-hidden">
                                <img src="{{ asset('images/MIS class.jpe') }}" alt="MIS Class" class="h-full w-full object-cover">
                            </div>
                            <div class="flex h-2/5 flex-col overflow-hidden p-5 text-center">
                                <h3 class="font-['Montserrat',sans-serif] text-base font-bold text-white">Management Information System (MIS)</h3>
                                <p class="mt-1.5 line-clamp-3 text-sm leading-relaxed text-white/60">Manage and organize faculty, subjects, rooms, and sections in one centralized system, keeping all scheduling records accurate and up to date.</p>
                            </div>
                        </div>

                        {{-- Card 4 --}}
                        <div data-card class="flex h-[500px] w-[300px] shrink-0 cursor-pointer snap-center scale-90 flex-col overflow-hidden rounded-xl border border-white/10 bg-white/[.06] opacity-50 backdrop-blur transition-all duration-300 sm:h-[450px] lg:h-[450px] sm:w-[500px] lg:w-[500px]">
                            <div class="h-3/5 w-full overflow-hidden">
                                <img src="{{ asset('images/faculty load.jpe') }}" alt="Faculty Load" class="h-full w-full object-cover">
                            </div>
                            <div class="flex h-2/5 flex-col overflow-hidden p-5 text-center">
                                <h3 class="font-['Montserrat',sans-serif] text-base font-bold text-white">Faculty Load</h3>
                                <p class="mt-1.5 line-clamp-3 text-sm leading-relaxed text-white/60">Track and manage the workload of each faculty member across different subjects and time slots.</p>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            <section id="features" class="mx-auto max-w-7xl -mt-15 scroll-mt-24 px-4 pb-16 sm:px-6 lg:px-8">
                <hr class="mb-8 border-t-[15px] border-white/20 sm:mb-12 lg:mb-16" id="about">

                <h2 class="font-['Montserrat',sans-serif] -mt-8 text-2xl font-semibold uppercase tracking-widest text-white/80 underline underline-offset-8 sm:text-2xl lg:text-3xl">
                    ABOUT US
                </h2>

                <div class="flex items-center justify-between gap-6 mt-10">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex h-52 w-52 items-center justify-center overflow-hidden rounded-full border-[10px] border-blue-600 bg-white shadow-xl shadow-blue-600/30 transition duration-300 hover:-translate-y-2 hover:border-blue-400">
                            <img src="{{ asset('images/about.png') }}" alt="About SKEDYUL" class="h-35 w-35 object-contain">
                        </div>
                        <h2 class="mt-5 font-['Montserrat',sans-serif] text-lg font-bold uppercase tracking-wide text-white">SYSTEM</h2>
                        <h3 class="mt-1 max-w-[14rem] text-sm leading-relaxed text-white/60">
                            <span class="font-semibold text-gray-300">
                                SKED<span class="text-blue-400">YUL</span>
                            </span> is a smart faculty scheduling system that helps colleges plot subjects,
                            assign rooms and sections, detect conflicts instantly,
                            and balance teaching workloads, all in one place.
                        </h3>
                    </div>
                    <div class="flex flex-col items-center text-center">
                        <div class="flex h-52 w-52 items-center justify-center overflow-hidden rounded-full border-[10px] border-blue-600 bg-white shadow-xl shadow-blue-600/30 transition duration-300 hover:-translate-y-2 hover:border-blue-400">
                            <img src="{{ asset('images/creation.png') }}" alt="Creation" class="h-35 w-35 object-contain">
                        </div>
                        <h2 class="mt-5 font-['Montserrat',sans-serif] text-lg font-bold uppercase tracking-wide text-white">CREATION</h2>
                        <h3 class="mt-1 max-w-[14rem] text-sm leading-relaxed text-white/60">
                            First developed back in February 2026, <span class="font-semibold text-gray-300">
                                SKED<span class="text-blue-400">YUL</span></span> was created by BSIS college students from Cebu Technological Univesity - Main Campus
                            to address the problem of handling manual faculty scheduling within departments.
                        </h3>
                    </div>
                    <div class="flex flex-col items-center text-center">
                        <div class="flex h-52 w-52 items-center justify-center overflow-hidden rounded-full border-[10px] border-blue-600 bg-white shadow-xl shadow-blue-600/30 transition duration-300 hover:-translate-y-2 hover:border-blue-400">
                            <img src="{{ asset('images/Roles.png') }}" alt="Roles" class="h-35 w-35 object-contain">
                        </div>
                        <h2 class="mt-5 font-['Montserrat',sans-serif] text-lg font-bold uppercase tracking-wide text-white">ROLES</h2>
                        <h3 class="mt-1 max-w-[14rem] text-sm leading-relaxed text-white/60">
                            <span class="font-semibold text-gray-300">
                                SKED<span class="text-blue-400">YUL</span></span>
                            has three roles based on the department hierarchy: the Dean,
                            the Department Chairperson, and the Faculty Member. Each role has its
                            own dashboard with features tailored to its specific responsibilities.
                        </h3>
                    </div>
                    <div class="flex flex-col items-center text-center">
                        <div class="flex h-52 w-52 items-center justify-center overflow-hidden rounded-full border-[10px] border-blue-600 bg-white shadow-xl shadow-blue-600/30 transition duration-300 hover:-translate-y-2 hover:border-blue-400">
                            <img src="{{ asset('images/web.png') }}" alt="Current Website Status" class="h-35 w-35 object-contain">
                        </div>
                        <h2 class="mt-5 font-['Montserrat',sans-serif] text-lg font-bold uppercase tracking-wide text-white">APPLICATION</h2>
                        <h3 class="mt-1 max-w-[14rem] text-sm leading-relaxed text-white/60">
                            <span class="font-semibold text-gray-300">
                                SKED<span class="text-blue-400">YUL</span></span>
                            can be accessed on the web through any browser, or on mobile by downloading the app
                            from the Google Play Store (Android), so you can manage your schedules anytime, anywhere.
                        </h3>
                    </div>
                </div>
            </section>

            <footer class="w-full bg-[#CED3E8] text-slate-900" id="contact">
                <div class="mx-auto grid max-w-7xl gap-10 px-6 py-10 md:grid-cols-2 md:px-10">
                    <div>
                        <div class="text-5xl font-extrabold leading-none tracking-tight text-gray-500">
                            SKED<span class="text-sky-500">YUL</span>
                        </div>

                        <p class="mt-4 max-w-md text-sm leading-6 text-slate-700">
                            Smart academic scheduling for faster planning, better coordination, and a smoother workflow.
                        </p>

                        <p class="mt-3 text-sm text-slate-600">© 2026 SKEDYUL. All rights reserved.</p>
                    </div>

                    {{-- RIGHT --}}
                    <div>
                        <h4 class="text-l font-bold">Location of Development</h4>
                        <p class="mt-2 text-sm leading-6 text-slate-700">
                            College of Computer, Information, and Communications Technology,<br>
                            Cebu Technological University - Main Campus,<br>
                            Corner M.J. Cuenco Ave, R. Palma Street,<br>
                            Cebu City, 6000
                        </p>

                        <h4 class="mt-5 text-l font-bold">Contact Us:</h4>

                        <div class="mt-2 flex items-center gap-2 text-sm text-slate-700">
                            <span class="text-xl font-bold text-slate-900">☏:</span>
                            <span></span>
                        </div>

                        <div class="mt-2 flex items-center gap-2 text-sm text-slate-700">
                            <span class="text-xl font-bold text-slate-900">✉:</span>
                            <span></span>
                        </div>
                    </div>

                </div>
            </footer>
        </div>
    </div>

    <script>
        const track = document.getElementById('features-track');
        const cards = [...track.querySelectorAll('[data-card]')];
        const prev = document.getElementById('features-prev');
        const next = document.getElementById('features-next');
        const GAP = 24;

        function setPadding() {
            const pad = (track.clientWidth - cards[0].offsetWidth) / 2;
            track.style.paddingLeft = pad + 'px';
            track.style.paddingRight = pad + 'px';
        }

        function activeIndex() {
            const center = track.scrollLeft + track.clientWidth / 2;
            let best = 0,
                bestDist = Infinity;
            cards.forEach((card, i) => {
                const cardCenter = card.offsetLeft + card.offsetWidth / 2;
                const dist = Math.abs(cardCenter - center);
                if (dist < bestDist) {
                    bestDist = dist;
                    best = i;
                }
            });
            return best;
        }

        function update() {
            const active = activeIndex();
            cards.forEach((card, i) => {
                const isActive = i === active;
                card.classList.toggle('scale-110', isActive);
                card.classList.toggle('opacity-100', isActive);
                card.classList.toggle('z-10', isActive);
                card.classList.toggle('scale-90', !isActive);
                card.classList.toggle('opacity-50', !isActive);
            });
            prev.disabled = active === 0;
            next.disabled = active === cards.length - 1;
        }

        function goTo(i, smooth = true) {
            i = Math.max(0, Math.min(cards.length - 1, i));
            const card = cards[i];
            track.scrollTo({
                left: card.offsetLeft - (track.clientWidth - card.offsetWidth) / 2,
                behavior: smooth ? 'smooth' : 'auto'
            });
        }

        prev.addEventListener('click', () => goTo(activeIndex() - 1));
        next.addEventListener('click', () => goTo(activeIndex() + 1));
        cards.forEach((card, i) => card.addEventListener('click', () => goTo(i)));
        track.addEventListener('scroll', update, {
            passive: true
        });

        window.addEventListener('resize', () => {
            setPadding();
            goTo(activeIndex(), false);
            update();
        });

        setPadding();
        goTo(1, false);
        update();
    </script>
</body>

</html>