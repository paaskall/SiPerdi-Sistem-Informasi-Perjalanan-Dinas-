@props(['maxWidth' => '7xl'])

@php
$maxWidthClass = match ($maxWidth) {
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    '3xl' => 'max-w-3xl',
    '4xl' => 'max-w-4xl',
    '5xl' => 'max-w-5xl',
    '6xl' => 'max-w-6xl',
    '7xl' => 'max-w-7xl',
    default => 'max-w-7xl',
};
@endphp

<div class="py-8 sm:py-10">
    <div class="mx-auto {{ $maxWidthClass }} px-4 sm:px-6 lg:px-8">
        {{ $slot }}
    </div>
</div>