<?php

namespace Filament\Schemas\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Concerns\CanBeCollapsed;
use Filament\Schemas\Components\Concerns\CanBeCompact;
use Filament\Schemas\Components\Concerns\CanBeDivided;
use Filament\Schemas\Components\Concerns\CanBeSecondary;
use Filament\Schemas\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Schemas\Components\Concerns\HasDescription;
use Filament\Schemas\Components\Concerns\HasFooterActions;
use Filament\Schemas\Components\Concerns\HasHeaderActions;
use Filament\Schemas\Components\Concerns\HasHeading;
use Filament\Schemas\Components\Contracts\CanConcealComponents;
use Filament\Schemas\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Schemas\Schema;
use Filament\Support\Concerns\CanBeContained;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Concerns\HasIconColor;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\Alignment;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class Section extends Component implements CanConcealComponents, CanEntangleWithSingularRelationships
{
    use CanBeCollapsed;
    use CanBeCompact;
    use CanBeContained;
    use CanBeDivided;
    use CanBeSecondary;
    use EntanglesStateWithSingularRelationship;
    use HasDescription;
    use HasExtraAlpineAttributes;
    use HasFooterActions;
    use HasHeaderActions;
    use HasHeading;
    use HasIcon;
    use HasIconColor;

    /**
     * @var view-string
     */
    protected string $view = 'filament-schemas::components.section';

    protected bool | Closure | null $isAside = null;

    protected bool | Closure $isFormBefore = false;

    const AFTER_HEADER_CONTAINER = 'after_header';

    const FOOTER_CONTAINER = 'footer';

    const BEFORE_LABEL_CONTAINER = 'before_label';

    const AFTER_LABEL_CONTAINER = 'after_label';

    const ABOVE_CONTENT_CONTAINER = 'above_content';

    const BELOW_CONTENT_CONTAINER = 'below_content';

    /**
     * @param  string | array<Component | Action | ActionGroup> | Htmlable | Closure | null  $heading
     */
    final public function __construct(string | array | Htmlable | Closure | null $heading = null)
    {
        is_array($heading)
            ? $this->components($heading)
            : $this->heading($heading);
    }

    /**
     * @param  string | array<Component | Action | ActionGroup> | Htmlable | Closure | null  $heading
     */
    public static function make(string | array | Htmlable | Closure | null $heading = null): static
    {
        $static = app(static::class, ['heading' => $heading]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->columnSpan('full');

        $this->key(function (Section $component): ?string {
            if ($statePath = $component->getStatePath(isAbsolute: false)) {
                return $statePath;
            }

            $heading = $this->getHeading();

            if (blank($heading)) {
                return null;
            }

            return Str::slug(Str::transliterate($heading, strict: true));
        });

        $this->afterHeader(fn (Section $component): array => $component->getHeaderActions());
        $this->footer(function (Section $component): Schema {
            return match ($component->getFooterActionsAlignment()) {
                Alignment::End, Alignment::Right => Schema::end($component->getFooterActions()),
                default => Schema::start($component->getFooterActions()),
            };
        });
    }

    public function aside(bool | Closure | null $condition = true): static
    {
        $this->isAside = $condition;

        return $this;
    }

    public function canConcealComponents(): bool
    {
        return $this->isCollapsible();
    }

    public function isAside(): bool
    {
        return (bool) ($this->evaluate($this->isAside) ?? false);
    }

    public function formBefore(bool | Closure $condition = true): static
    {
        $this->isFormBefore = $condition;

        return $this;
    }

    public function isFormBefore(): bool
    {
        return (bool) $this->evaluate($this->isFormBefore);
    }

    /**
     * @param  array<Component | Action | ActionGroup | string> | Schema | Component | Action | ActionGroup | string | Closure | null  $components
     */
    public function afterHeader(array | Schema | Component | Action | ActionGroup | string | Closure | null $components): static
    {
        $this->childComponents($components, static::AFTER_HEADER_CONTAINER);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string> | Schema | Component | Action | ActionGroup | string | Closure | null  $components
     */
    public function footer(array | Schema | Component | Action | ActionGroup | string | Closure | null $components): static
    {
        $this->childComponents($components, static::FOOTER_CONTAINER);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string> | Schema | Component | Action | ActionGroup | string | Closure | null  $components
     */
    public function beforeLabel(array | Schema | Component | Action | ActionGroup | string | Closure | null $components): static
    {
        $this->childComponents($components, static::BEFORE_LABEL_CONTAINER);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string> | Schema | Component | Action | ActionGroup | string | Closure | null  $components
     */
    public function afterLabel(array | Schema | Component | Action | ActionGroup | string | Closure | null $components): static
    {
        $this->childComponents($components, static::AFTER_LABEL_CONTAINER);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string> | Schema | Component | Action | ActionGroup | string | Closure | null  $components
     */
    public function aboveContent(array | Schema | Component | Action | ActionGroup | string | Closure | null $components): static
    {
        $this->childComponents($components, static::ABOVE_CONTENT_CONTAINER);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string> | Schema | Component | Action | ActionGroup | string | Closure | null  $components
     */
    public function belowContent(array | Schema | Component | Action | ActionGroup | string | Closure | null $components): static
    {
        $this->childComponents($components, static::BELOW_CONTENT_CONTAINER);

        return $this;
    }

    protected function makeSchemaForSlot(string $slot): Schema
    {
        $schema = parent::makeSchemaForSlot($slot);

        if (in_array($slot, [static::AFTER_HEADER_CONTAINER, static::AFTER_LABEL_CONTAINER])) {
            $schema->alignEnd();
        }

        return $schema;
    }

    protected function configureSchemaForSlot(Schema $schema, string $slot): Schema
    {
        $schema = parent::configureSchemaForSlot($schema, $slot);

        if (in_array($slot, [
            static::AFTER_HEADER_CONTAINER,
            static::FOOTER_CONTAINER,
            static::BEFORE_LABEL_CONTAINER,
            static::AFTER_LABEL_CONTAINER,
            static::ABOVE_CONTENT_CONTAINER,
            static::BELOW_CONTENT_CONTAINER,
        ])) {
            $schema
                ->inline()
                ->embeddedInParentComponent();
        }

        if (in_array($slot, [
            static::BEFORE_LABEL_CONTAINER,
            static::AFTER_LABEL_CONTAINER,
            static::ABOVE_CONTENT_CONTAINER,
            static::BELOW_CONTENT_CONTAINER,
        ])) {
            $schema
                ->configureActionsUsing(fn (Action $action) => $action
                    ->defaultSize(ActionSize::Small)
                    ->defaultView(Action::LINK_VIEW))
                ->configureActionGroupsUsing(fn (ActionGroup $actionGroup) => $actionGroup->defaultSize(ActionSize::Small));
        }

        return $schema;
    }

    public function getHeadingsCount(): int
    {
        if (blank($this->getHeading())) {
            return 0;
        }

        return 1;
    }
}
