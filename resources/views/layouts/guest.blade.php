<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col lg:flex-row">
            <aside class="lg:w-2/5 w-full bg-[#0F172A] text-white px-10 py-12 flex flex-col justify-between">
                <div>
                    <a href="/" class="inline-flex items-center gap-3">
                        <svg width="200" height="36" viewBox="0 0 200 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="36" height="36" rx="10" fill="#6366F1"/>
                            <path d="M11 18C11 13.5817 14.5817 10 19 10H25C29.4183 10 33 13.5817 33 18C33 22.4183 29.4183 26 25 26H19C14.5817 26 11 22.4183 11 18Z" fill="#0F172A"/>
                            <path d="M15 18C15 15.2386 17.2386 13 20 13H26C28.7614 13 31 15.2386 31 18C31 20.7614 28.7614 23 26 23H20C17.2386 23 15 20.7614 15 18Z" fill="#F8FAFC"/>
                            <text x="46" y="24" fill="#F8FAFC" font-size="18" font-weight="700" font-family="Figtree, sans-serif">CollabSphere</text>
                        </svg>
                    </a>

                    <p class="mt-8 text-lg text-slate-200">Where student teams get things done</p>

                    <div class="mt-10 space-y-6">
                        <div class="flex gap-4">
                            <div class="h-10 w-10 rounded-xl bg-white/10 flex items-center justify-center">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="3" y="4" width="6" height="16" rx="2" stroke="#F8FAFC" stroke-width="1.5"/>
                                    <rect x="10.5" y="8" width="6" height="12" rx="2" stroke="#F8FAFC" stroke-width="1.5"/>
                                    <rect x="18" y="6" width="3" height="14" rx="1.5" stroke="#F8FAFC" stroke-width="1.5"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-base font-semibold">Kanban clarity</p>
                                <p class="text-sm text-slate-300">Visualize work across planning, build, and review.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="h-10 w-10 rounded-xl bg-white/10 flex items-center justify-center">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 19C8.5 17 10.5 17 13 19C15.5 21 17.5 21 20 19" stroke="#F8FAFC" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M4 9C4 6.23858 6.23858 4 9 4H15C17.7614 4 20 6.23858 20 9V13C20 15.7614 17.7614 18 15 18H9C6.23858 18 4 15.7614 4 13V9Z" stroke="#F8FAFC" stroke-width="1.5"/>
                                    <circle cx="9" cy="11" r="1.5" fill="#F8FAFC"/>
                                    <circle cx="15" cy="11" r="1.5" fill="#F8FAFC"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-base font-semibold">AI co-pilot</p>
                                <p class="text-sm text-slate-300">Generate smart suggestions and next steps.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="h-10 w-10 rounded-xl bg-white/10 flex items-center justify-center">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4 20V10" stroke="#F8FAFC" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M10 20V4" stroke="#F8FAFC" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M16 20V12" stroke="#F8FAFC" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M22 20V8" stroke="#F8FAFC" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-base font-semibold">Live progress</p>
                                <p class="text-sm text-slate-300">Track momentum with real-time updates.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="text-xs text-slate-400 mt-10">Powered by CollabSphere</p>
            </aside>

            <main class="lg:w-3/5 w-full bg-white flex items-center justify-center px-8 py-12">
                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
