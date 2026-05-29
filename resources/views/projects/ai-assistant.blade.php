<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-semibold text-slate-900">AI Assistant</h2>
                <p class="mt-1 text-sm text-slate-500">Powered by Llama 3.1 — Intelligent task breakdown</p>
            </div>
            <a href="{{ route('projects.show', $project) }}" class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 font-medium hover:bg-slate-200 transition">
                ← Back to Project
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="flex gap-8" style="min-height: 600px;">

            {{-- ═══════════ LEFT PANEL — Project Context ═══════════ --}}
            <div style="width: 360px; flex-shrink: 0;">
                {{-- Project Info Card --}}
                <div style="background: linear-gradient(135deg, #1E1B4B, #312E81); border-radius: 16px; padding: 28px; color: white; margin-bottom: 20px; position: relative; overflow: hidden;">
                    {{-- Glow orb --}}
                    <div style="position: absolute; top: -30px; right: -30px; width: 120px; height: 120px; border-radius: 50%; background: radial-gradient(circle, rgba(139,92,246,0.4), transparent); pointer-events: none;"></div>

                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                        </div>
                        <span style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(255,255,255,0.6);">Project Context</span>
                    </div>

                    <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 8px;">{{ $project->title }}</h3>
                    @if ($project->description)
                        <p style="font-size: 13px; color: rgba(255,255,255,0.7); line-height: 1.6; margin-bottom: 16px;">{{ Str::limit($project->description, 150) }}</p>
                    @endif

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div style="background: rgba(255,255,255,0.08); border-radius: 10px; padding: 12px;">
                            <p style="font-size: 11px; color: rgba(255,255,255,0.5); margin-bottom: 4px;">Deadline</p>
                            <p style="font-size: 14px; font-weight: 600;">{{ $project->deadline ? $project->deadline->format('M d, Y') : 'Not set' }}</p>
                        </div>
                        <div style="background: rgba(255,255,255,0.08); border-radius: 10px; padding: 12px;">
                            <p style="font-size: 11px; color: rgba(255,255,255,0.5); margin-bottom: 4px;">Team Size</p>
                            <p style="font-size: 14px; font-weight: 600;">{{ $project->team ? $project->team->users->count() : 1 }} members</p>
                        </div>
                        <div style="background: rgba(255,255,255,0.08); border-radius: 10px; padding: 12px;">
                            <p style="font-size: 11px; color: rgba(255,255,255,0.5); margin-bottom: 4px;">Existing Tasks</p>
                            <p style="font-size: 14px; font-weight: 600;">{{ $project->tasks->count() }}</p>
                        </div>
                        <div style="background: rgba(255,255,255,0.08); border-radius: 10px; padding: 12px;">
                            <p style="font-size: 11px; color: rgba(255,255,255,0.5); margin-bottom: 4px;">Status</p>
                            <p style="font-size: 14px; font-weight: 600;">{{ ucfirst($project->status) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Generate Button --}}
                @if ($isTeamLeader)
                    <button id="generateBtn"
                        onclick="generateBreakdown()"
                        style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 10px; padding: 16px 24px; background: linear-gradient(135deg, #6366F1, #8B5CF6, #A855F7); color: white; font-weight: 700; font-size: 15px; border: none; border-radius: 14px; cursor: pointer; box-shadow: 0 8px 24px rgba(99,102,241,0.4); transition: all 0.3s ease; position: relative; overflow: hidden;"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 12px 32px rgba(99,102,241,0.5)';"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 24px rgba(99,102,241,0.4)';">
                        <span id="btnIcon" style="display: flex; align-items: center;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a4 4 0 0 1 4 4c0 1.95-1.4 3.57-3.24 3.92L12 22"/><path d="M8.5 8.5L3 6"/><path d="M15.5 8.5L21 6"/><path d="M12 2v2"/></svg>
                        </span>
                        <span id="btnText">Generate Task Breakdown</span>
                        {{-- Spinner overlay --}}
                        <div id="btnSpinner" style="display: none; position: absolute; inset: 0; background: linear-gradient(135deg, #6366F1, #8B5CF6); border-radius: 14px; align-items: center; justify-content: center;">
                            <div style="width: 28px; height: 28px; border: 3px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: aiSpin 0.8s linear infinite;"></div>
                        </div>
                    </button>
                @else
                    <div style="width: 100%; padding: 16px 24px; background: #F1F5F9; color: #94A3B8; font-weight: 600; font-size: 14px; border-radius: 14px; text-align: center;">
                        🔒 Only team leaders can use AI Assistant
                    </div>
                @endif

                {{-- Previous Generations --}}
                @if ($previousSuggestions->isNotEmpty())
                    <div style="margin-top: 20px;">
                        <p style="font-size: 12px; font-weight: 600; color: #64748B; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;">Previous Generations</p>
                        @foreach ($previousSuggestions as $prev)
                            <div style="padding: 10px 14px; border-radius: 10px; border: 1px solid #E2E8F0; margin-bottom: 8px; background: white;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 12px; color: #64748B;">{{ $prev->created_at->diffForHumans() }}</span>
                                    <span style="font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 6px; {{ $prev->status === 'accepted' ? 'background: #D1FAE5; color: #065F46;' : ($prev->status === 'rejected' ? 'background: #FEE2E2; color: #991B1B;' : 'background: #EEF2FF; color: #4338CA;') }}">
                                        {{ ucfirst($prev->status) }}
                                    </span>
                                </div>
                                @if (is_array($prev->response_json))
                                    <p style="font-size: 12px; color: #94A3B8; margin-top: 4px;">{{ count($prev->response_json) }} tasks generated</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ═══════════ RIGHT PANEL — AI Response ═══════════ --}}
            <div style="flex: 1; min-width: 0;">

                {{-- Initial Empty State --}}
                <div id="emptyState" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; text-align: center; padding: 40px;">
                    <div style="width: 80px; height: 80px; border-radius: 20px; background: linear-gradient(135deg, #EEF2FF, #E0E7FF); display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#6366F1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a4 4 0 0 1 4 4c0 1.95-1.4 3.57-3.24 3.92L12 22"/><path d="M8.5 8.5L3 6"/><path d="M15.5 8.5L21 6"/><path d="M12 2v2"/></svg>
                    </div>
                    <h3 style="font-size: 20px; font-weight: 700; color: #1E293B; margin-bottom: 8px;">AI-Powered Task Breakdown</h3>
                    <p style="font-size: 14px; color: #94A3B8; max-width: 400px; line-height: 1.6;">Click "Generate Task Breakdown" to have Llama 3.1 analyze your project and suggest actionable tasks for your team.</p>
                </div>

                {{-- Loading State --}}
                <div id="loadingState" style="display: none;">
                    <div style="text-align: center; margin-bottom: 24px;">
                        <div style="display: inline-flex; align-items: center; gap: 12px; padding: 12px 24px; background: linear-gradient(135deg, #EEF2FF, #E0E7FF); border-radius: 12px; border: 1px solid #C7D2FE;">
                            <div style="width: 20px; height: 20px; border: 2.5px solid #C7D2FE; border-top-color: #6366F1; border-radius: 50%; animation: aiSpin 0.8s linear infinite;"></div>
                            <span style="font-size: 14px; font-weight: 600; color: #4338CA;">Llama 3.1 is analyzing your project...</span>
                        </div>
                    </div>

                    {{-- Skeleton Cards --}}
                    @for ($i = 0; $i < 6; $i++)
                        <div style="background: white; border-radius: 14px; border: 1px solid #E2E8F0; padding: 20px; margin-bottom: 12px; animation: skeletonPulse 1.5s ease-in-out infinite; animation-delay: {{ $i * 0.15 }}s;">
                            <div style="display: flex; gap: 10px; margin-bottom: 14px;">
                                <div style="width: 70px; height: 22px; border-radius: 6px; background: linear-gradient(90deg, #EEF2FF, #E0E7FF, #EEF2FF); background-size: 200% 100%; animation: shimmer 1.5s ease-in-out infinite;"></div>
                                <div style="width: 55px; height: 22px; border-radius: 6px; background: linear-gradient(90deg, #F1F5F9, #E2E8F0, #F1F5F9); background-size: 200% 100%; animation: shimmer 1.5s ease-in-out infinite;"></div>
                            </div>
                            <div style="width: 75%; height: 16px; border-radius: 4px; background: linear-gradient(90deg, #F1F5F9, #E2E8F0, #F1F5F9); background-size: 200% 100%; animation: shimmer 1.5s ease-in-out infinite; margin-bottom: 8px;"></div>
                            <div style="width: 90%; height: 12px; border-radius: 4px; background: linear-gradient(90deg, #F8FAFC, #F1F5F9, #F8FAFC); background-size: 200% 100%; animation: shimmer 1.5s ease-in-out infinite;"></div>
                        </div>
                    @endfor
                </div>

                {{-- Results State --}}
                <div id="resultsState" style="display: none;">
                    {{-- Header with Accept All --}}
                    <div id="resultsHeader" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <div>
                            <h3 style="font-size: 18px; font-weight: 700; color: #1E293B;">Suggested Tasks</h3>
                            <p style="font-size: 13px; color: #94A3B8;" id="taskCountLabel">0 tasks generated</p>
                        </div>
                        <button id="acceptAllBtn" onclick="acceptAllTasks()"
                            style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 22px; background: linear-gradient(135deg, #059669, #10B981); color: white; font-weight: 600; font-size: 14px; border: none; border-radius: 10px; cursor: pointer; box-shadow: 0 4px 14px rgba(16,185,129,0.35); transition: all 0.2s ease;"
                            onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 20px rgba(16,185,129,0.45)';"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 14px rgba(16,185,129,0.35)';">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Accept All & Add to Kanban
                        </button>
                    </div>

                    {{-- Task Cards Container --}}
                    <div id="taskCardsContainer"></div>
                </div>

                {{-- Error State --}}
                <div id="errorState" style="display: none;">
                    <div style="text-align: center; padding: 40px;">
                        <div id="errorContent"></div>
                    </div>
                </div>

                {{-- Success State --}}
                <div id="successState" style="display: none;">
                    <div style="text-align: center; padding: 60px;">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #D1FAE5, #A7F3D0); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px; animation: successBounce 0.6s ease;">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <h3 style="font-size: 22px; font-weight: 700; color: #1E293B; margin-bottom: 8px;" id="successTitle">Tasks Created!</h3>
                        <p style="font-size: 14px; color: #64748B; margin-bottom: 24px;" id="successMessage">All tasks have been added to your Kanban board.</p>
                        <a href="{{ route('projects.kanban', $project) }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 28px; background: linear-gradient(135deg, #6366F1, #8B5CF6); color: white; font-weight: 600; font-size: 14px; border-radius: 10px; text-decoration: none; box-shadow: 0 4px 14px rgba(99,102,241,0.35); transition: all 0.2s ease;"
                            onmouseover="this.style.transform='translateY(-1px)';" onmouseout="this.style.transform='translateY(0)';">
                            📊 Open Kanban Board
                        </a>
                </div>

            </div>
        </div>
    </div>

    <!-- Edit Task Modal -->
    <div id="editTaskModal" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
        <div style="background: white; border-radius: 16px; width: 100%; max-width: 500px; padding: 28px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); border: 1px solid #E2E8F0; margin: 16px; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid #F1F5F9; padding-bottom: 12px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1E293B; display: flex; align-items: center; gap: 8px; margin: 0;">
                    ✏️ Customize AI Task Suggestion
                </h3>
                <button onclick="closeEditModal()" style="background: none; border: none; font-size: 20px; color: #94A3B8; cursor: pointer; font-weight: bold; padding: 4px;">✕</button>
            </div>

            <div style="display: flex; flex-direction: column; gap: 16px;">
                <input type="hidden" id="editTaskIndex">

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Task Title</label>
                    <input type="text" id="editTaskTitle" style="width: 100%; px: 14px; py: 10px; border-radius: 10px; border: 1.5px solid #E2E8F0; outline: none; font-size: 14px; box-sizing: border-box; padding: 10px 14px;" placeholder="e.g. Set up database schema">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Description</label>
                    <textarea id="editTaskDescription" rows="3" style="width: 100%; px: 14px; py: 10px; border-radius: 10px; border: 1.5px solid #E2E8F0; outline: none; font-size: 14px; resize: none; box-sizing: border-box; padding: 10px 14px;" placeholder="Write a short task description..."></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Priority</label>
                        <select id="editTaskPriority" style="width: 100%; px: 14px; py: 10px; border-radius: 10px; border: 1.5px solid #E2E8F0; outline: none; font-size: 14px; padding: 10px 14px;">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Category</label>
                        <select id="editTaskCategory" style="width: 100%; px: 14px; py: 10px; border-radius: 10px; border: 1.5px solid #E2E8F0; outline: none; font-size: 14px; padding: 10px 14px;">
                            <option value="frontend">Frontend</option>
                            <option value="backend">Backend</option>
                            <option value="design">Design</option>
                            <option value="research">Research</option>
                            <option value="testing">Testing</option>
                            <option value="documentation">Documentation</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Estimated Hours</label>
                        <input type="number" id="editTaskHours" min="0.5" step="0.5" style="width: 100%; px: 14px; py: 10px; border-radius: 10px; border: 1.5px solid #E2E8F0; outline: none; font-size: 14px; box-sizing: border-box; padding: 10px 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Due Date</label>
                        <input type="date" id="editTaskDueDate" style="width: 100%; px: 14px; py: 10px; border-radius: 10px; border: 1.5px solid #E2E8F0; outline: none; font-size: 14px; box-sizing: border-box; padding: 10px 14px;">
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Assign To</label>
                    <select id="editTaskAssignee" style="width: 100%; px: 14px; py: 10px; border-radius: 10px; border: 1.5px solid #E2E8F0; outline: none; font-size: 14px; padding: 10px 14px;">
                        <option value="">👤 Unassigned (Default)</option>
                        @foreach ($project->team ? $project->team->users : [] as $member)
                            <option value="{{ $member->id }}">👤 {{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 12px; padding-top: 20px; border-top: 1px solid #F1F5F9; margin-top: 24px;">
                <button onclick="closeEditModal()" style="flex: 1; padding: 12px 20px; border-radius: 10px; border: 1.5px solid #CBD5E1; background: #FFFFFF; color: #475569; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s ease;" onmouseover="this.style.background='#F8FAFC';" onmouseout="this.style.background='#FFFFFF';">
                    Cancel
                </button>
                <button onclick="saveTaskEdits()" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 20px; border-radius: 10px; border: none; background: linear-gradient(135deg, #6366F1, #8B5CF6); color: #FFFFFF; font-weight: 600; font-size: 14px; cursor: pointer; box-shadow: 0 4px 14px rgba(99,102,241,0.35); transition: all 0.2s ease;" onmouseover="this.style.transform='translateY(-1px)';" onmouseout="this.style.transform='translateY(0)';">
                    Save Changes
                </button>
            </div>
        </div>
    </div>

    <style>
        @keyframes aiSpin {
            to { transform: rotate(360deg); }
        }
        @keyframes skeletonPulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        @keyframes cardSlideIn {
            from { opacity: 0; transform: translateY(20px) scale(0.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes cardFlyOut {
            to { opacity: 0; transform: translateX(100vw) scale(0.8); }
        }
        @keyframes successBounce {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); opacity: 1; }
        }
        .ai-task-card {
            animation: cardSlideIn 0.4s ease forwards;
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .ai-task-card.accepted {
            animation: cardFlyOut 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }
        .ai-task-card.rejected {
            opacity: 0;
            transform: scale(0.9);
            max-height: 0;
            padding: 0;
            margin: 0;
            border: none;
            overflow: hidden;
            transition: all 0.4s ease;
        }
    </style>

    <script>
        const projectId = {{ $project->id }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const teamMembers = @json($project->team ? $project->team->users : []);
        let currentSuggestionId = null;
        let currentTasks = [];

        const categoryColors = {
            frontend: { bg: '#DBEAFE', text: '#1E40AF', label: 'Frontend' },
            backend: { bg: '#EDE9FE', text: '#5B21B6', label: 'Backend' },
            design: { bg: '#FCE7F3', text: '#9D174D', label: 'Design' },
            research: { bg: '#FEF3C7', text: '#92400E', label: 'Research' },
            testing: { bg: '#D1FAE5', text: '#065F46', label: 'Testing' },
            documentation: { bg: '#F1F5F9', text: '#334155', label: 'Docs' },
        };

        const priorityColors = {
            low: { bg: '#F1F5F9', text: '#475569' },
            medium: { bg: '#EEF2FF', text: '#4338CA' },
            high: { bg: '#FFF7ED', text: '#C2410C' },
            critical: { bg: '#FEF2F2', text: '#991B1B' },
        };

        function showState(state) {
            ['emptyState', 'loadingState', 'resultsState', 'errorState', 'successState'].forEach(id => {
                document.getElementById(id).style.display = 'none';
            });
            document.getElementById(state).style.display = state === 'emptyState' ? 'flex' : 'block';
        }

        async function generateBreakdown() {
            const btn = document.getElementById('generateBtn');
            const spinner = document.getElementById('btnSpinner');

            // Show loading
            spinner.style.display = 'flex';
            btn.style.pointerEvents = 'none';
            showState('loadingState');

            try {
                const response = await fetch(`/projects/${projectId}/ai/generate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                const data = await response.json();

                if (data.success) {
                    currentSuggestionId = data.suggestion_id;
                    currentTasks = data.tasks;
                    renderTaskCards(data.tasks);
                } else if (data.error === 'rate_limit') {
                    showRateLimitError(data.retry_after);
                } else if (data.error === 'parse_error') {
                    showParseError(data.raw_content);
                } else if (data.error === 'no_api_key') {
                    showApiKeyError();
                } else {
                    showGenericError(data.message || 'An unexpected error occurred.');
                }
            } catch (err) {
                showNetworkError();
            } finally {
                spinner.style.display = 'none';
                btn.style.pointerEvents = 'auto';
            }
        }

        function renderTaskCards(tasks) {
            showState('resultsState');
            document.getElementById('taskCountLabel').textContent = `${tasks.length} tasks generated`;
            const container = document.getElementById('taskCardsContainer');
            container.innerHTML = '';

            tasks.forEach((task, index) => {
                const cat = categoryColors[task.category] || categoryColors.backend;
                const pri = priorityColors[task.priority] || priorityColors.medium;

                // Check for custom assignee or deadline
                let assigneeName = '';
                if (task.assigned_to) {
                    const member = teamMembers.find(m => m.id === parseInt(task.assigned_to));
                    if (member) assigneeName = member.name;
                }

                let dateDisplay = '';
                if (task.due_date) {
                    const dateObj = new Date(task.due_date);
                    dateDisplay = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                }

                const card = document.createElement('div');
                card.className = 'ai-task-card';
                card.id = `task-card-${index}`;
                card.style.animationDelay = `${index * 80}ms`;
                card.style.background = 'white';
                card.style.borderRadius = '14px';
                card.style.border = '1px solid #E2E8F0';
                card.style.padding = '20px';
                card.style.marginBottom = '12px';

                card.innerHTML = `
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <span style="padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; background: ${cat.bg}; color: ${cat.text}; text-transform: uppercase; letter-spacing: 0.5px;">
                                ${cat.label}
                            </span>
                            <span style="padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; background: ${pri.bg}; color: ${pri.text};">
                                ${task.priority.charAt(0).toUpperCase() + task.priority.slice(1)}
                            </span>
                            <span style="padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #F0FDF4; color: #166534;">
                                ⏱ ${task.estimated_hours}h
                            </span>
                            ${dateDisplay ? `
                                <span style="padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #FFF7ED; color: #C2410C;">
                                    📅 Due: ${dateDisplay}
                                </span>
                            ` : ''}
                            ${assigneeName ? `
                                <span style="padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #EFF6FF; color: #1E40AF;">
                                    👤 Assigned to: ${assigneeName}
                                </span>
                            ` : ''}
                        </div>
                        <div style="display: flex; gap: 6px; flex-shrink: 0;">
                            <button onclick="openEditModal(${index})"
                                style="width: 34px; height: 34px; border-radius: 8px; border: 1.5px solid #E0E7FF; background: #EEF2FF; color: #4F46E5; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;"
                                onmouseover="this.style.background='#E0E7FF'; this.style.borderColor='#C7D2FE';"
                                onmouseout="this.style.background='#EEF2FF'; this.style.borderColor='#E0E7FF';"
                                title="Edit task details">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                            </button>
                            <button onclick="acceptSingleTask(${index})"
                                style="width: 34px; height: 34px; border-radius: 8px; border: 1.5px solid #D1FAE5; background: #F0FDF4; color: #059669; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;"
                                onmouseover="this.style.background='#D1FAE5'; this.style.borderColor='#6EE7B7';"
                                onmouseout="this.style.background='#F0FDF4'; this.style.borderColor='#D1FAE5';"
                                title="Accept task">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            </button>
                            <button onclick="rejectSingleTask(${index})"
                                style="width: 34px; height: 34px; border-radius: 8px; border: 1.5px solid #FEE2E2; background: #FEF2F2; color: #DC2626; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;"
                                onmouseover="this.style.background='#FEE2E2'; this.style.borderColor='#FCA5A5';"
                                onmouseout="this.style.background='#FEF2F2'; this.style.borderColor='#FEE2E2';"
                                title="Reject task">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                    </div>
                    <h4 style="font-size: 16px; font-weight: 600; color: #1E293B; margin-bottom: 6px;">${task.title}</h4>
                    <p style="font-size: 13px; color: #64748B; line-height: 1.5;">${task.description}</p>
                `;

                container.appendChild(card);
            });
        }

        function openEditModal(index) {
            const task = currentTasks[index];
            document.getElementById('editTaskIndex').value = index;
            document.getElementById('editTaskTitle').value = task.title;
            document.getElementById('editTaskDescription').value = task.description || '';
            document.getElementById('editTaskPriority').value = task.priority || 'medium';
            document.getElementById('editTaskCategory').value = task.category || 'backend';
            document.getElementById('editTaskHours').value = task.estimated_hours || 2;
            document.getElementById('editTaskDueDate').value = task.due_date || '';
            document.getElementById('editTaskAssignee').value = task.assigned_to || '';

            document.getElementById('editTaskModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editTaskModal').style.display = 'none';
        }

        function saveTaskEdits() {
            const index = parseInt(document.getElementById('editTaskIndex').value);
            const title = document.getElementById('editTaskTitle').value;
            const description = document.getElementById('editTaskDescription').value;
            const priority = document.getElementById('editTaskPriority').value;
            const category = document.getElementById('editTaskCategory').value;
            const hours = parseFloat(document.getElementById('editTaskHours').value) || 2;
            const dueDate = document.getElementById('editTaskDueDate').value;
            const assigneeId = document.getElementById('editTaskAssignee').value;

            if (!title.trim()) {
                alert('Task title is required.');
                return;
            }

            // Update memory array
            currentTasks[index].title = title;
            currentTasks[index].description = description;
            currentTasks[index].priority = priority;
            currentTasks[index].category = category;
            currentTasks[index].estimated_hours = hours;
            currentTasks[index].due_date = dueDate ? dueDate : null;
            currentTasks[index].assigned_to = assigneeId ? parseInt(assigneeId) : null;

            // Re-render tasks to reflect new styling and badges
            renderTaskCards(currentTasks);
            closeEditModal();
        }

        async function acceptAllTasks() {
            const btn = document.getElementById('acceptAllBtn');
            btn.innerHTML = '<div style="width: 18px; height: 18px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: aiSpin 0.8s linear infinite;"></div> Creating tasks...';
            btn.style.pointerEvents = 'none';

            const tasksToAccept = currentTasks.filter((_, i) => {
                const card = document.getElementById(`task-card-${i}`);
                return card && !card.classList.contains('rejected');
            });

            try {
                const response = await fetch(`/projects/${projectId}/ai/accept`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        suggestion_id: currentSuggestionId,
                        tasks: tasksToAccept,
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    // Animate all remaining cards flying out
                    document.querySelectorAll('.ai-task-card:not(.rejected)').forEach((card, i) => {
                        setTimeout(() => {
                            card.classList.add('accepted');
                        }, i * 60);
                    });

                    // Show success after animations
                    setTimeout(() => {
                        document.getElementById('successTitle').textContent = `${data.tasks_created} Tasks Created!`;
                        document.getElementById('successMessage').textContent = data.message;
                        showState('successState');
                    }, tasksToAccept.length * 60 + 600);
                } else {
                    alert(data.message || 'Failed to create tasks.');
                    btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Accept All & Add to Kanban';
                    btn.style.pointerEvents = 'auto';
                }
            } catch (err) {
                alert('Network error. Please try again.');
                btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Accept All & Add to Kanban';
                btn.style.pointerEvents = 'auto';
            }
        }

        function acceptSingleTask(index) {
            const card = document.getElementById(`task-card-${index}`);
            if (!card) return;

            // Create single task
            fetch(`/projects/${projectId}/ai/accept`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    suggestion_id: currentSuggestionId,
                    tasks: [currentTasks[index]],
                }),
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    card.classList.add('accepted');
                    updateRemainingCount();
                }
            });
        }

        function rejectSingleTask(index) {
            const card = document.getElementById(`task-card-${index}`);
            if (!card) return;
            card.classList.add('rejected');
            updateRemainingCount();
        }

        function updateRemainingCount() {
            const remaining = document.querySelectorAll('.ai-task-card:not(.rejected):not(.accepted)').length;
            document.getElementById('taskCountLabel').textContent = `${remaining} tasks remaining`;
            if (remaining === 0) {
                document.getElementById('resultsHeader').style.display = 'none';
            }
        }

        // ─── Error States ───

        function showRateLimitError(retryAfter) {
            let seconds = parseInt(retryAfter) || 30;
            showState('errorState');
            const el = document.getElementById('errorContent');
            el.innerHTML = `
                <div style="width: 64px; height: 64px; border-radius: 16px; background: linear-gradient(135deg, #FEF3C7, #FDE68A); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <h3 style="font-size: 18px; font-weight: 700; color: #1E293B; margin-bottom: 8px;">Rate Limit Reached</h3>
                <p style="font-size: 14px; color: #64748B; margin-bottom: 16px;">The AI service is temporarily busy. Retrying in <span id="retryCountdown" style="font-weight: 700; color: #D97706;">${seconds}</span> seconds...</p>
                <div style="width: 200px; height: 4px; border-radius: 2px; background: #F1F5F9; margin: 0 auto;">
                    <div id="retryProgress" style="height: 100%; border-radius: 2px; background: linear-gradient(90deg, #F59E0B, #D97706); transition: width 1s linear; width: 100%;"></div>
                </div>
            `;

            const countdown = setInterval(() => {
                seconds--;
                const countdownEl = document.getElementById('retryCountdown');
                const progressEl = document.getElementById('retryProgress');
                if (countdownEl) countdownEl.textContent = seconds;
                if (progressEl) progressEl.style.width = `${(seconds / parseInt(retryAfter)) * 100}%`;
                if (seconds <= 0) {
                    clearInterval(countdown);
                    generateBreakdown();
                }
            }, 1000);
        }

        function showNetworkError() {
            showState('errorState');
            document.getElementById('errorContent').innerHTML = `
                <div style="width: 64px; height: 64px; border-radius: 16px; background: linear-gradient(135deg, #FEE2E2, #FECACA); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                </div>
                <h3 style="font-size: 18px; font-weight: 700; color: #1E293B; margin-bottom: 8px;">Connection Failed</h3>
                <p style="font-size: 14px; color: #64748B; margin-bottom: 20px;">Could not reach the AI service. Check your internet connection.</p>
                <button onclick="generateBreakdown()"
                    style="padding: 10px 24px; background: linear-gradient(135deg, #6366F1, #8B5CF6); color: white; font-weight: 600; font-size: 14px; border: none; border-radius: 10px; cursor: pointer; box-shadow: 0 4px 14px rgba(99,102,241,0.35);">
                    🔄 Retry
                </button>
            `;
        }

        function showParseError(rawContent) {
            showState('errorState');
            document.getElementById('errorContent').innerHTML = `
                <div style="width: 64px; height: 64px; border-radius: 16px; background: linear-gradient(135deg, #FEF3C7, #FDE68A); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
                <h3 style="font-size: 18px; font-weight: 700; color: #1E293B; margin-bottom: 8px;">Could Not Parse Structured Response</h3>
                <p style="font-size: 14px; color: #64748B; margin-bottom: 16px;">The AI returned a response that couldn't be parsed as structured tasks.</p>
                <div style="text-align: left; max-width: 600px; margin: 0 auto; background: #F8FAFC; border-radius: 10px; border: 1px solid #E2E8F0; padding: 16px; max-height: 300px; overflow-y: auto;">
                    <pre style="font-size: 12px; color: #475569; white-space: pre-wrap; word-break: break-word; margin: 0; font-family: 'Fira Code', monospace;">${rawContent}</pre>
                </div>
                <button onclick="generateBreakdown()" style="margin-top: 16px; padding: 10px 24px; background: linear-gradient(135deg, #6366F1, #8B5CF6); color: white; font-weight: 600; font-size: 14px; border: none; border-radius: 10px; cursor: pointer;">
                    🔄 Try Again
                </button>
            `;
        }

        function showApiKeyError() {
            showState('errorState');
            document.getElementById('errorContent').innerHTML = `
                <div style="width: 64px; height: 64px; border-radius: 16px; background: linear-gradient(135deg, #FEE2E2, #FECACA); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <h3 style="font-size: 18px; font-weight: 700; color: #1E293B; margin-bottom: 8px;">API Key Not Configured</h3>
                <p style="font-size: 14px; color: #64748B; margin-bottom: 16px;">Please set your Groq API key in the <code style="background: #F1F5F9; padding: 2px 6px; border-radius: 4px;">.env</code> file.</p>
                <div style="text-align: left; max-width: 400px; margin: 0 auto; background: #F8FAFC; border-radius: 10px; border: 1px solid #E2E8F0; padding: 16px;">
                    <code style="font-size: 13px; color: #475569;">GROQ_API_KEY=your_key_here</code>
                </div>
            `;
        }

        function showGenericError(message) {
            showState('errorState');
            document.getElementById('errorContent').innerHTML = `
                <div style="width: 64px; height: 64px; border-radius: 16px; background: linear-gradient(135deg, #FEE2E2, #FECACA); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                </div>
                <h3 style="font-size: 18px; font-weight: 700; color: #1E293B; margin-bottom: 8px;">Something Went Wrong</h3>
                <p style="font-size: 14px; color: #64748B; margin-bottom: 20px;">${message}</p>
                <button onclick="generateBreakdown()" style="padding: 10px 24px; background: linear-gradient(135deg, #6366F1, #8B5CF6); color: white; font-weight: 600; font-size: 14px; border: none; border-radius: 10px; cursor: pointer;">
                    🔄 Retry
                </button>
            `;
        }
    </script>
</x-app-layout>
