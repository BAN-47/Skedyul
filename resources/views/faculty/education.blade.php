<!doctype html>
<html lang="en">

<head>
    @include('partials.favicon')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKEDYUL — Complete Faculty Profile</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[radial-gradient(circle_at_top_right,#2563eb_0,#0f172a_48%,#020617_100%)] p-4 font-sans text-slate-900 sm:p-8">
    <div class="flex min-h-[calc(100vh-2rem)] items-center justify-center sm:min-h-[calc(100vh-4rem)]">
        <main class="w-full max-w-xl overflow-hidden rounded-3xl bg-white shadow-2xl">
            <div class="bg-gradient-to-r from-blue-700 to-indigo-700 px-6 py-7 text-white sm:px-9 border-4 border-white/20">
                <div class="relative flex items-start justify-end gap-4">
                    <div class="absolute inset-x-0 text-start text-5xl font-extrabold tracking-tight text-white leading-none">
                        SKED<span class="text-blue-400">YUL</span>
                    </div>
                    <div class="relative rounded-full bg-white/15 px-3 py-1 text-xs font-bold text-white">
                        {{ $faculty->fac_employment_type === 'full_time' ? 'Full-Time' : 'Part-Time' }}
                    </div>
                </div>
                <h1 class="mt-12 text-l font-extrabold sm:text-2xl text-start">Complete your profile</h1>
                <p class="mt-2 text-sm leading-relaxed text-blue-100 text-start">Your account is approved. Save your education information before entering your dashboard.</p>
            </div>
            <form class="space-y-5 p-6 sm:p-9" method="POST" action="{{ request()->fullUrl() }}">
                @csrf
                @if(session('warning'))<div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">{{ session('warning') }}</div>@endif
                @if($errors->any())<div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>@endif
                @foreach([
                'fac_bachelors_degree' => ["Bachelor's Degree", 'e.g. Bachelor of Science in Information Systems'],
                'fac_masters_degree' => ["Master's Degree", 'e.g. Master of Information Technology'],
                'fac_doctorate_degree' => ["Doctorate Degree", 'e.g. Doctor of Information Technology'],
                ] as $field => [$label, $placeholder])
                <label class="block text-sm font-bold text-slate-700">{{ $label }}
                    <input name="{{ $field }}" value="{{ old($field, $faculty->{$field}) }}" placeholder="{{ $placeholder }}" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100" @if($field==='fac_bachelors_degree' || ($field==='fac_masters_degree' && $faculty->fac_employment_type === 'full_time')) required @endif>
                </label>
                @endforeach
                <button class="w-full rounded-xl bg-blue-600 px-4 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-blue-200 transition hover:bg-blue-700">Save and Continue</button>
            </form>
        </main>
    </div>
</body>

</html>