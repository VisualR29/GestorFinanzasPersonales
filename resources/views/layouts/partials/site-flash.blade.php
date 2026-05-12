@if (session('status') || session('error'))
    <div
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4"
        x-data="{ show: true }"
        x-show="show"
        x-transition.opacity.duration.200ms
        @if (session('status') && ! session('error'))
            x-init="setTimeout(() => { show = false }, 6500)"
        @endif
    >
        @if (session('error'))
            <div class="rounded-md bg-red-50 p-4 text-sm text-red-800 border border-red-200 shadow-sm flex justify-between gap-4 items-start" role="alert">
                <p class="flex-1">{{ session('error') }}</p>
                <button type="button" class="shrink-0 rounded p-1 text-red-600 hover:bg-red-100" @click="show = false" aria-label="Cerrar">×</button>
            </div>
        @else
            <div class="rounded-md bg-green-50 p-4 text-sm text-green-800 border border-green-200 shadow-sm flex justify-between gap-4 items-start">
                <p class="flex-1">{{ session('status') }}</p>
                <button type="button" class="shrink-0 rounded p-1 text-green-700 hover:bg-green-100" @click="show = false" aria-label="Cerrar">×</button>
            </div>
        @endif
    </div>
@endif
