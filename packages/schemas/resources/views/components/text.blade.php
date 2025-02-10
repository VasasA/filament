@php
    use Filament\Support\Enums\FontFamily;
    use Filament\Support\Enums\FontWeight;
    use Filament\Support\RawJs;

    $color = $getColor();
    $content = $getContent();
    $size = $getSize();
    $weight = $getWeight();
    $fontFamily = $getFontFamily();

    $copyableState = $getCopyableState($content) ?? $content;
    $copyMessage = $getCopyMessage($copyableState);
    $copyMessageDuration = $getCopyMessageDuration($copyableState);
    $isCopyable = $isCopyable($copyableState);
@endphp

@if ($isBadge())
    <x-filament::badge
        :color="$color"
        :icon="$getIcon()"
        :icon-position="$getIconPosition()"
        :icon-size="$getIconSize()"
        :size="$size"
        :x-on:click="
            $isCopyable ? '
                window.navigator.clipboard.writeText(' . \Illuminate\Support\Js::from($copyableState) . ')
                $tooltip(' . \Illuminate\Support\Js::from($copyMessage) . ', {
                    theme: $store.theme,
                    timeout: ' . \Illuminate\Support\Js::from($copyMessageDuration) . ',
                })
            ' : null
        "
        :tag="$isCopyable ? 'button' : 'span'"
    >
        {{ $content }}
    </x-filament::badge>
@else
    <span
        @if ($isCopyable)
            x-on:click="
                window.navigator.clipboard.writeText(@js($copyableState))
                $tooltip(@js($copyMessage), {
                    theme: $store.theme,
                    timeout: @js($copyMessageDuration),
                })
            "
        @endif
        @class([
            'fi-sc-text break-words',
            'cursor-pointer' => $isCopyable,
            match ($color) {
                'gray' => 'text-gray-600 dark:text-gray-400',
                'neutral' => 'text-gray-950 dark:text-white',
                default => 'fi-color text-color-600 dark:text-color-400',
            },
            is_string($color) ? "fi-color-{$color}" : null,
            match ($size) {
                'xs' => 'text-xs',
                null => 'text-sm',
                default => ($size instanceof \Filament\Support\Enums\TextSize) ? $size->value : $size,
            },
            match ($weight) {
                FontWeight::Thin, 'thin' => 'font-thin',
                FontWeight::ExtraLight, 'extralight' => 'font-extralight',
                FontWeight::Light, 'light' => 'font-light',
                FontWeight::Medium, 'medium' => 'font-medium',
                FontWeight::SemiBold, 'semibold' => 'font-semibold',
                FontWeight::Bold, 'bold' => 'font-bold',
                FontWeight::ExtraBold, 'extrabold' => 'font-extrabold',
                FontWeight::Black, 'black' => 'font-black',
                default => $weight,
            },
            match ($fontFamily) {
                FontFamily::Sans, 'sans' => 'font-sans',
                FontFamily::Serif, 'serif' => 'font-serif',
                FontFamily::Mono, 'mono' => 'font-mono',
                default => $fontFamily,
            },
        ])
        @style([
            \Filament\Support\get_color_css_variables(
                $color,
                shades: [400, 600],
                alias: 'schema::components.text',
            ),
        ])
    >
        {{ $content }}
    </span>
@endif
