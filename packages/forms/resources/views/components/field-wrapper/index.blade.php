@php
    use Filament\Support\Enums\VerticalAlignment;
@endphp

@props([
    'field' => null,
    'hasInlineLabel' => null,
    'hasNestedRecursiveValidationRules' => null,
    'id' => null,
    'inlineLabelVerticalAlignment' => VerticalAlignment::Start,
    'isDisabled' => null,
    'label' => null,
    'labelPrefix' => null,
    'labelSrOnly' => null,
    'labelSuffix' => null,
    'required' => null,
    'statePath' => null,
])

@php
    if ($field) {
        $hasInlineLabel ??= $field->hasInlineLabel();
        $hasNestedRecursiveValidationRules ??= $field instanceof \Filament\Forms\Components\Contracts\HasNestedRecursiveValidationRules;
        $id ??= $field->getId();
        $isDisabled ??= $field->isDisabled();
        $label ??= $field->getLabel();
        $labelSrOnly ??= $field->isLabelHidden();
        $required ??= $field->isMarkedAsRequired();
        $statePath ??= $field->getStatePath();
    }

    $beforeLabelContainer = $field?->getChildComponentContainer($field::BEFORE_LABEL_CONTAINER)?->toHtmlString();
    $afterLabelContainer = $field?->getChildComponentContainer($field::AFTER_LABEL_CONTAINER)?->toHtmlString();
    $aboveContentContainer = $field?->getChildComponentContainer($field::ABOVE_CONTENT_CONTAINER)?->toHtmlString();
    $belowContentContainer = $field?->getChildComponentContainer($field::BELOW_CONTENT_CONTAINER)?->toHtmlString();
    $beforeContentContainer = $field?->getChildComponentContainer($field::BEFORE_CONTENT_CONTAINER)?->toHtmlString();
    $afterContentContainer = $field?->getChildComponentContainer($field::AFTER_CONTENT_CONTAINER)?->toHtmlString();
    $aboveErrorMessageContainer = $field?->getChildComponentContainer($field::ABOVE_ERROR_MESSAGE_CONTAINER)?->toHtmlString();
    $belowErrorMessageContainer = $field?->getChildComponentContainer($field::BELOW_ERROR_MESSAGE_CONTAINER)?->toHtmlString();

    $hasError = filled($statePath) && ($errors->has($statePath) || ($hasNestedRecursiveValidationRules && $errors->has("{$statePath}.*")));
@endphp

<div
    data-field-wrapper
    {{
        $attributes
            ->merge($field?->getExtraFieldWrapperAttributes() ?? [], escape: false)
            ->class(['fi-fo-field-wrp'])
    }}
>
    @if ($label && $labelSrOnly)
        <label for="{{ $id }}" class="sr-only">
            {{ $label }}
        </label>
    @endif

    <div
        @class([
            'grid gap-y-2',
            'sm:grid-cols-3 sm:gap-x-4' => $hasInlineLabel,
            match ($inlineLabelVerticalAlignment) {
                VerticalAlignment::Start => 'sm:items-start',
                VerticalAlignment::Center => 'sm:items-center',
                VerticalAlignment::End => 'sm:items-end',
            } => $hasInlineLabel,
        ])
    >
        {{ $field?->getChildComponentContainer($field::ABOVE_LABEL_CONTAINER) }}

        @if (($label && (! $labelSrOnly)) || $labelPrefix || $labelSuffix || $beforeLabelContainer || $afterLabelContainer)
            <div
                @class([
                    'flex items-center gap-x-3',
                    ($label instanceof \Illuminate\View\ComponentSlot) ? $label->attributes->get('class') : null,
                ])
            >
                {{ $beforeLabelContainer }}

                @if ($label && (! $labelSrOnly))
                    <x-filament-forms::field-wrapper.label
                        :for="$id"
                        :disabled="$isDisabled"
                        :prefix="$labelPrefix"
                        :required="$required"
                        :suffix="$labelSuffix"
                    >
                        {{ $label }}
                    </x-filament-forms::field-wrapper.label>
                @elseif ($labelPrefix)
                    {{ $labelPrefix }}
                @elseif ($labelSuffix)
                    {{ $labelSuffix }}
                @endif

                {{ $afterLabelContainer }}
            </div>
        @endif

        {{ $field?->getChildComponentContainer($field::BELOW_LABEL_CONTAINER) }}

        @if ((! \Filament\Support\is_slot_empty($slot)) || $hasError || $aboveContentContainer || $belowContentContainer || $beforeContentContainer || $afterContentContainer || $aboveErrorMessageContainer || $belowErrorMessageContainer)
            <div
                @class([
                    'grid auto-cols-fr gap-y-2',
                    'sm:col-span-2' => $hasInlineLabel,
                ])
            >
                {{ $aboveContentContainer }}

                @if ($beforeContentContainer || $afterContentContainer)
                    <div class="flex w-full items-center gap-x-3">
                        {{ $beforeContentContainer }}

                        <div class="w-full">
                            {{ $slot }}
                        </div>

                        {{ $afterContentContainer }}
                    </div>
                @else
                    {{ $slot }}
                @endif

                {{ $belowContentContainer }}

                {{ $aboveErrorMessageContainer }}

                @if ($hasError)
                    <x-filament-forms::field-wrapper.error-message>
                        {{ $errors->has($statePath) ? $errors->first($statePath) : ($hasNestedRecursiveValidationRules ? $errors->first("{$statePath}.*") : null) }}
                    </x-filament-forms::field-wrapper.error-message>
                @endif

                {{ $belowErrorMessageContainer }}
            </div>
        @endif
    </div>
</div>
