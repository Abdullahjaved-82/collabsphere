<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts & Styling -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{ asset('css/animations.css') }}">
    </head>
    <body class="font-sans antialiased text-slate-800" x-data="{ mobileMenuOpen: false }">
        
        <!-- Flash session Toast dispatcher hooks -->
        <x-toast />
        
        @if (session('success'))
            <div x-data x-init="window.dispatchEvent(new CustomEvent('toast', { detail: { message: '{{ session('success') }}', type: 'success' } }))"></div>
        @endif
        @if (session('error'))
            <div x-data x-init="window.dispatchEvent(new CustomEvent('toast', { detail: { message: '{{ session('error') }}', type: 'error' } }))"></div>
        @endif
        @if (session('warning'))
            <div x-data x-init="window.dispatchEvent(new CustomEvent('toast', { detail: { message: '{{ session('warning') }}', type: 'warning' } }))"></div>
        @endif
        @if (session('info'))
            <div x-data x-init="window.dispatchEvent(new CustomEvent('toast', { detail: { message: '{{ session('info') }}', type: 'info' } }))"></div>
        @endif

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
                        'active' => request()->routeIs('projects.*') && !request()->routeIs('projects.ai'),
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
                        'href' => request()->route('project') ? route('projects.ai', request()->route('project')) : route('ai.hub'),
                        'active' => request()->routeIs('projects.ai') || request()->routeIs('ai.hub'),
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-robot" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><path d="M8 4h8a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-8a2 2 0 0 1-2-2v-6a2 2 0 0 1 2-2z" /><path d="M12 2v2" /><path d="M9 9v1" /><path d="M15 9v1" /><path d="M8 16h8" /><path d="M5 12v4a2 2 0 0 0 2 2h1" /><path d="M19 12v4a2 2 0 0 1-2 2h-1" /></svg>',
                    ],
                ];
                $avatarPath = Auth::user()?->avatar;
                $avatarUrl = $avatarPath ? asset('storage/' . $avatarPath) : null;
                $userInitial = Auth::user() ? strtoupper(substr(Auth::user()->name, 0, 1)) : 'U';
            @endphp

            <!-- 1. DESKTOP SIDEBAR -->
            <aside class="hidden lg:flex w-[260px] flex-col bg-[#0F172A] text-slate-200 px-6 py-8 flex-shrink-0">
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
                               class="relative flex items-center gap-3 px-3 py-2.5 rounded-lg border-l-[3px] transition duration-150 {{ $item['active'] ? 'bg-[rgba(99,102,241,0.15)] border-[#6366F1] text-white' : 'border-transparent text-slate-300 hover:bg-white/5' }}"
                               @if ($item['active']) aria-current="page" @endif>
                                <span class="text-indigo-200">{!! $item['icon'] !!}</span>
                                <span class="text-sm font-medium">{{ $item['label'] }}</span>
                            </a>
                    @endforeach
                </nav>

                <div class="mt-auto pt-6 text-xs text-slate-500">
                    CollabSphere v1.0
                </div>
            </aside>

            <!-- 2. MOBILE OFF-CANVAS DRAWER SIDEBAR -->
            <div x-show="mobileMenuOpen" class="relative z-50 lg:hidden" style="display: none;">
                <!-- Backdrop overlay -->
                <div x-show="mobileMenuOpen" 
                     x-transition:enter="transition-opacity ease-linear duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity ease-linear duration-300"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="mobileMenuOpen = false" 
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

                <!-- Drawer content container -->
                <div class="fixed inset-y-0 left-0 flex max-w-xs w-full bg-[#0F172A] p-6 text-slate-200 shadow-2xl">
                    <div x-show="mobileMenuOpen" 
                         x-transition:enter="transition ease-in-out duration-300 transform"
                         x-transition:enter-start="-translate-x-full"
                         x-transition:enter-end="translate-x-0"
                         x-transition:leave="transition ease-in-out duration-300 transform"
                         x-transition:leave-start="translate-x-0"
                         x-transition:leave-end="-translate-x-full"
                         class="flex flex-col w-full h-full">
                         
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-xl bg-indigo-500 flex items-center justify-center text-white font-semibold">CS</div>
                                <span class="text-base font-bold text-white">CollabSphere</span>
                            </div>
                            <button @click="mobileMenuOpen = false" type="button" class="h-8 w-8 text-slate-400 hover:text-white flex items-center justify-center rounded-lg hover:bg-white/5 transition">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <nav class="mt-10 space-y-2 flex-1">
                            @foreach ($navItems as $item)
                                    <a href="{{ $item['href'] }}"
                                       @click="mobileMenuOpen = false"
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg border-l-[3px] transition {{ $item['active'] ? 'bg-[rgba(99,102,241,0.15)] border-[#6366F1] text-white' : 'border-transparent text-slate-300 hover:bg-white/5' }}">
                                        <span class="text-indigo-200">{!! $item['icon'] !!}</span>
                                        <span class="text-sm font-semibold">{{ $item['label'] }}</span>
                                    </a>
                            @endforeach
                        </nav>

                        <div class="mt-auto text-xs text-slate-500 pt-6 border-t border-slate-800">
                            CollabSphere v1.0
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. MAIN WORKSPACE CONTAINER -->
            <div class="flex-1 flex flex-col min-h-screen min-w-0">
                
                <!-- TOP HEADER BAR -->
                <header class="h-16 bg-white border-b border-slate-200 px-6 flex-shrink-0">
                    <div class="h-full flex items-center justify-between gap-4">
                        
                        <!-- Left Block: Mobile Menu Hamburger & Search Command Button -->
                        <div class="flex items-center gap-3">
                            <button @click="mobileMenuOpen = true" type="button" class="lg:hidden h-10 w-10 border border-slate-200 rounded-xl flex items-center justify-center text-slate-500 hover:text-slate-700 transition">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>

                            <!-- Modern Clickable Search bar (Toggles Cmd+K modal) -->
                            <button type="button" 
                                    @click="window.dispatchEvent(new CustomEvent('open-search'))"
                                    class="hidden sm:flex items-center gap-3 h-10 border border-slate-200 rounded-xl px-3.5 bg-slate-50/50 hover:bg-slate-50 transition w-72 text-left text-xs font-semibold text-slate-400">
                                <span>🔍 Search projects, tasks, or teammates...</span>
                                <span class="ml-auto text-[9px] font-bold text-slate-400 bg-white border border-slate-200 px-1.5 py-0.5 rounded shadow-sm">Ctrl + K</span>
                            </button>
                        </div>

                        <!-- Right Block: Search icon (mobile), Notification Bell, User Account -->
                        <div class="flex items-center gap-4">
                            <!-- Mobile-only search button -->
                            <button type="button" @click="window.dispatchEvent(new CustomEvent('open-search'))" class="sm:hidden h-10 w-10 border border-slate-200 rounded-full flex items-center justify-center text-slate-500 hover:text-indigo-600 transition">
                                🔍
                            </button>

                            <!-- Dynamic Notification Dropdown Bell -->
                            <div x-data="notificationBell()" class="relative">
                                <button @click="openDropdown = !openDropdown" type="button" 
                                        class="h-10 w-10 rounded-full border border-slate-200 flex items-center justify-center text-slate-500 hover:text-indigo-600 hover:border-indigo-200 transition relative"
                                        :class="{ 'border-indigo-200 text-indigo-600': openDropdown }">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                                         :class="{ 'animate-bounce': bounce }">
                                        <path d="M15 18H9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                        <path d="M10 21H14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                        <path d="M6 9C6 5.68629 8.68629 3 12 3C15.3137 3 18 5.68629 18 9C18 12.121 19 13.5 20 14.5H4C5 13.5 6 12.121 6 9Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <span x-show="unreadCount > 0" x-text="unreadCount" 
                                          class="absolute -top-1 -right-1 bg-red-500 text-white text-[9px] font-extrabold rounded-full h-5 w-5 flex items-center justify-center border border-white"></span>
                                </button>

                                <!-- Dropdown Panel (360px wide) -->
                                <div x-show="openDropdown" @click.outside="openDropdown = false" x-transition
                                     class="absolute right-0 mt-3 w-[360px] bg-white border border-slate-200 rounded-2xl shadow-xl z-50 overflow-hidden"
                                     style="display: none;">
                                    <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                                        <h4 class="font-bold text-slate-800 text-sm">Notifications</h4>
                                        <button x-show="unreadCount > 0" @click="markAllRead()" class="text-xs text-indigo-600 font-semibold hover:text-indigo-800 transition">
                                            Mark all read
                                        </button>
                                    </div>
                                    <div class="max-h-[320px] overflow-y-auto divide-y divide-slate-100" style="scrollbar-width: thin;">
                                        <template x-for="item in list" :key="item.id">
                                            <div class="p-3.5 flex gap-3 items-start hover:bg-slate-50 transition relative border-l-4"
                                                 :class="{
                                                     'border-l-indigo-500': item.data.type === 'info',
                                                     'border-l-emerald-500': item.data.type === 'success',
                                                     'border-l-amber-500': item.data.type === 'warning',
                                                     'border-l-red-500': item.data.type === 'error',
                                                     'bg-slate-50/40': !item.read_at
                                                 }">
                                                <span class="text-lg mt-0.5" x-text="item.data.icon || '🔔'"></span>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs text-slate-700 leading-snug font-medium" x-text="item.data.message"></p>
                                                    <span class="text-[10px] text-slate-400 mt-1 block" x-text="item.time_ago"></span>
                                                </div>
                                                <span x-show="!item.read_at" class="h-2 w-2 bg-indigo-500 rounded-full flex-shrink-0 mt-2"></span>
                                            </div>
                                        </template>
                                        <div x-show="list.length === 0" class="py-12 text-center text-slate-400 text-xs">
                                            📭 No notifications yet
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- User Account Dropdown -->
                            <div x-data="{ open: false }" class="relative">
                                <button type="button" @click="open = !open" class="flex items-center gap-2.5 rounded-full border border-slate-200 p-1 hover:border-indigo-200 transition">
                                    @if ($avatarUrl)
                                        <img src="{{ $avatarUrl }}" alt="Avatar" class="h-8 w-8 rounded-full object-cover shadow-sm bg-white" />
                                    @else
                                        <div class="h-8 w-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-semibold shadow-sm">
                                            {{ $userInitial }}
                                        </div>
                                    @endif
                                    <span class="hidden md:block text-xs font-semibold text-slate-700 pr-2">{{ Auth::user()->name }}</span>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-slate-400 mr-1.5 hidden md:block">
                                        <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>

                                <div x-show="open" @click.outside="open = false" x-transition
                                     class="absolute right-0 mt-2 w-48 rounded-xl border border-slate-200 bg-white shadow-lg py-2 z-50"
                                     style="display: none;">
                                    <div class="px-4 py-2 border-b border-slate-100">
                                        <p class="text-xs font-bold text-slate-700 truncate">{{ Auth::user()->name }}</p>
                                        <p class="text-[10px] text-slate-400 truncate mt-0.5">{{ Auth::user()->email }}</p>
                                    </div>
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 font-semibold transition">Profile Settings</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-xs text-red-600 hover:bg-slate-50 font-bold transition">Log out</button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </header>

                <!-- MAIN SLOTTED BODY (W/ PAGE ENTER ANIMATIONS) -->
                <main class="flex-1 bg-[#F8FAFC] p-6 md:p-8 min-w-0 page-enter overflow-y-auto">
                    @isset($header)
                        <div class="mb-8">
                            {{ $header }}
                        </div>
                    @endisset
                    {{ $slot }}
                </main>
            </div>
        </div>

        <!-- Cmd+K Global Search Modal Overlay -->
        <div x-data="globalSearch()"
             @keydown.window="handleShortcut($event)"
             @open-search.window="openSearch = true; setTimeout(() => document.getElementById('search-input')?.focus(), 150)"
             x-show="openSearch"
             class="fixed inset-0 z-[10000] flex items-start justify-center pt-24 px-4 bg-slate-900/60 backdrop-blur-sm"
             style="display: none;"
             x-transition:enter="transition-opacity ease-out duration-250"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
             
             <!-- Modal Box -->
             <div @click.outside="close()" 
                  class="bg-white w-full max-w-[600px] rounded-3xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col scale-95"
                  x-show="openSearch"
                  x-transition:enter="transition ease-out duration-300 transform"
                  x-transition:enter-start="scale-95 translate-y-4"
                  x-transition:enter-end="scale-100 translate-y-0"
                  x-transition:leave="transition ease-in duration-200 transform"
                  x-transition:leave-start="scale-100 translate-y-0"
                  x-transition:leave-end="scale-95 translate-y-4">
                  
                  <!-- Input Header -->
                  <div class="relative p-5 border-b border-slate-100">
                      <span class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-400">
                          🔍
                      </span>
                      <input x-model="query" 
                             @input="debounceSearch()" 
                             @keydown.down.prevent="navigateDown()"
                             @keydown.up.prevent="navigateUp()"
                             @keydown.enter.prevent="selectActive()"
                             id="search-input"
                             type="text" 
                             class="w-full pl-10 pr-12 py-3 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 text-sm placeholder-slate-400 bg-slate-50"
                             placeholder="Search projects, tasks, or teammates (press Esc to close)">
                      <span class="absolute right-6 top-1/2 -translate-y-1/2 text-[9px] font-bold text-slate-400 bg-white border border-slate-200 px-2 py-1 rounded-md shadow-sm">
                          ESC
                      </span>
                  </div>
                  
                  <!-- Results Area -->
                  <div class="max-h-[380px] overflow-y-auto p-4 space-y-4" style="scrollbar-width: thin;">
                      <!-- Loading State -->
                      <div x-show="loading" class="space-y-3 py-4" style="display: none;">
                          <div class="skeleton h-6 w-1/3 rounded-lg"></div>
                          <div class="skeleton h-14 w-full rounded-2xl"></div>
                          <div class="skeleton h-14 w-full rounded-2xl"></div>
                      </div>
                      
                      <!-- Results Present State -->
                      <div x-show="!loading && Object.keys(results).length > 0" class="space-y-5" style="display: none;">
                          <template x-for="(groupItems, groupName) in results" :key="groupName">
                              <div>
                                  <h5 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2.5 px-2" x-text="groupName"></h5>
                                  <div class="space-y-1">
                                      <template x-for="item in groupItems" :key="item.id">
                                          <div @click="goTo(item.url)"
                                               @mouseenter="activeIndex = item.globalIndex"
                                               class="flex items-center justify-between p-3 rounded-xl cursor-pointer transition select-none"
                                               :class="activeIndex === item.globalIndex ? 'bg-indigo-50/70' : 'hover:bg-slate-50/70'">
                                               <div class="flex items-center gap-3 min-w-0">
                                                   <span class="text-lg flex-shrink-0" x-text="item.icon"></span>
                                                   <div class="min-w-0">
                                                       <span class="block text-sm font-bold text-slate-800" x-text="item.title"></span>
                                                       <span class="block text-[10px] text-slate-400 mt-0.5 truncate" x-text="item.subtitle"></span>
                                                   </div>
                                               </div>
                                               <span class="text-slate-400 transform transition-transform" :class="activeIndex === item.globalIndex ? 'translate-x-1' : ''">
                                                   ➔
                                               </span>
                                          </div>
                                      </template>
                                  </div>
                              </div>
                          </template>
                      </div>
                      
                      <!-- Empty Matches State -->
                      <div x-show="!loading && query.length >= 2 && Object.keys(results).length === 0" class="py-12 text-center text-slate-400" style="display: none;">
                          <p class="text-sm font-bold">🔍 No matches found</p>
                          <p class="text-xs text-slate-300 mt-1">Try searching for other keywords</p>
                      </div>

                      <!-- Placeholder State -->
                      <div x-show="query.length < 2 && !loading" class="py-12 text-center text-slate-400">
                          <p class="text-xs font-semibold">Type 2 or more characters to begin searching...</p>
                      </div>
                  </div>
             </div>
        </div>

        <script>
            // Polling for direct messages in the background
            setInterval(async () => {
                try {
                    const response = await fetch('{{ route("api.messages.unreadCount") }}');
                    const data = await response.json();
                    const unreadBadge = document.querySelector('.unread-badge');
                    
                    if (unreadBadge) {
                        if (data.unread_count > 0) {
                            unreadBadge.textContent = data.unread_count;
                            unreadBadge.classList.remove('hidden');
                        } else {
                            unreadBadge.classList.add('hidden');
                        }
                    }
                } catch (error) {
                    console.error('Error polling unread messages:', error);
                }
            }, 30000);

            // Fetch initial unread count
            document.addEventListener('DOMContentLoaded', async () => {
                try {
                    const response = await fetch('{{ route("api.messages.unreadCount") }}');
                    const data = await response.json();
                    const unreadBadge = document.querySelector('.unread-badge');
                    
                    if (unreadBadge && data.unread_count > 0) {
                        unreadBadge.textContent = data.unread_count;
                        unreadBadge.classList.remove('hidden');
                    }
                } catch (error) {
                    console.error('Error loading unread count:', error);
                }
            });

            // Alpine: Notification Bell Drops Controller
            function notificationBell() {
                return {
                    openDropdown: false,
                    list: [],
                    unreadCount: 0,
                    bounce: false,
                    
                    init() {
                        this.fetchNotifications();
                        setInterval(() => this.fetchNotifications(), 30000);
                    },
                    
                    async fetchNotifications() {
                        try {
                            const res = await fetch('{{ route("api.notifications") }}');
                            if (!res.ok) return;
                            const data = await res.json();
                            
                            if (data.unread_count > this.unreadCount) {
                                this.bounce = true;
                                setTimeout(() => this.bounce = false, 1200);
                                
                                const newNotifs = data.notifications.slice(0, data.unread_count - this.unreadCount);
                                newNotifs.forEach(n => {
                                    window.dispatchEvent(new CustomEvent('toast', {
                                        detail: { message: n.data.message, type: n.data.type || 'info' }
                                    }));
                                });
                            }
                            
                            this.list = data.notifications;
                            this.unreadCount = data.unread_count;
                        } catch (e) {
                            console.error('Error fetching notifications:', e);
                        }
                    },
                    
                    async markAllRead() {
                        try {
                            const res = await fetch('{{ route("api.notifications.markAllRead") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json'
                                }
                            });
                            const data = await res.json();
                            if (data.success) {
                                this.unreadCount = 0;
                                this.list.forEach(n => n.read_at = new Date().toISOString());
                                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'All notifications marked as read', type: 'success' } }));
                            }
                        } catch (e) {
                            console.error('Error marking read:', e);
                        }
                    }
                }
            }

            // Alpine: Global Search Overlay Controller
            function globalSearch() {
                return {
                    openSearch: false,
                    query: '',
                    loading: false,
                    results: {},
                    flatItems: [],
                    activeIndex: 0,
                    timer: null,
                    
                    handleShortcut(e) {
                        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                            e.preventDefault();
                            this.openSearch = true;
                            this.query = '';
                            this.results = {};
                            this.flatItems = [];
                            this.activeIndex = 0;
                            setTimeout(() => document.getElementById('search-input')?.focus(), 150);
                        }
                        if (e.key === 'Escape') {
                            this.close();
                        }
                    },
                    
                    close() {
                        this.openSearch = false;
                    },
                    
                    debounceSearch() {
                        clearTimeout(this.timer);
                        if (this.query.length < 2) {
                            this.results = {};
                            this.flatItems = [];
                            this.activeIndex = 0;
                            return;
                        }
                        
                        this.loading = true;
                        this.timer = setTimeout(() => this.performSearch(), 300);
                    },
                    
                    async performSearch() {
                        try {
                            const res = await fetch(`/api/search?q=${encodeURIComponent(this.query)}`);
                            const data = await res.json();
                            
                            this.results = data.results || {};
                            
                            this.flatItems = [];
                            let globalIndex = 0;
                            
                            Object.keys(this.results).forEach(group => {
                                this.results[group].forEach(item => {
                                    item.globalIndex = globalIndex;
                                    this.flatItems.push(item);
                                    globalIndex++;
                                });
                            });
                            
                            this.activeIndex = 0;
                        } catch (e) {
                            console.error('Search error:', e);
                        } finally {
                            this.loading = false;
                        }
                    },
                    
                    navigateDown() {
                        if (this.flatItems.length > 0) {
                            this.activeIndex = (this.activeIndex + 1) % this.flatItems.length;
                        }
                    },
                    
                    navigateUp() {
                        if (this.flatItems.length > 0) {
                            this.activeIndex = (this.activeIndex - 1 + this.flatItems.length) % this.flatItems.length;
                        }
                    },
                    
                    selectActive() {
                        if (this.flatItems[this.activeIndex]) {
                            this.goTo(this.flatItems[this.activeIndex].url);
                        }
                    },
                    
                    goTo(url) {
                        this.close();
                        window.location.href = url;
                    }
                }
            }
        </script>
    </body>
</html>
