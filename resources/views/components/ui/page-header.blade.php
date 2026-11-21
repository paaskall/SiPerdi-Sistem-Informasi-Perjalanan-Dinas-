@props([
    'title',
    'desc' => null,
    'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    'color' => 'from-blue-900 to-blue-700',
])

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br {{ $color }} text-white shadow-lg shadow-blue-900/20">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $icon }}"/>
            </svg>
        </div>
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">{{ $title }}</h2>
            @if($desc)
                <p class="mt-0.5 text-sm text-slate-500">{{ $desc }}</p>
            @endif
        </div>
    </div>

    @isset($action)
        <div>{{ $action }}</div>
    @endisset
</div>