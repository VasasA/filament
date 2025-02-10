<?php

namespace Filament\Schemas\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Schema;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasVerticalAlignment;
use Filament\Support\Enums\ActionSize;

class Actions extends Component
{
    use HasAlignment;
    use HasLabel;
    use HasVerticalAlignment;

    protected string $view = 'filament-schemas::components.actions';

    protected bool | Closure $isSticky = false;

    protected bool | Closure $isFullWidth = false;

    const BEFORE_LABEL_CONTAINER = 'before_label';

    const AFTER_LABEL_CONTAINER = 'after_label';

    const ABOVE_CONTENT_CONTAINER = 'above_content';

    const BELOW_CONTENT_CONTAINER = 'below_content';

    /**
     * @param  array<Action | ActionGroup>  $actions
     */
    final public function __construct(array $actions)
    {
        $this->actions($actions);
    }

    /**
     * @param  array<Action | ActionGroup>  $actions
     */
    public static function make(array $actions): static
    {
        $static = app(static::class, ['actions' => $actions]);
        $static->configure();

        return $static;
    }

    /**
     * @param  array<Action | ActionGroup>  $actions
     */
    public function actions(array $actions): static
    {
        $this->components($actions);

        return $this;
    }

    public function isHidden(): bool
    {
        if (parent::isHidden()) {
            return true;
        }

        foreach ($this->getChildComponentContainer()->getComponents() as $component) {
            if ($component->isVisible()) {
                return false;
            }
        }

        return true;
    }

    public function fullWidth(bool | Closure $isFullWidth = true): static
    {
        $this->isFullWidth = $isFullWidth;

        return $this;
    }

    public function isFullWidth(): bool
    {
        return (bool) $this->evaluate($this->isFullWidth);
    }

    /**
     * @param  array<Component | Action | ActionGroup> | Schema | Component | Action | ActionGroup | string | Closure | null  $components
     */
    public function beforeLabel(array | Schema | Component | Action | ActionGroup | string | Closure | null $components): static
    {
        $this->childComponents($components, static::BEFORE_LABEL_CONTAINER);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup> | Schema | Component | Action | ActionGroup | string | Closure | null  $components
     */
    public function afterLabel(array | Schema | Component | Action | ActionGroup | string | Closure | null $components): static
    {
        $this->childComponents($components, static::AFTER_LABEL_CONTAINER);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup> | Schema | Component | Action | ActionGroup | string | Closure | null  $components
     */
    public function aboveContent(array | Schema | Component | Action | ActionGroup | string | Closure | null $components): static
    {
        $this->childComponents($components, static::ABOVE_CONTENT_CONTAINER);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup> | Schema | Component | Action | ActionGroup | string | Closure | null  $components
     */
    public function belowContent(array | Schema | Component | Action | ActionGroup | string | Closure | null $components): static
    {
        $this->childComponents($components, static::BELOW_CONTENT_CONTAINER);

        return $this;
    }

    protected function makeSchemaForSlot(string $slot): Schema
    {
        $schema = parent::makeSchemaForSlot($slot);

        if ($slot === static::AFTER_LABEL_CONTAINER) {
            $schema->alignEnd();
        }

        return $schema;
    }

    protected function configureSchemaForSlot(Schema $schema, string $slot): Schema
    {
        $schema = parent::configureSchemaForSlot($schema, $slot);

        if (in_array($slot, [
            static::BEFORE_LABEL_CONTAINER,
            static::AFTER_LABEL_CONTAINER,
            static::ABOVE_CONTENT_CONTAINER,
            static::BELOW_CONTENT_CONTAINER,
        ])) {
            $schema
                ->inline()
                ->embeddedInParentComponent()
                ->configureActionsUsing(fn (Action $action) => $action
                    ->defaultSize(ActionSize::Small)
                    ->defaultView(Action::LINK_VIEW))
                ->configureActionGroupsUsing(fn (ActionGroup $actionGroup) => $actionGroup->defaultSize(ActionSize::Small));
        }

        return $schema;
    }

    public function sticky(bool | Closure $condition = true): static
    {
        $this->isSticky = $condition;

        return $this;
    }

    public function isSticky(): bool
    {
        return (bool) $this->evaluate($this->isSticky);
    }
}
