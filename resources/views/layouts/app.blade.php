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
    <body class="font-sans antialiased">
        <div class="min-h-screen flex bg-[#F8FAFC]">
            @php
                $navItems = [
                    [
                        'label' => 'Dashboard',
                        'href' => route('dashboard'),
                        'active' => request()->routeIs('dashboard'),
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-layout-dashboard" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><rect x="4" y="4" width="6" height="6" rx="1" /><rect x="14" y="4" width="6" height="6" rx="1" /><rect x="4" y="14" width="6" height="6" rx="1" /><rect x="14" y="14" width="6" height="6" rx="1" /></svg>',
                    ],
                    [
                        'label' => 'Projects',
                        'href' => route('projects.index'),
                        'active' => request()->routeIs('projects.*'),
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-folder" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><path d="M5 4h4l2 3h8a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-14a2 2 0 0 1-2-2v-9a2 2 0 0 1 2-2" /></svg>',
                    ],
                    [
                        'label' => 'My Tasks',
                        'href' => route('tasks.index'),
                        'active' => request()->routeIs('tasks.index'),
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-checklist" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><path d="M9 5h12" /><path d="M9 12h12" /><path d="M9 19h12" /><path d="M5 5l-2 2l1 1" /><path d="M5 12l-2 2l1 1" /><path d="M5 19l-2 2l1 1" /></svg>',
                    ],
                    [
                        'label' => 'Messages',
                        'href' => route('messages.inbox'),
                        'active' => request()->routeIs('messages.*'),
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-mail" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><rect x="3" y="5" width="18" height="14" rx="2" /><polyline points="3 7 12 13 21 7" /></svg>',
                        'badge' => 'unread-badge',
                    ],
                    [
                        'label' => 'Team',
                        'href' => route('teams.index'),
                        'active' => request()->routeIs('teams.*'),
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><path d="M9 7a4 4 0 1 0 0 8a4 4 0 0 0 0-8" /><path d="M17 11a4 4 0 1 0 0 8" /><path d="M3 21v-1a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v1" /><path d="M13 21v-1a4 4 0 0 1 4-4h1" /></svg>',
                    ],
                    [
                        'label' => 'AI Assistant',
                        'href' => request()->route('project') ? route('projects.ai', request()->route('project')) : '#',
                        'active' => request()->routeIs('projects.ai'),
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-robot" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><path d="M8 4h8a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-8a2 2 0 0 1-2-2v-6a2 2 0 0 1 2-2z" /><path d="M12 2v2" /><path d="M9 9v1" /><path d="M15 9v1" /><path d="M8 16h8" /><path d="M5 12v4a2 2 0 0 0 2 2h1" /><path d="M19 12v4a2 2 0 0 1-2 2h-1" /></svg>',
                    ],
                    [
                        'label' => 'Settings',
                        'href' => route('profile.edit'),
                        'active' => request()->routeIs('profile.*'),
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><path d="M10.325 4.317a1 1 0 0 1 .945-.69h1.46a1 1 0 0 1 .945 .69l.5 1.5a1 1 0 0 0 .75 .65l1.54 .3a1 1 0 0 1 .8 .98v1.4a1 1 0 0 1-.8 .98l-1.54 .3a1 1 0 0 0-.75 .65l-.5 1.5a1 1 0 0 1-.945 .69h-1.46a1 1 0 0 1-.945-.69l-.5-1.5a1 1 0 0 0-.75-.65l-1.54-.3a1 1 0 0 1-.8-.98v-1.4a1 1 0 0 1 .8-.98l1.54-.3a1 1 0 0 0 .75-.65l.5-1.5z" /><path d="M12 9.5a2.5 2.5 0 1 0 0 5a2.5 2.5 0 0 0 0-5z" /></svg>',
                    ],
                ];
                $avatarPath = Auth::user()?->avatar;
                $avatarUrl = $avatarPath ? asset('storage/' . $avatarPath) : null;
                $userInitial = Auth::user() ? strtoupper(substr(Auth::user()->name, 0, 1)) : 'U';
            @endphp

            <aside class="hidden lg:flex w-[260px] flex-col bg-[#0F172A] text-slate-200 px-6 py-8">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-indigo-500 flex items-center justify-center text-white font-semibold">CS</div>
                    <div>
                        <p class="text-lg font-semibold text-white">CollabSphere</p>
                        <p class="text-xs text-slate-400">Student workspace</p>
                    </div>
                </div>

                <nav class="mt-10 space-y-2">
                    @foreach ($navItems as $item)
                        <a href="{{ $item['href'] }}"
                           class="relative flex items-center gap-3 px-3 py-2 rounded-lg border-l-[3px] {{ $item['active'] ? 'bg-[rgba(99,102,241,0.15)] border-[#6366F1] text-white' : 'border-transparent text-slate-200 hover:bg-white/5' }}"
                           @if ($item['active']) aria-current="page" @endif>
                            <span class="text-indigo-200">{!! $item['icon'] !!}</span>
                            <span class="text-sm font-medium">{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </nav>

                <div class="mt-auto pt-6 text-xs text-slate-400">
                    CollabSphere v1.0
                </div>
            </aside>

            <div class="flex-1 flex flex-col min-h-screen">
                <header class="h-16 bg-white border-b border-slate-200 px-6">
                    <div class="h-full grid grid-cols-[1fr_auto_1fr] items-center gap-4">
                        <div></div>
                        <div class="relative w-full max-w-md justify-self-center">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.5"/>
                                    <path d="M20 20L17 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <input type="text" class="cs-input pl-10 pr-4" placeholder="Search projects, tasks, or teammates" />
                        </div>
                        <div class="flex items-center gap-4 justify-end">
                        <button type="button" class="h-10 w-10 rounded-full border border-slate-200 flex items-center justify-center text-slate-500 hover:text-indigo-600 hover:border-indigo-200 transition relative">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15 18H9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                <path d="M10 21H14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                <path d="M6 9C6 5.68629 8.68629 3 12 3C15.3137 3 18 5.68629 18 9C18 12.121 19 13.5 20 14.5H4C5 13.5 6 12.121 6 9Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="notification-badge hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">0</span>
                        </button>

                        <div x-data="{ open: false }" class="relative">
                            <button type="button" @click="open = !open" class="flex items-center gap-3 rounded-full border border-slate-200 px-2 py-1.5 hover:border-indigo-200 transition">
                                @if ($avatarUrl)
                                    <img src="{{ $avatarUrl }}" alt="Avatar" class="h-8 w-8 rounded-full object-cover" />
                                @else
                                    <div class="h-8 w-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-semibold">
                                        {{ $userInitial }}
                                    </div>
                                @endif
                                <span class="hidden sm:block text-sm font-medium text-slate-700">{{ Auth::user()->name }}</span>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-slate-400">
                                    <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>

                            <div x-show="open" @click.outside="open = false" x-transition
                                 class="absolute right-0 mt-2 w-48 rounded-xl border border-slate-200 bg-white shadow-lg py-2">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Log out</button>
                                </form>
                            </div>
                        </div>
                        </div>
                    </div>
                </header>

                <main class="flex-1 bg-[#F8FAFC] p-8">
                    @isset($header)
                        <div class="mb-6">
                            {{ $header }}
                        </div>
                    @endisset
                    {{ $slot }}
                </main>
            </div>
        </div>

        <script>
            // Poll for unread messages every 30 seconds
            setInterval(async () => {
                try {
                    const response = await fetch('{{ route("api.messages.unreadCount") }}');
                    const data = await response.json();
                    const badge = document.querySelector('.notification-badge');
                    const unreadBadge = document.querySelector('.unread-badge');
                    
                    if (data.unread_count > 0) {
                        if (badge) {
                            badge.textContent = data.unread_count;
                            badge.classList.remove('hidden');
                        }
                        if (unreadBadge) {
                            unreadBadge.textContent = data.unread_count;
                            unreadBadge.classList.remove('hidden');
                        }
                    } else {
                        if (badge) badge.classList.add('hidden');
                        if (unreadBadge) unreadBadge.classList.add('hidden');
                    }
                } catch (error) {
                    console.error('Error polling unread messages:', error);
                }
            }, 30000);

            // Initial load
            document.addEventListener('DOMContentLoaded', async () => {
                try {
                    const response = await fetch('{{ route("api.messages.unreadCount") }}');
                    const data = await response.json();
                    const badge = document.querySelector('.notification-badge');
                    const unreadBadge = document.querySelector('.unread-badge');
                    
                    if (data.unread_count > 0) {
                        if (badge) {
                            badge.textContent = data.unread_count;
                            badge.classList.remove('hidden');
                        }
                        if (unreadBadge) {
                            unreadBadge.textContent = data.unread_count;
                            unreadBadge.classList.remove('hidden');
                        }
                    }
                } catch (error) {
                    console.error('Error loading unread count:', error);
                }
            });
        </script>
    </body>
</html>
