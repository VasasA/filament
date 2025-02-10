@php
    use Filament\Support\Enums\Alignment;

    $alignment = $getAlignment();
    $height = $getImageHeight() ?? '8rem';
    $width = $getImageWidth();
    $tooltip = $getTooltip();

    if (! $alignment instanceof Alignment) {
        $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
    }
@endphp

<img
    alt="{{ $getAlt() }}"
    src="{{ $getUrl() }}"
    @if (filled($tooltip))
        x-tooltip="{ content: @js($tooltip), theme: $store.theme }"
    @endif
    @class([
        'fi-sc-icon rounded-lg border border-gray-300 dark:border-transparent',
        match ($alignment) {
            Alignment::Center => 'mx-auto',
            Alignment::End, Alignment::Right => 'ms-auto',
            Alignment::Start, Alignment::Left, Alignment::Between, Alignment::Justify => '',
            default => $alignment,
        },
    ])
    @style([
        "height: {$height}" => $height,
        "width: {$width}" => $width,
    ])
/>
