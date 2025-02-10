@php
    use Filament\Support\Enums\IconSize;
    use Filament\Support\View\Components\Dropdown\Header;
    use Illuminate\View\ComponentAttributeBag;
@endphp

@props([
    'color' => 'gray',
    'icon' => null,
    'iconSize' => null,
    'tag' => 'div',
])

@php
    if (! ($iconSize instanceof IconSize)) {
        $iconSize = filled($iconSize) ? (IconSize::tryFrom($iconSize) ?? $iconSize) : null;
    }
@endphp

<{{ $tag }}
    {{
        $attributes
            ->class([
                'fi-dropdown-header',
            ])
            ->color(Header::class, $color)
    }}
>
    {{ \Filament\Support\generate_icon_html($icon, size: $iconSize) }}

    <span>
        {{ $slot }}
    </span>
</{{ $tag }}>
