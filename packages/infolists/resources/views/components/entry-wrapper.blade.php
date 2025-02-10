@props([
    'alignment' => null,
    'entry' => null,
    'hasInlineLabel' => null,
    'label' => null,
    'labelPrefix' => null,
    'labelSrOnly' => null,
    'labelSuffix' => null,
])

@php
    use Filament\Support\Enums\Alignment;
    use Illuminate\View\ComponentAttributeBag;

    if ($entry) {
        $alignment ??= $entry->getAlignment();
        $hasInlineLabel ??= $entry->hasInlineLabel();
        $label ??= $entry->getLabel();
        $labelSrOnly ??= $entry->isLabelHidden();
    }

    if (! $alignment instanceof Alignment) {
        $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
    }

    $beforeLabelContainer = $entry?->getChildComponentContainer($entry::BEFORE_LABEL_CONTAINER)?->toHtmlString();
    $afterLabelContainer = $entry?->getChildComponentContainer($entry::AFTER_LABEL_CONTAINER)?->toHtmlString();
    $beforeContentContainer = $entry?->getChildComponentContainer($entry::BEFORE_CONTENT_CONTAINER)?->toHtmlString();
    $afterContentContainer = $entry?->getChildComponentContainer($entry::AFTER_CONTENT_CONTAINER)?->toHtmlString();
@endphp

<div
    {{
        $attributes
            ->merge($entry?->getExtraEntryWrapperAttributes() ?? [], escape: false)
            ->class([
                'fi-in-entry',
                'fi-in-entry-has-inline-label' => $hasInlineLabel,
            ])
    }}
>
    @if ($label && $labelSrOnly)
        <dt class="fi-in-entry-label fi-hidden">
            {{ $label }}
        </dt>
    @endif

    <div class="fi-in-entry-label-col">
        {{ $entry?->getChildComponentContainer($entry::ABOVE_LABEL_CONTAINER) }}

        @if (($label && (! $labelSrOnly)) || $labelPrefix || $labelSuffix || $beforeLabelContainer || $afterLabelContainer)
            <div
                @class([
                    'fi-in-entry-label-ctn',
                    ($label instanceof \Illuminate\View\ComponentSlot) ? $label->attributes->get('class') : null,
                ])
            >
                {{ $beforeLabelContainer }}

                @if ($label && (! $labelSrOnly))
                    <dt
                        {{
                            (
                                ($label instanceof \Illuminate\View\ComponentSlot)
                                ? $label->attributes
                                : (new ComponentAttributeBag)
                            )
                                ->class(['fi-in-entry-label'])
                        }}
                    >
                        {{ $labelPrefix }}

                        {{ $label }}

                        {{ $labelSuffix }}
                    </dt>
                @elseif ($labelPrefix)
                    {{ $labelPrefix }}
                @elseif ($labelSuffix)
                    {{ $labelSuffix }}
                @endif

                {{ $afterLabelContainer }}
            </div>
        @endif

        {{ $entry?->getChildComponentContainer($entry::BELOW_LABEL_CONTAINER) }}
    </div>

    <div class="fi-in-entry-content-col">
        {{ $entry?->getChildComponentContainer($entry::ABOVE_CONTENT_CONTAINER) }}

        <div class="fi-in-entry-content-ctn">
            {{ $beforeContentContainer }}

            <dd
                @class([
                    'fi-in-entry-content',
                    (($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : (is_string($alignment) ? $alignment : '')),
                ])
            >
                {{ $slot }}
            </dd>

            {{ $afterContentContainer }}
        </div>

        {{ $entry?->getChildComponentContainer($entry::BELOW_CONTENT_CONTAINER) }}
    </div>
</div>
