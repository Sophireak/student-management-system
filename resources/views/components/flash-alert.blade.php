@if (session('success') || session('error') || session('warning') || session('info'))
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 3500)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-x-8"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-8"
        class="fixed top-5 right-5 z-[100] max-w-sm"
    >
        @if (session('success'))
            <div class="flex items-start gap-3 bg-white border border-green-200 shadow-lg rounded-xl px-4 py-3 min-w-72">
                <div class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-check text-green-600 text-lg"></i>
                </div>
                <div class="flex-1 pt-0.5">
                    <p class="text-sm font-semibold text-gray-800">Success</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-gray-300 hover:text-gray-500 transition-colors">
                    <i class="ti ti-x text-sm"></i>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-start gap-3 bg-white border border-red-200 shadow-lg rounded-xl px-4 py-3 min-w-72">
                <div class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-alert-circle text-red-600 text-lg"></i>
                </div>
                <div class="flex-1 pt-0.5">
                    <p class="text-sm font-semibold text-gray-800">Error</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ session('error') }}</p>
                </div>
                <button @click="show = false" class="text-gray-300 hover:text-gray-500 transition-colors">
                    <i class="ti ti-x text-sm"></i>
                </button>
            </div>
        @endif

        @if (session('warning'))
            <div class="flex items-start gap-3 bg-white border border-yellow-200 shadow-lg rounded-xl px-4 py-3 min-w-72">
                <div class="w-9 h-9 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-alert-triangle text-yellow-600 text-lg"></i>
                </div>
                <div class="flex-1 pt-0.5">
                    <p class="text-sm font-semibold text-gray-800">Warning</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ session('warning') }}</p>
                </div>
                <button @click="show = false" class="text-gray-300 hover:text-gray-500 transition-colors">
                    <i class="ti ti-x text-sm"></i>
                </button>
            </div>
        @endif

        @if (session('info'))
            <div class="flex items-start gap-3 bg-white border border-blue-200 shadow-lg rounded-xl px-4 py-3 min-w-72">
                <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-info-circle text-blue-600 text-lg"></i>
                </div>
                <div class="flex-1 pt-0.5">
                    <p class="text-sm font-semibold text-gray-800">Info</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ session('info') }}</p>
                </div>
                <button @click="show = false" class="text-gray-300 hover:text-gray-500 transition-colors">
                    <i class="ti ti-x text-sm"></i>
                </button>
            </div>
        @endif
    </div>
@endif