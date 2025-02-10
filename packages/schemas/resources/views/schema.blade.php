@php
    use Filament\Actions\Action;
    use Filament\Actions\ActionGroup;
    use Filament\Schemas\Components\Component;
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\Width;
    use Illuminate\Support\Js;
    use Illuminate\View\ComponentAttributeBag;

    $alignment = $getAlignment();
    $isInline = $isInline();
    $isRoot = $isRoot();

    $hasVisibleComponents = false;

    $componentsWithVisibility = array_map(
        function (Component | Action | ActionGroup $component) use (&$hasVisibleComponents): array {
            $isComponentVisible = $component->isVisible();

            if ($isComponentVisible) {
                $hasVisibleComponents = true;
            }

            return [$component, $isComponentVisible];
        },
        $getComponents(withHidden: true),
    );

    $isEmbeddedInParentComponent = $isEmbeddedInParentComponent();
    $parentComponent = $isEmbeddedInParentComponent
        ? $getParentComponent()
        : null;
    $statePath = $isEmbeddedInParentComponent
        ? $parentComponent->getContainer()->getStatePath()
        : $getStatePath();
@endphp

@if ((! $isDirectlyHidden()) && $hasVisibleComponents)
    <div
        {{
            $getExtraAttributeBag()
                ->when(
                    ! $isInline,
                    fn (ComponentAttributeBag $attributes) => $attributes->grid($getColumns()),
                )
                ->merge([
                    'wire:partial' => $shouldPartiallyRender() ? ('schema.' . $getKey()) : null,
                    'x-data' => $isRoot ? 'filamentSchema({ livewireId: ' . Js::from($this->getId()) . ' })' : null,
                    'x-on:form-validation-error.window' => $isRoot ? 'handleFormValidationError' : null,
                ], escape: false)
                ->class([
                    'fi-sc',
                    'flex grow flex-wrap items-center' => $isInline,
                    match ($alignment) {
                        Alignment::Start, Alignment::Left => 'justify-start',
                        Alignment::Center => 'justify-center',
                        Alignment::End, Alignment::Right => 'justify-end',
                        Alignment::Between, Alignment::Justify => 'justify-between',
                        default => $alignment,
                    },
                    ($isDense() ? 'gap-3' : 'gap-6') => $hasGap(),
                ])
        }}
    >
        @foreach ($componentsWithVisibility as [$schemaComponent, $isSchemaComponentVisible])
            @if (($schemaComponent instanceof Action) || ($schemaComponent instanceof ActionGroup))
                <div
                    @class([
                        'hidden' => ! $isSchemaComponentVisible,
                    ])
                >
                    @if ($isSchemaComponentVisible)
                        {{ $schemaComponent }}
                    @endif
                </div>
            @elseif (! $schemaComponent->isLiberatedFromContainerGrid())
                @php
                    /**
                     * Instead of only rendering the hidden components, we should
                     * render the `<div>` wrappers for all fields, regardless of
                     * if they are hidden or not. This is to solve Livewire DOM
                     * diffing issues.
                     *
                     * Additionally, any `<div>` elements that wrap hidden
                     * components need to have `class="hidden"`, so that they
                     * don't consume grid space.
                     */
                    $hiddenJs = $schemaComponent->getHiddenJs();
                    $visibleJs = $schemaComponent->getVisibleJs();
                @endphp

                <div
                    {{
                        (new ComponentAttributeBag)
                            ->when(
                                ! $isInline,
                                fn (ComponentAttributeBag $attributes) => $attributes->gridColumn($schemaComponent->getColumnSpan(), $schemaComponent->getColumnStart(), ! $isSchemaComponentVisible),
                            )
                            ->merge([
                                'wire:key' => $schemaComponent->getLivewireKey(),
                                ...(($pollingInterval = $schemaComponent->getPollingInterval()) ? ["wire:poll.{$pollingInterval}" => "partiallyRenderSchemaComponent('{$schemaComponent->getKey()}')"] : []),
                            ], escape: false)
                            ->class([
                                match ($maxWidth = $schemaComponent->getMaxWidth()) {
                                    Width::ExtraSmall, 'xs' => 'max-w-xs',
                                    Width::Small, 'sm' => 'max-w-sm',
                                    Width::Medium, 'md' => 'max-w-md',
                                    Width::Large, 'lg' => 'max-w-lg',
                                    Width::ExtraLarge, 'xl' => 'max-w-xl',
                                    Width::TwoExtraLarge, '2xl' => 'max-w-2xl',
                                    Width::ThreeExtraLarge, '3xl' => 'max-w-3xl',
                                    Width::FourExtraLarge, '4xl' => 'max-w-4xl',
                                    Width::FiveExtraLarge, '5xl' => 'max-w-5xl',
                                    Width::SixExtraLarge, '6xl' => 'max-w-6xl',
                                    Width::SevenExtraLarge, '7xl' => 'max-w-7xl',
                                    default => $maxWidth,
                                },
                            ])
                    }}
                >
                    @if ($isSchemaComponentVisible)
                        @php
                            $schemaComponentStatePath = $isEmbeddedInParentComponent
                                ? $parentComponent->getStatePath()
                                : $schemaComponent->getStatePath();
                        @endphp

                        <div
                            x-data="filamentSchemaComponent({
                                        path: @js($schemaComponentStatePath),
                                        containerPath: @js($statePath),
                                        isLive: @js($schemaComponent->isLive()),
                                        $wire,
                                    })"
                            @if ($afterStateUpdatedJs = $schemaComponent->getAfterStateUpdatedJs())
                                x-init="{{
                                    implode(';', array_map(
                                        fn (string $js): string => '$wire.watch(' . Js::from($schemaComponentStatePath) . ', ($state, $old) => eval(' . Js::from($js) . '))',
                                        $afterStateUpdatedJs,
                                    ))
                                }}"
                            @endif
                            @if (filled($xShow = match ([filled($hiddenJs), filled($visibleJs)]) {
                                     [true, true] => "(! {$hiddenJs}) && ({$visibleJs})",
                                     [true, false] => "! {$hiddenJs}",
                                     [false, true] => $visibleJs,
                                     default => null,
                                 }))
                                x-show="{{ $xShow }}"
                                x-cloak
                            @endif
                            @class([
                                'fi-grid-ctn' => $schemaComponent->isGridContainer(),
                            ])
                        >
                            {{ $schemaComponent }}
                        </div>
                    @endif
                </div>
            @elseif ($isSchemaComponentVisible)
                {{ $schemaComponent }}
            @endif
        @endforeach
    </div>
@endif
