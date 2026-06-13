<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Welcome to CollabSphere — Onboarding</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Scripts & CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/animations.css') }}">
    
    <style>
        body {
            background: linear-gradient(135deg, #0F172A 0%, #1E1B4B 50%, #311042 100%);
            min-height: 100vh;
            font-family: 'Figtree', sans-serif;
        }
        .wizard-card {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(12px);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .step-dot {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .step-dot.active {
            background-color: #6366F1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.25);
            transform: scale(1.1);
        }
        .step-dot.completed {
            background-color: #10B981;
        }
    </style>
</head>
<body class="flex items-center justify-center p-4">

    <!-- Toast Component -->
    <x-toast />

    <div x-data="onboardingWizard()" class="w-full max-w-2xl page-enter">
        
        <!-- Header Branding -->
        <div class="text-center mb-8">
            <div class="h-14 w-14 bg-indigo-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4 shadow-lg shadow-indigo-500/30">
                CS
            </div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Setup Your Workspace</h1>
            <p class="text-slate-400 mt-2 text-sm">Let's configure your profile and build your first team workspace</p>
        </div>

        <!-- Wizard Card Container -->
        <div class="wizard-card p-8 md:p-10">
            
            <!-- Progress Bar Tracker -->
            <div class="flex items-center justify-between mb-10 relative">
                <!-- Connecting Line -->
                <div class="absolute left-0 right-0 h-1 bg-slate-100 top-1/2 -translate-y-1/2 z-0">
                    <div class="h-full bg-indigo-500 transition-all duration-300" :style="`width: ${(currentStep - 1) * (teamAction === 'join' ? 100 : 50)}%`"></div>
                </div>

                <!-- Step 1 Dot -->
                <div class="flex flex-col items-center z-10 relative">
                    <div class="step-dot h-10 w-10 rounded-full flex items-center justify-center text-sm font-bold text-slate-500 bg-slate-100"
                         :class="{ 'active': currentStep === 1, 'completed': currentStep > 1, 'text-white bg-indigo-600': currentStep === 1, 'text-white bg-emerald-500': currentStep > 1 }">
                        <span x-show="currentStep <= 1">1</span>
                        <span x-show="currentStep > 1">✓</span>
                    </div>
                    <span class="text-[11px] font-bold mt-2 uppercase tracking-wide" :class="currentStep === 1 ? 'text-indigo-600' : 'text-slate-400'">Profile Info</span>
                </div>

                <!-- Step 2 Dot -->
                <div class="flex flex-col items-center z-10 relative">
                    <div class="step-dot h-10 w-10 rounded-full flex items-center justify-center text-sm font-bold text-slate-500 bg-slate-100"
                         :class="{ 'active': currentStep === 2, 'completed': currentStep > 2, 'text-white bg-indigo-600': currentStep === 2, 'text-white bg-emerald-500': currentStep > 2 }">
                        <span x-show="currentStep <= 2">2</span>
                        <span x-show="currentStep > 2">✓</span>
                    </div>
                    <span class="text-[11px] font-bold mt-2 uppercase tracking-wide" :class="currentStep === 2 ? 'text-indigo-600' : 'text-slate-400'">Build Workspace</span>
                </div>

                <!-- Step 3 Dot -->
                <div class="flex flex-col items-center z-10 relative">
                    <div class="step-dot h-10 w-10 rounded-full flex items-center justify-center text-sm font-bold text-slate-500 bg-slate-100"
                         :class="{ 'active': currentStep === 3, 'text-white bg-indigo-600': currentStep === 3 }">
                        <span>3</span>
                    </div>
                    <span class="text-[11px] font-bold mt-2 uppercase tracking-wide" :class="currentStep === 3 ? 'text-indigo-600' : 'text-slate-400'">First Project</span>
                </div>
            </div>

            <!-- STEP 1: Profile Bio and Avatar -->
            <div x-show="currentStep === 1" class="stagger-children">
                <h2 class="text-xl font-bold text-slate-800 mb-2">Create your professional profile</h2>
                <p class="text-slate-500 text-xs mb-8">Upload an avatar mockup and share a brief bio with your teammates.</p>

                <form @submit.prevent="submitStep1()" class="space-y-6">
                    <!-- Avatar Upload Row -->
                    <div class="flex flex-col sm:flex-row items-center gap-6 p-5 bg-slate-50 rounded-2xl border border-slate-200/60">
                        <div class="relative">
                            <img :src="avatarPreview || 'https://api.dicebear.com/7.x/avataaars/svg?seed={{ urlencode($user->name) }}&size=96'" 
                                 alt="Avatar Preview" 
                                 class="h-20 w-20 rounded-full object-cover border-2 border-indigo-500 shadow-sm bg-white">
                            <label class="absolute -bottom-1.5 -right-1.5 bg-indigo-600 text-white rounded-full p-1.5 cursor-pointer shadow hover:bg-indigo-700 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                <input type="file" @change="previewAvatar($event)" accept="image/*" class="hidden">
                            </label>
                        </div>
                        <div class="text-center sm:text-left">
                            <span class="text-sm font-bold text-slate-700 block">Teammate Portrait</span>
                            <span class="text-xs text-slate-400 mt-1 block">Upload a JPEG or PNG profile image (max 2MB)</span>
                        </div>
                    </div>

                    <!-- Bio Input -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">A short bio</label>
                        <textarea x-model="bio" rows="3" 
                                  class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 text-sm placeholder-slate-400 bg-white" 
                                  placeholder="E.g., Computer science senior, UI designer, cloud enthusiast..."></textarea>
                    </div>

                    <!-- Specialty / Client Field -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Specialty / Expertise</label>
                        <div class="relative">
                            <select x-model="specialtySelect" @change="if(specialtySelect !== 'custom') specialty = specialtySelect; showCustomSpecialty = (specialtySelect === 'custom')" required
                                    class="w-full h-12 px-4 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 text-sm bg-white appearance-none cursor-pointer">
                                <option value="">Select your specialty...</option>
                                <option value="Frontend Development">🎨 Frontend Development</option>
                                <option value="Backend Development">⚙️ Backend Development</option>
                                <option value="Full Stack Development">🔥 Full Stack Development</option>
                                <option value="UI/UX Design">🖌️ UI/UX Design</option>
                                <option value="Mobile Development">📱 Mobile Development</option>
                                <option value="Data Science">📊 Data Science</option>
                                <option value="Machine Learning">🤖 Machine Learning</option>
                                <option value="DevOps">🚀 DevOps</option>
                                <option value="Cloud Architecture">☁️ Cloud Architecture</option>
                                <option value="Cybersecurity">🔒 Cybersecurity</option>
                                <option value="Database Administration">🗃️ Database Administration</option>
                                <option value="Project Management">📋 Project Management</option>
                                <option value="QA / Testing">🧪 QA / Testing</option>
                                <option value="Technical Writing">📝 Technical Writing</option>
                                <option value="custom">✏️ Other (type your own)</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                        <div x-show="showCustomSpecialty" x-transition class="mt-3">
                            <input x-model="specialty" type="text" maxlength="255" :required="showCustomSpecialty"
                                   class="w-full h-12 px-4 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 text-sm placeholder-slate-400 bg-white" 
                                   placeholder="E.g., Game Development, Blockchain, AR/VR...">
                        </div>
                        <span class="text-[10px] text-slate-400 mt-1.5 block">This will be visible to your teammates and can be changed later in settings.</span>
                    </div>

                    <!-- Form Navigation -->
                    <div class="pt-4 flex justify-end">
                        <button type="submit" :disabled="loading" 
                                class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition flex items-center gap-1.5 shadow-md shadow-indigo-200">
                            <span x-show="!loading">Next Step</span>
                            <span x-show="loading" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full block"></span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- STEP 2: Create or Join Team -->
            <div x-show="currentStep === 2" class="stagger-children" style="display: none;">
                <h2 class="text-xl font-bold text-slate-800 mb-2">Build or Join a Team Workspace</h2>
                <p class="text-slate-500 text-xs mb-8">Workspaces hold all projects, tasks, and message boards in CollabSphere.</p>

                <!-- Double Tabs Selector -->
                <div class="flex gap-2 p-1 bg-slate-100 rounded-xl mb-6">
                    <button @click="teamAction = 'create'" 
                            class="flex-1 py-2.5 rounded-lg text-xs font-bold transition duration-200"
                            :class="teamAction === 'create' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-800'">
                        👥 Create a Team
                    </button>
                    <button @click="teamAction = 'join'" 
                            class="flex-1 py-2.5 rounded-lg text-xs font-bold transition duration-200"
                            :class="teamAction === 'join' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-800'">
                        🔑 Join a Team
                    </button>
                </div>

                <!-- Create Team Form -->
                <form @submit.prevent="submitStep2()" class="space-y-6">
                    <div x-show="teamAction === 'create'" class="space-y-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Team Name</label>
                            <input x-model="teamName" type="text" 
                                   class="w-full h-12 px-4 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 text-sm placeholder-slate-400 bg-white" 
                                   placeholder="E.g., Senior Design Capstone Team A">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Description (Optional)</label>
                            <textarea x-model="teamDescription" rows="3" 
                                      class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 text-sm placeholder-slate-400 bg-white" 
                                      placeholder="Explain the team scope or project assignment..."></textarea>
                        </div>
                    </div>

                    <!-- Join Team Form -->
                    <div x-show="teamAction === 'join'" class="space-y-5" style="display: none;">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Invite Code</label>
                            <input x-model="inviteCode" type="text" maxlength="8" 
                                   class="w-full h-12 px-4 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 text-sm font-mono tracking-widest text-center uppercase placeholder-slate-400 bg-white" 
                                   placeholder="A8B9CD2E">
                            <span class="text-[10px] text-slate-400 mt-2 block text-center">Ask your team leader for the 8-character workspace invite code.</span>
                        </div>
                    </div>

                    <!-- Form Navigation -->
                    <div class="pt-4 flex justify-between">
                        <button type="button" @click="currentStep = 1" 
                                class="px-5 py-3 hover:bg-slate-50 text-slate-600 font-bold rounded-xl text-sm transition">
                            Back
                        </button>
                        <button type="submit" :disabled="loading" 
                                class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition flex items-center gap-1.5 shadow-md shadow-indigo-200">
                            <span x-show="!loading">Next Step</span>
                            <span x-show="loading" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full block"></span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- STEP 3: Create First Project -->
            <div x-show="currentStep === 3" class="stagger-children" style="display: none;">
                <h2 class="text-xl font-bold text-slate-800 mb-2">Launch your first Project</h2>
                <p class="text-slate-500 text-xs mb-8">Projects group tasks, track metrics, and run AI assistant suggestion tools.</p>

                <form @submit.prevent="submitStep3()" class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Project Title</label>
                        <input x-model="projectTitle" type="text" 
                               class="w-full h-12 px-4 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 text-sm placeholder-slate-400 bg-white" 
                               placeholder="E.g., Software Engineering Capstone Phase 1">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Description (Optional)</label>
                        <textarea x-model="projectDescription" rows="3" 
                                  class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 text-sm placeholder-slate-400 bg-white" 
                                  placeholder="Describe project requirements or key goals..."></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Target Deadline</label>
                        <input x-model="projectDeadline" type="date" 
                               class="w-full h-12 px-4 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 text-sm placeholder-slate-400 bg-white">
                    </div>

                    <!-- Form Navigation -->
                    <div class="pt-4 flex justify-between">
                        <button type="button" @click="currentStep = 2" 
                                class="px-5 py-3 hover:bg-slate-50 text-slate-600 font-bold rounded-xl text-sm transition">
                            Back
                        </button>
                        <button type="submit" :disabled="loading" 
                                class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold rounded-xl text-sm transition flex items-center gap-1.5 shadow-md shadow-emerald-200">
                            <span x-show="!loading">Finish Setup 🚀</span>
                            <span x-show="loading" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full block"></span>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        function onboardingWizard() {
            return {
                currentStep: 1,
                loading: false,
                bio: '{{ addslashes($user->bio) }}',
                specialty: '{{ addslashes($user->specialty ?? "") }}',
                specialtySelect: '{{ addslashes($user->specialty ?? "") }}',
                showCustomSpecialty: false,
                avatarFile: null,
                avatarPreview: '{{ $user->avatar ? asset("storage/".$user->avatar) : "" }}',
                
                teamAction: 'create',
                teamName: '',
                teamDescription: '',
                inviteCode: '',
                createdTeamId: null,

                projectTitle: '',
                projectDescription: '',
                projectDeadline: '',

                previewAvatar(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.avatarFile = file;
                        this.avatarPreview = URL.createObjectURL(file);
                    }
                },

                submitStep1() {
                    this.loading = true;
                    const formData = new FormData();
                    formData.append('bio', this.bio);
                    formData.append('specialty', this.specialty);
                    if (this.avatarFile) {
                        formData.append('avatar', this.avatarFile);
                    }

                    fetch('{{ route("onboarding.step1") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.loading = false;
                        if (data.success) {
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
                            this.currentStep = 2;
                        } else {
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message || 'Error occurred', type: 'error' } }));
                        }
                    })
                    .catch(err => {
                        this.loading = false;
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Network connection error', type: 'error' } }));
                    });
                },

                submitStep2() {
                    if (this.teamAction === 'create' && !this.teamName.trim()) {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Please provide a team name', type: 'warning' } }));
                        return;
                    }
                    if (this.teamAction === 'join' && !this.inviteCode.trim()) {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Please provide an invite code', type: 'warning' } }));
                        return;
                    }

                    this.loading = true;
                    fetch('{{ route("onboarding.step2") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            action: this.teamAction,
                            name: this.teamName,
                            description: this.teamDescription,
                            invite_code: this.inviteCode
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.loading = false;
                        if (data.success) {
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
                            this.createdTeamId = data.team.id;
                            
                            // If user JOINED a team, skip project creation (admin already did it)
                            if (this.teamAction === 'join') {
                                this.finishOnboarding();
                            } else {
                                this.currentStep = 3;
                            }
                        } else {
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message || 'Action failed', type: 'error' } }));
                        }
                    })
                    .catch(err => {
                        this.loading = false;
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Workspace error occurred', type: 'error' } }));
                    });
                },

                submitStep3() {
                    if (!this.projectTitle.trim()) {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Please provide a project title', type: 'warning' } }));
                        return;
                    }

                    this.loading = true;
                    fetch('{{ route("onboarding.step3") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            team_id: this.createdTeamId,
                            title: this.projectTitle,
                            description: this.projectDescription,
                            deadline: this.projectDeadline
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
                            this.finishOnboarding();
                        } else {
                            this.loading = false;
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message || 'Project creation failed', type: 'error' } }));
                        }
                    })
                    .catch(err => {
                        this.loading = false;
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Project error occurred', type: 'error' } }));
                    });
                },

                finishOnboarding() {
                    fetch('{{ route("onboarding.complete") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = data.redirect;
                        }
                    });
                }
            }
        }
    </script>
</body>
</html>
