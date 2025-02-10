<?php

namespace Filament\Infolists\Components;

use Closure;
use Filament\Infolists\View\Components\TextEntry\Item;
use Filament\Infolists\View\Components\TextEntry\Item\Icon;
use Filament\Schemas\Components\Contracts\HasAffixActions;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\CanBeCopied;
use Filament\Support\Concerns\CanWrap;
use Filament\Support\Concerns\HasFontFamily;
use Filament\Support\Concerns\HasLineClamp;
use Filament\Support\Concerns\HasWeight;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\TextSize;
use Filament\Support\View\Components\Badge;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;

use function Filament\Support\generate_href_html;
use function Filament\Support\generate_icon_html;

class TextEntry extends Entry implements HasAffixActions, HasEmbeddedView
{
    use CanBeCopied;
    use CanWrap;
    use Concerns\CanFormatState;
    use Concerns\HasAffixes;
    use Concerns\HasColor;
    use Concerns\HasIcon;
    use Concerns\HasIconColor;
    use HasFontFamily;
    use HasLineClamp;
    use HasWeight;

    protected bool | Closure $isBadge = false;

    protected bool | Closure $isBulleted = false;

    protected bool | Closure $isProse = false;

    protected bool | Closure $isListWithLineBreaks = false;

    protected int | Closure | null $listLimit = null;

    protected TextSize | string | Closure | null $size = null;

    protected bool | Closure $isLimitedListExpandable = false;

    public function badge(bool | Closure $condition = true): static
    {
        $this->isBadge = $condition;

        return $this;
    }

    public function bulleted(bool | Closure $condition = true): static
    {
        $this->isBulleted = $condition;

        return $this;
    }

    public function listWithLineBreaks(bool | Closure $condition = true): static
    {
        $this->isListWithLineBreaks = $condition;

        return $this;
    }

    public function limitList(int | Closure | null $limit = 3): static
    {
        $this->listLimit = $limit;

        return $this;
    }

    public function prose(bool | Closure $condition = true): static
    {
        $this->isProse = $condition;

        return $this;
    }

    public function size(TextSize | string | Closure | null $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getSize(mixed $state): TextSize | string
    {
        $size = $this->evaluate($this->size, [
            'state' => $state,
        ]);

        if (blank($size)) {
            return TextSize::Small;
        }

        if (is_string($size)) {
            $size = TextSize::tryFrom($size) ?? $size;
        }

        if ($size === 'base') {
            return TextSize::Medium;
        }

        return $size;
    }

    public function isBadge(): bool
    {
        return (bool) $this->evaluate($this->isBadge);
    }

    public function isBulleted(): bool
    {
        return (bool) $this->evaluate($this->isBulleted);
    }

    public function isProse(): bool
    {
        return (bool) $this->evaluate($this->isProse);
    }

    public function isListWithLineBreaks(): bool
    {
        return $this->evaluate($this->isListWithLineBreaks) || $this->isBulleted();
    }

    public function getListLimit(): ?int
    {
        return $this->evaluate($this->listLimit);
    }

    public function expandableLimitedList(bool | Closure $condition = true): static
    {
        $this->isLimitedListExpandable = $condition;

        return $this;
    }

    public function isLimitedListExpandable(): bool
    {
        return (bool) $this->evaluate($this->isLimitedListExpandable);
    }

    public function toEmbeddedHtml(): string
    {
        return view($this->getEntryWrapperAbsoluteView(), [
            'entry' => $this,
            'slot' => new ComponentSlot($this->toEmbeddedContentHtml()),
        ])->toHtml();
    }

    public function toEmbeddedContentHtml(): string
    {
        $isBadge = $this->isBadge();
        $isListWithLineBreaks = $this->isListWithLineBreaks();
        $isLimitedListExpandable = $this->isLimitedListExpandable();

        $state = $this->getState();

        if ($state instanceof Collection) {
            $state = $state->all();
        }

        $attributes = $this->getExtraAttributeBag()
            ->class([
                'fi-in-text',
            ]);

        if (blank($state)) {
            $attributes = $attributes
                ->merge([
                    'x-tooltip' => filled($tooltip = $this->getEmptyTooltip())
                        ? '{
                            content: ' . Js::from($tooltip) . ',
                            theme: $store.theme,
                        }'
                        : null,
                ], escape: false);

            $placeholder = $this->getPlaceholder();

            ob_start(); ?>

            <div <?= $attributes->toHtml() ?>>
                <?php if (filled($placeholder !== null)) { ?>
                    <p class="fi-in-placeholder">
                        <?= e($placeholder) ?>
                    </p>
                <?php } ?>
            </div>

            <?php return ob_get_clean();
        }

        $shouldOpenUrlInNewTab = $this->shouldOpenUrlInNewTab();

        $formatState = function (mixed $stateItem) use ($shouldOpenUrlInNewTab): string {
            $url = $this->getUrl($stateItem);

            $item = '';

            if (filled($url)) {
                $item .= '<a ' . generate_href_html($url, $shouldOpenUrlInNewTab)->toHtml() . '>';
            }

            $item .= e($this->formatState($stateItem));

            if (filled($url)) {
                $item .= '</a>';
            }

            return $item;
        };

        $state = Arr::wrap($state);
        $stateCount = count($state);

        $listLimit = $this->getListLimit() ?? $stateCount;
        $stateOverListLimitCount = 0;

        if ($listLimit && ($stateCount > $listLimit)) {
            $stateOverListLimitCount = $stateCount - $listLimit;

            if (
                (! $isListWithLineBreaks) ||
                (! $isLimitedListExpandable)
            ) {
                $state = array_slice($state, 0, $listLimit);
            }
        }

        if (($stateCount > 1) && (! $isListWithLineBreaks) && (! $isBadge)) {
            $state = [
                implode(
                    ', ',
                    array_map(
                        fn (mixed $stateItem): string => $formatState($stateItem),
                        $state,
                    ),
                ),
            ];

            $stateCount = 1;
            $formatState = fn (mixed $stateItem): string => $stateItem;
        }

        $alignment = $this->getAlignment();

        $attributes = $attributes
            ->class([
                'fi-in-text-has-badges' => $isBadge,
                'fi-wrapped' => $this->canWrap(),
                ($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : (is_string($alignment) ? $alignment : ''),
            ]);

        $lineClamp = $this->getLineClamp();
        $iconPosition = $this->getIconPosition();
        $isBulleted = $this->isBulleted();
        $isProse = $this->isProse();
        $isMarkdown = $this->isMarkdown();

        $getStateItem = function (mixed $stateItem) use ($iconPosition, $isBadge, $isMarkdown, $isProse, $lineClamp): array {
            $color = $this->getColor($stateItem) ?? ($isBadge ? 'primary' : null);
            $iconColor = $this->getIconColor($stateItem);

            $size = $this->getSize($stateItem);

            $iconHtml = generate_icon_html($this->getIcon($stateItem), attributes: (new ComponentAttributeBag)
                ->color(Icon::class, $iconColor), size: match ($size) {
                    TextSize::Medium => IconSize::Medium,
                    TextSize::Large => IconSize::Large,
                    default => IconSize::Small,
                })?->toHtml();

            $isCopyable = $this->isCopyable($stateItem);

            if ($isCopyable) {
                $copyableStateJs = Js::from($this->getCopyableState($stateItem) ?? $this->formatState($stateItem));
                $copyMessageJs = Js::from($this->getCopyMessage($stateItem));
                $copyMessageDurationJs = Js::from($this->getCopyMessageDuration($stateItem));
            }

            return [
                'attributes' => (new ComponentAttributeBag)
                    ->merge([
                        'x-on:click' => $isCopyable
                            ? <<<JS
                                window.navigator.clipboard.writeText({$copyableStateJs})
                                \$tooltip({$copyMessageJs}, {
                                    theme: \$store.theme,
                                    timeout: {$copyMessageDurationJs},
                                })
                                JS
                            : null,
                        'x-tooltip' => filled($tooltip = $this->getTooltip($stateItem))
                            ? '{
                                content: ' . Js::from($tooltip) . ',
                                theme: $store.theme,
                            }'
                            : null,
                    ], escape: false)
                    ->class([
                        'fi-in-text-item',
                        'fi-in-text-item-prose' => $isProse || $isMarkdown,
                        (($fontFamily = $this->getFontFamily($stateItem)) instanceof FontFamily) ? "fi-font-{$fontFamily->value}" : (is_string($fontFamily) ? $fontFamily : ''),
                        'fi-copyable' => $isCopyable,
                    ])
                    ->when(
                        ! $isBadge,
                        fn (ComponentAttributeBag $attributes) => $attributes
                            ->class([
                                ($size instanceof TextSize) ? "fi-size-{$size->value}" : $size,
                                (($weight = $this->getWeight($stateItem)) instanceof FontWeight) ? "fi-font-{$weight->value}" : (is_string($weight) ? $weight : ''),
                            ])
                            ->when($lineClamp, fn (ComponentAttributeBag $attributes) => $attributes->style([
                                "--line-clamp: {$lineClamp}",
                            ]))
                            ->color(Item::class, $color)
                    ),
                'badgeAttributes' => $isBadge
                    ? (new ComponentAttributeBag)
                        ->class([
                            'fi-badge',
                            ($size instanceof TextSize) ? "fi-size-{$size->value}" : $size,
                        ])
                        ->color(Badge::class, $color ?? 'primary')
                    : null,
                'iconAfterHtml' => ($iconPosition === IconPosition::After) ? $iconHtml : '',
                'iconBeforeHtml' => ($iconPosition === IconPosition::Before) ? $iconHtml : '',
            ];
        };

        if (
            ($stateCount === 1) &&
            (! $isBulleted)
        ) {
            $stateItem = Arr::first($state);
            [
                'attributes' => $stateItemAttributes,
                'badgeAttributes' => $stateItemBadgeAttributes,
                'iconAfterHtml' => $stateItemIconAfterHtml,
                'iconBeforeHtml' => $stateItemIconBeforeHtml,
            ] = $getStateItem($stateItem);

            ob_start(); ?>

            <div <?= $attributes
                ->merge($stateItemAttributes->getAttributes(), escape: false)
                ->toHtml() ?>>
                <?php if ($isBadge) { ?>
                <span <?= $stateItemBadgeAttributes->toHtml() ?>>
                <?php } ?>

                <?= $stateItemIconBeforeHtml ?>
                <?= $formatState($stateItem) ?>
                <?= $stateItemIconAfterHtml ?>

                <?php if ($isBadge) { ?>
                    </span>
            <?php } ?>
            </div>

            <?php return ob_get_clean();
        }

        $attributes = $attributes
            ->class([
                'fi-bulleted' => $isBulleted,
                'fi-in-text-has-line-breaks' => $isListWithLineBreaks,
            ]);

        if ($stateOverListLimitCount) {
            $attributes = $attributes
                ->merge([
                    'x-data' => $isLimitedListExpandable
                        ? '{ isLimited: true }'
                        : null,
                ], escape: false)
                ->class([
                    'fi-in-text-list-limited',
                ]);

            ob_start(); ?>

            <div <?= $attributes->toHtml() ?>>
                <ul>
                    <?php $stateIteration = 1; ?>

                    <?php foreach ($state as $stateItem) { ?>
                        <?php [
                            'attributes' => $stateItemAttributes,
                            'badgeAttributes' => $stateItemBadgeAttributes,
                            'iconAfterHtml' => $stateItemIconAfterHtml,
                            'iconBeforeHtml' => $stateItemIconBeforeHtml,
                        ] = $getStateItem($stateItem); ?>

                        <li
                            <?php if ($stateIteration > $listLimit) { ?>
                                x-show="! isLimited"
                                x-cloak
                                x-transition
                            <?php } ?>
                            <?= $stateItemAttributes->toHtml() ?>
                        >
                            <?php if ($isBadge) { ?>
                            <span <?= $stateItemBadgeAttributes->toHtml() ?>>
                            <?php } ?>

                            <?= $stateItemIconBeforeHtml ?>
                            <?= $formatState($stateItem) ?>
                            <?= $stateItemIconAfterHtml ?>

                            <?php if ($isBadge) { ?>
                                </span>
                        <?php } ?>
                        </li>

                        <?php $stateIteration++ ?>
                    <?php } ?>
                </ul>

                <p class="fi-in-text-list-limited-message">
                    <?php if ($isLimitedListExpandable) { ?>
                        <button
                            type="button"
                            x-on:click.prevent="isLimited = false"
                            x-show="isLimited"
                            class="fi-link fi-size-xs"
                        >
                            <?= trans_choice('filament-infolists::components.entries.text.actions.expand_list', $stateOverListLimitCount) ?>
                        </button>

                        <button
                            type="button"
                            x-on:click.prevent="isLimited = true"
                            x-cloak
                            x-show="! isLimited"
                            class="fi-link fi-size-xs"
                        >
                            <?= trans_choice('filament-infolists::components.entries.text.actions.collapse_list', $stateOverListLimitCount) ?>
                        </button>
                    <?php } else { ?>
                        <?= trans_choice('filament-infolists::components.entries.text.more_list_items', $stateOverListLimitCount) ?>
                    <?php } ?>
                </p>
            </div>

            <?php return ob_get_clean();
        }

        ob_start(); ?>

        <ul <?= $attributes->toHtml() ?>>
            <?php foreach ($state as $stateItem) { ?>
                <?php [
                    'attributes' => $stateItemAttributes,
                    'badgeAttributes' => $stateItemBadgeAttributes,
                    'iconAfterHtml' => $stateItemIconAfterHtml,
                    'iconBeforeHtml' => $stateItemIconBeforeHtml,
                ] = $getStateItem($stateItem); ?>

                <li <?= $stateItemAttributes->toHtml() ?>>
                    <?php if ($isBadge) { ?>
                    <span <?= $stateItemBadgeAttributes->toHtml() ?>>
                    <?php } ?>

                    <?= $stateItemIconBeforeHtml ?>
                    <?= $formatState($stateItem) ?>
                    <?= $stateItemIconAfterHtml ?>

                    <?php if ($isBadge) { ?>
                        </span>
                <?php } ?>
                </li>
            <?php } ?>
        </ul>

        <?php return ob_get_clean();
    }

    public function canWrapByDefault(): bool
    {
        return true;
    }
}
