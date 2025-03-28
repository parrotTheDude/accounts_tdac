@props(['label', 'type'])

@php
    $colors = [
        // User types
        'master' => 'bg-purple-100 text-purple-700',
        'superadmin' => 'bg-red-100 text-red-700',
        'admin' => 'bg-yellow-100 text-yellow-700',
        'staff' => 'bg-green-100 text-green-700',
        'participant' => 'bg-blue-100 text-blue-700',
        'parent' => 'bg-pink-100 text-pink-700',
        'external' => 'bg-gray-100 text-gray-700',

        // Engagement types
        'engaged' => 'bg-green-100 text-green-700',
        'limited' => 'bg-yellow-100 text-yellow-700',
        'unengaged' => 'bg-red-100 text-red-700',
        'unknown' => 'bg-gray-200 text-gray-600',
    ];

    $class = $colors[$type] ?? $colors['unknown'];
@endphp

<span class="inline-block text-xs font-semibold px-2 py-1 rounded {{ $class }}">
    {{ $label }}
</span>