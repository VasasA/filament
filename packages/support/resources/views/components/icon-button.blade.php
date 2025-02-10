@php
    use Filament\Support\Enums\ActionSize;
    use Filament\Support\Enums\IconSize;
    use Filament\Support\View\Components\Badge;
    use Filament\Support\View\Components\IconButton;
    use Illuminate\View\ComponentAttributeBag;
@endphp

@props([
    'badge' => null,
    'badgeColor' => 'primary',
    'badgeSize' => 'xs',
    'color' => 'primary',
    'disabled' => false,
    'form' => null,
    'formId' => null,
    'href' => null,
    'icon' => null,
    'iconAlias' => null,
    'iconSize' => null,
    'keyBindings' => null,
    'label' => null,
    'loadingIndicator' => true,
    'size' => ActionSize::Medium,
    'spaMode' => null,
    'tag' => 'button',
    'target' => null,
    'tooltip' => null,
    'type' => 'button',
])

@php
    if (! $size instanceof ActionSize) {
        $size = filled($size) ? (ActionSize::tryFrom($size) ?? $size) : null;
    }

    if (filled($iconSize) && (! $iconSize instanceof IconSize)) {
        $iconSize = IconSize::tryFrom($iconSize) ?? $iconSize;
    }

    $iconSize ??= match ($size) {
        ActionSize::ExtraSmall => IconSize::Small,
        ActionSize::Large, ActionSize::ExtraLarge => IconSize::Large,
        default => null,
    };

    if (! $badgeSize instanceof ActionSize) {
        $badgeSize = filled($badgeSize) ? (ActionSize::tryFrom($badgeSize) ?? $badgeSize) : null;
    }

    $wireTarget = $loadingIndicator ? $attributes->whereStartsWith(['wire:target', 'wire:click'])->filter(fn ($value): bool => filled($value))->first() : null;

    $hasLoadingIndicator = filled($wireTarget) || ($type === 'submit' && filled($form));

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget ?: $form, ENT_QUOTES);
    }

    $hasTooltip = filled($tooltip);
@endphp

<{{ $tag }}
    @if (($tag === 'a') && (! ($disabled && filled($tooltip))))
        {{ \Filament\Support\generate_href_html($href, $target === '_blank', $spaMode) }}
    @endif
    @if ($keyBindings || $hasTooltip)
        x-data="{}"
    @endif
    @if ($keyBindings)
        x-bind:id="$id('key-bindings')"
        x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}="document.getElementById($el.id).click()"
    @endif
    @if ($hasTooltip)
        x-tooltip="{
            content: @js($tooltip),
            theme: $store.theme,
        }"
    @endif
    {{
        $attributes
            ->merge([
                'aria-disabled' => $disabled ? 'true' : null,
                'aria-label' => $label,
                'disabled' => $disabled && blank($tooltip),
                'form' => $formId,
                'type' => $tag === 'button' ? $type : null,
                'wire:loading.attr' => $tag === 'button' ? 'disabled' : null,
                'wire:target' => ($hasLoadingIndicator && $loadingIndicatorTarget) ? $loadingIndicatorTarget : null,
            ], escape: false)
            ->merge([
                'title' => $hasTooltip ? null : $label,
            ], escape: true)
            ->when(
                $disabled && filled($tooltip),
                fn (ComponentAttributeBag $attributes) => $attributes->filter(
                    fn (mixed $value, string $key): bool => ! str($key)->startsWith(['href', 'x-on:', 'wire:click']),
                ),
            )
            ->class([
                'fi-icon-btn',
                'fi-disabled' => $disabled,
                ($size instanceof ActionSize) ? "fi-size-{$size->value}" : (is_string($size) ? $size : ''),
            ])
            ->color(IconButton::class, $color)
    }}
>
    {{
        \Filament\Support\generate_icon_html($icon, $iconAlias, (new \Illuminate\View\ComponentAttributeBag([
            'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => $hasLoadingIndicator,
            'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : false,
        ])), size: $iconSize)
    }}

    @if ($hasLoadingIndicator)
        {{
            \Filament\Support\generate_loading_indicator_html((new \Illuminate\View\ComponentAttributeBag([
                'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                'wire:target' => $loadingIndicatorTarget,
            ])), size: $iconSize)
        }}
    @endif

    @if (filled($badge))
        <div class="fi-icon-btn-badge-ctn">
            @if ($badge instanceof \Illuminate\View\ComponentSlot)
                {{ $badge }}
            @else
                <span
                    @class([
                        'fi-badge',
                        ...\Filament\Support\get_component_color_classes(Badge::class, $badgeColor),
                        ($badgeSize instanceof ActionSize) ? "fi-size-{$badgeSize->value}" : (is_string($badgeSize) ? $badgeSize : ''),
                    ])
                >
                    {{ $badge }}
                </span>
            @endif
        </div>
    @endif
</{{ $tag }}>
