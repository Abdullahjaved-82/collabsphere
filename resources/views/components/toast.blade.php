<div x-data="toastManager()" 
     @toast.window="addToast($event.detail)"
     class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-3.5 max-w-sm w-full pointer-events-none px-4 sm:px-0">
    
    <template x-for="toast in queue" :key="toast.id">
        <div x-show="toast.visible" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-x-full opacity-0 scale-95"
             x-transition:enter-end="translate-x-0 opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0 opacity-100 scale-100"
             x-transition:leave-end="translate-x-8 opacity-0 scale-95"
             class="pointer-events-auto bg-white border-l-4 rounded-xl shadow-lg p-4 relative overflow-hidden flex flex-col gap-1 border border-slate-100/80 backdrop-blur-md"
             :class="{
                 'border-l-emerald-500': toast.type === 'success',
                 'border-l-red-500': toast.type === 'error',
                 'border-l-amber-500': toast.type === 'warning',
                 'border-l-indigo-500': toast.type === 'info'
             }">
            
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="text-lg flex-shrink-0" x-text="
                        toast.type === 'success' ? '✅' :
                        toast.type === 'error' ? '❌' :
                        toast.type === 'warning' ? '⚠️' : 'ℹ️'
                    "></span>
                    <span class="font-bold text-sm text-slate-800 leading-snug" x-text="toast.message"></span>
                </div>
                <button @click="dismiss(toast.id)" class="text-slate-400 hover:text-slate-600 transition flex-shrink-0 p-0.5 rounded-lg hover:bg-slate-100">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Progress Bar -->
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-slate-100">
                <div class="h-full transition-all duration-100 ease-linear"
                     :class="{
                         'bg-emerald-500': toast.type === 'success',
                         'bg-red-500': toast.type === 'error',
                         'bg-amber-500': toast.type === 'warning',
                         'bg-indigo-500': toast.type === 'info'
                     }"
                     :style="`width: ${toast.progress}%`"></div>
            </div>
        </div>
    </template>
</div>

<script>
function toastManager() {
    return {
        queue: [],
        addToast(detail) {
            const id = Date.now() + Math.random().toString(36).substr(2, 9);
            const message = typeof detail === 'string' ? detail : detail.message;
            const type = detail.type || 'success';
            
            const toast = {
                id,
                message,
                type,
                visible: true,
                progress: 100,
                interval: null
            };
            
            this.queue.push(toast);
            
            const duration = 4000;
            const step = 100;
            const decrement = (step / duration) * 100;
            
            toast.interval = setInterval(() => {
                toast.progress -= decrement;
                if (toast.progress <= 0) {
                    clearInterval(toast.interval);
                    this.dismiss(id);
                }
            }, step);
        },
        dismiss(id) {
            const toast = this.queue.find(t => t.id === id);
            if (toast) {
                toast.visible = false;
                clearInterval(toast.interval);
                setTimeout(() => {
                    this.queue = this.queue.filter(t => t.id !== id);
                }, 300);
            }
        }
    }
}
</script>
