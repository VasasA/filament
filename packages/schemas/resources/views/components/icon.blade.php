@php
    $color = $getColor();
    $tooltip = $getTooltip();
@endphp

<x-filament::icon
    :icon="$getIcon()"
    :x-tooltip="filled($tooltip) ? '{ content: ' . \Illuminate\Support\Js::from($tooltip) . ', theme: $store.theme }' : null"
    @class([
        'fi-sc-icon size-5',
        match ($color) {
            'gray' => 'text-gray-400 dark:text-gray-500',
            default => 'text-color-500 dark:text-color-400',
        },
    ])
    @style([
        \Filament\Support\get_color_css_variables(
            $color,
            shades: [400, 500],
            alias: 'schema::components.icon',
        ),
    ])
/>
