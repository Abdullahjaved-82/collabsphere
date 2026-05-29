<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Analytics Dashboard</h2>
                <p class="mt-1 text-sm text-slate-500">Real-time team progress and operational velocity</p>
            </div>
            
            <!-- Team Switcher Dropdown -->
            @if ($teams->isNotEmpty() && $activeTeam)
                <div class="flex items-center gap-2 bg-white px-4 py-2.5 rounded-xl border border-slate-200 shadow-sm">
                    <span class="text-xs font-semibold text-slate-400 uppercase">Active Team:</span>
                    <select id="dashboardTeamSwitcher" onchange="switchTeam(this.value)" class="text-sm font-bold text-slate-800 border-none p-0 focus:ring-0 cursor-pointer bg-transparent">
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}" {{ $activeTeam->id == $team->id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        @if (!$activeTeam)
            <!-- Empty state for users without a team -->
            <div class="text-center py-20 bg-white rounded-2xl border border-slate-200 shadow-sm max-w-xl mx-auto px-6">
                <div class="h-16 w-16 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-6">👥</div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Welcome to CollabSphere!</h3>
                <p class="text-sm text-slate-500 mb-8 leading-relaxed">To view the dashboard metrics, tasks status velocity, and charts, you need to belong to a team first.</p>
                <div class="flex justify-center gap-4">
                    <a href="{{ route('teams.index') }}" class="px-5 py-3 bg-gradient-to-r from-indigo-600 to-violet-600 text-white rounded-xl font-semibold shadow-md shadow-indigo-200 hover:opacity-90 transition">
                        👥 Create or Join Team
                    </a>
                </div>
            </div>
        @else
            <!-- Dashboard Main Container -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                {{-- ═══════════ LEFT PANEL: Stats & Charts (col-span-9) ═══════════ --}}
                <div class="lg:col-span-9 space-y-8">
                    
                    <!-- 1. TOP ROW: 4 Stat Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        
                        <!-- Card 1: Total Tasks -->
                        <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4 shadow-sm relative overflow-hidden group">
                            <div class="h-12 w-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xl flex-shrink-0 group-hover:scale-110 transition duration-300">
                                📋
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Tasks</p>
                                <h3 class="text-3xl font-extrabold text-slate-800 mt-1 stat-count" data-target="{{ $stats['total_tasks'] }}" data-is-percentage="false">0</h3>
                                <div class="flex items-center gap-1 mt-1 text-[11px] font-semibold text-emerald-600">
                                    <span>↑</span>
                                    <span>{{ $stats['created_trend'] }}%</span>
                                    <span class="text-slate-400 font-normal">vs last week</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: Completed % -->
                        <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4 shadow-sm relative overflow-hidden group">
                            <div class="h-12 w-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl flex-shrink-0 group-hover:scale-110 transition duration-300">
                                ✅
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Completed</p>
                                <h3 class="text-3xl font-extrabold text-slate-800 mt-1 stat-count" data-target="{{ $stats['completed_percentage'] }}" data-is-percentage="true">0%</h3>
                                <div class="flex items-center gap-1 mt-1 text-[11px] font-semibold {{ $stats['completed_trend'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                    <span>{{ $stats['completed_trend'] >= 0 ? '↑' : '↓' }}</span>
                                    <span>{{ abs($stats['completed_trend']) }}%</span>
                                    <span class="text-slate-400 font-normal">completion rate</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card 3: In Progress Count -->
                        <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4 shadow-sm relative overflow-hidden group">
                            <div class="h-12 w-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl flex-shrink-0 group-hover:scale-110 transition duration-300">
                                ⏱
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">In Progress</p>
                                <h3 class="text-3xl font-extrabold text-slate-800 mt-1 stat-count" data-target="{{ $stats['in_progress_count'] }}" data-is-percentage="false">0</h3>
                                <div class="flex items-center gap-1 mt-1 text-[11px] font-semibold text-slate-400">
                                    <span>⚙️</span>
                                    <span class="text-indigo-600">Active development</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card 4: Overdue Count -->
                        <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4 shadow-sm relative overflow-hidden group">
                            <div class="h-12 w-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-xl flex-shrink-0 group-hover:scale-110 transition duration-300">
                                ⚠️
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Overdue</p>
                                <h3 class="text-3xl font-extrabold text-slate-800 mt-1 stat-count {{ $stats['overdue_count'] > 0 ? 'text-red-600' : '' }}" data-target="{{ $stats['overdue_count'] }}" data-is-percentage="false">0</h3>
                                <div class="flex items-center gap-1 mt-1 text-[11px] font-semibold text-slate-400">
                                    @if ($stats['overdue_count'] > 0)
                                        <span class="text-red-500 font-bold">Needs attention</span>
                                    @else
                                        <span class="text-emerald-500">All caught up</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- 2. CHARTS SECTION (Velocity & Workload Row) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        <!-- Task Velocity Card -->
                        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm animate-on-scroll" data-chart-id="velocityChart">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h4 class="font-bold text-slate-800">Task Velocity</h4>
                                    <p class="text-xs text-slate-400">Tasks completed per day (last 14 days)</p>
                                </div>
                                <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-bold">Line Chart</span>
                            </div>
                            <div class="relative h-60 w-full">
                                <canvas id="velocityChart"></canvas>
                            </div>
                        </div>

                        <!-- Member Workload Card -->
                        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm animate-on-scroll" data-chart-id="workloadChart">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h4 class="font-bold text-slate-800">Member Workload</h4>
                                    <p class="text-xs text-slate-400">Active tasks assignment balance</p>
                                </div>
                                <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-bold">Doughnut</span>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                                <div class="relative h-44 w-full">
                                    <canvas id="workloadChart"></canvas>
                                </div>
                                
                                <!-- Custom Workload Legend -->
                                <div class="flex flex-col gap-2.5 max-h-44 overflow-y-auto pr-1" id="workloadLegend">
                                    @foreach ($memberWorkload as $index => $work)
                                        <div class="flex items-center justify-between p-1.5 rounded-lg hover:bg-slate-50 transition duration-150">
                                            <div class="flex items-center gap-2">
                                                <img src="{{ $work['avatar'] }}" alt="{{ $work['name'] }}" class="h-6.5 w-6.5 rounded-full object-cover border border-slate-200">
                                                <span class="text-xs font-semibold text-slate-700 truncate" style="max-width: 85px;">{{ $work['name'] }}</span>
                                            </div>
                                            <span class="text-xs font-bold px-2 py-0.5 rounded {{ $work['task_count'] > 3 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }}">
                                                {{ $work['task_count'] }} tasks
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- 3. CHARTS SECTION (Project Progress Full Width Row) -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm animate-on-scroll" data-chart-id="progressChart">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="font-bold text-slate-800">Project Progress</h4>
                                <p class="text-xs text-slate-400">Task completion percentage across all active team projects</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-bold">Horizontal Bar</span>
                        </div>
                        <div class="relative h-56 w-full">
                            <canvas id="progressChart"></canvas>
                        </div>
                    </div>

                </div>

                {{-- ═══════════ RIGHT PANEL: Activity Feed Sidebar (col-span-3) ═══════════ --}}
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm sticky" style="top: 24px;">
                        <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <span>⚡</span> Recent Activity
                        </h4>
                        
                        @if ($recentActivities->isEmpty())
                            <div class="text-center py-10 text-slate-400">
                                <p class="text-sm">📭 No recent activity log</p>
                                <p class="text-[10px] text-slate-300 mt-1">Activities populate as tasks change</p>
                            </div>
                        @else
                            <div class="space-y-4 max-h-[580px] overflow-y-auto pr-1" style="scrollbar-width: thin;">
                                @foreach ($recentActivities as $activity)
                                    <div class="flex gap-3 items-start pb-3.5 border-b border-slate-100 last:border-b-0 last:pb-0">
                                        <img src="{{ $activity->user && $activity->user->avatar ? asset('storage/' . $activity->user->avatar) : 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($activity->user ? $activity->user->name : 'System') . '&size=32' }}" 
                                             alt="{{ $activity->user ? $activity->user->name : 'System' }}" 
                                             class="h-8 w-8 rounded-full object-cover flex-shrink-0 border border-slate-100 shadow-sm mt-0.5">
                                        
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-slate-600 leading-snug font-medium">
                                                {{ $activity->description }}
                                            </p>
                                            <span class="text-[10px] text-slate-400 block mt-1">
                                                {{ $activity->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        @endif
    </div>

    <!-- Inject Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Switch team via query params
        function switchTeam(teamId) {
            window.location.href = '?team_id=' + teamId;
        }

        @if ($activeTeam)
            // Load datasets dynamically from Laravel controller variables
            const velocityData = @json($taskVelocity);
            const progressData = @json($projectProgress);
            const workloadData = @json($memberWorkload);

            // Chart color scheme
            const colorIndigo = '#6366F1';
            const colorIndigoHover = '#4F46E5';
            const chartColors = ['#6366F1', '#8B5CF6', '#EC4899', '#F59E0B', '#10B981', '#3B82F6', '#64748B'];

            // Tooltip configurations
            const customTooltip = {
                enabled: true,
                backgroundColor: '#0F172A',
                titleColor: '#FFFFFF',
                bodyColor: '#E2E8F0',
                titleFont: { size: 12, weight: 'bold', family: 'sans-serif' },
                bodyFont: { size: 12, family: 'sans-serif' },
                padding: 12,
                borderRadius: 8,
                boxPadding: 6,
                usePointStyle: true,
            };

            // Global Chart initialized tracker
            const initializedCharts = {
                velocityChart: false,
                workloadChart: false,
                progressChart: false
            };

            function initChart(chartId) {
                if (chartId === 'velocityChart') {
                    const ctx = document.getElementById('velocityChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: velocityData.map(d => d.label),
                            datasets: [{
                                label: 'Tasks Completed',
                                data: velocityData.map(d => d.count),
                                borderColor: colorIndigo,
                                backgroundColor: 'rgba(99, 102, 241, 0.06)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 3,
                                pointBackgroundColor: colorIndigo,
                                pointBorderColor: '#FFFFFF',
                                pointBorderWidth: 2,
                                pointHoverRadius: 6,
                                pointHoverBackgroundColor: colorIndigo,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: customTooltip
                            },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: { font: { size: 10 }, color: '#94A3B8' }
                                },
                                y: {
                                    grid: { display: false },
                                    ticks: { font: { size: 10 }, color: '#94A3B8', stepSize: 1 }
                                }
                            }
                        }
                    });
                } else if (chartId === 'workloadChart') {
                    const ctx = document.getElementById('workloadChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: workloadData.map(d => d.name),
                            datasets: [{
                                data: workloadData.map(d => d.task_count),
                                backgroundColor: chartColors.slice(0, workloadData.length),
                                borderColors: '#FFFFFF',
                                borderWidth: 2,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: customTooltip
                            },
                            cutout: '65%'
                        }
                    });
                } else if (chartId === 'progressChart') {
                    const ctx = document.getElementById('progressChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: progressData.map(d => d.name),
                            datasets: [{
                                data: progressData.map(d => d.progress),
                                backgroundColor: colorIndigo,
                                hoverBackgroundColor: colorIndigoHover,
                                borderRadius: 8,
                                borderSkipped: false,
                                barThickness: 20,
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    ...customTooltip,
                                    callbacks: {
                                        label: function(context) {
                                            return `Progress: ${context.raw}% (${progressData[context.dataIndex].total_tasks} tasks)`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: true, color: '#F1F5F9' },
                                    ticks: { font: { size: 10 }, color: '#94A3B8', max: 100 },
                                    max: 100
                                },
                                y: {
                                    grid: { display: false },
                                    ticks: { font: { size: 11, weight: 'bold' }, color: '#475569' }
                                }
                            }
                        }
                    });
                }
            }

            // IntersectionObserver - Butter-smooth Scroll Animate trigger
            document.addEventListener('DOMContentLoaded', () => {
                // 1. Count-up animation
                const animateCount = (el) => {
                    const target = parseFloat(el.getAttribute('data-target'));
                    const isPct = el.getAttribute('data-is-percentage') === 'true';
                    const duration = 1200;
                    const startTime = performance.now();

                    const updateCount = (now) => {
                        const elapsed = now - startTime;
                        const progress = Math.min(elapsed / duration, 1);
                        const ease = progress * (2 - progress); // easeOutQuad
                        const value = target * ease;
                        
                        el.textContent = Math.round(value) + (isPct ? '%' : '');

                        if (progress < 1) {
                            requestAnimationFrame(updateCount);
                        } else {
                            el.textContent = target + (isPct ? '%' : '');
                        }
                    };
                    requestAnimationFrame(updateCount);
                };

                document.querySelectorAll('.stat-count').forEach(animateCount);

                // 2. Animate Charts on Scroll
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('chart-visible');
                            const chartId = entry.target.getAttribute('data-chart-id');
                            if (chartId && !initializedCharts[chartId]) {
                                initChart(chartId);
                                initializedCharts[chartId] = true;
                            }
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.15 });

                document.querySelectorAll('.animate-on-scroll').forEach(el => observer.observe(el));
            });
        @endif
    </script>

    <style>
        /* Animate on Scroll Card Styles */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(32px);
            transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .chart-visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</x-app-layout>
