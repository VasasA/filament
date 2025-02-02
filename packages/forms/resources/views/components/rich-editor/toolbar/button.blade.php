<button
    {{
        $attributes
            ->merge([
                'type' => 'button',
            ], escape: false)
            ->class(['fi-fo-rich-editor-toolbar-btn [&.trix-active]:text-primary-600 dark:[&.trix-active]:text-primary-400 flex h-8 min-w-[--spacing(8)] cursor-pointer items-center justify-center rounded-lg text-sm font-semibold text-gray-700 transition duration-75 hover:bg-gray-50 focus-visible:bg-gray-50 dark:text-gray-200 dark:hover:bg-white/5 dark:focus-visible:bg-white/5 [&.trix-active]:bg-gray-50 dark:[&.trix-active]:bg-white/5'])
    }}
>
    {{ $slot }}
</button>
