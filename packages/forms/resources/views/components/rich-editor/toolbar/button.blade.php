@props([
    'activeOptions' => [],
    'type' => null,
])

<button
    @if ($type)
        x-bind:class="{
            'text-primary-600 dark:text-primary-400 bg-gray-50 dark:bg-white/5':
                editorUpdatedAt && getEditor().isActive(@js($type), @js($activeOptions)),
        }"
    @endif
    {{
        $attributes
            ->merge([
                'type' => 'button',
            ], escape: false)
            ->class(['fi-fo-rich-editor-toolbar-btn flex h-8 min-w-[--spacing(8)] cursor-pointer items-center justify-center rounded-lg text-sm font-semibold text-gray-700 transition duration-75 hover:bg-gray-50 focus-visible:bg-gray-50 dark:text-gray-200 dark:hover:bg-white/5 dark:focus-visible:bg-white/5'])
    }}
>
    {{ $slot }}
</button>
