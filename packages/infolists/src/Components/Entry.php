<?php

namespace Filament\Infolists\Components;

use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\CanOpenUrl;
use Filament\Schemas\Schema;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasPlaceholder;
use Filament\Support\Enums\ActionSize;
use Illuminate\Contracts\Support\Htmlable;

class Entry extends Component
{
    use CanOpenUrl;
    use Concerns\HasExtraEntryWrapperAttributes;
    use Concerns\HasHelperText;
    use Concerns\HasHint;
    use Concerns\HasName;
    use Concerns\HasTooltip;
    use HasAlignment;
    use HasPlaceholder;

    protected string $viewIdentifier = 'entry';

    const ABOVE_LABEL_CONTAINER = 'above_label';

    const BELOW_LABEL_CONTAINER = 'below_label';

    const BEFORE_LABEL_CONTAINER = 'before_label';

    const AFTER_LABEL_CONTAINER = 'after_label';

    const ABOVE_CONTENT_CONTAINER = 'above_content';

    const BELOW_CONTENT_CONTAINER = 'below_content';

    const BEFORE_CONTENT_CONTAINER = 'before_content';

    const AFTER_CONTENT_CONTAINER = 'after_content';

    final public function __construct(string $name)
    {
        $this->name($name);
        $this->statePath($name);
    }

    public static function make(?string $name = null): static
    {
        $entryClass = static::class;

        $name ??= static::getDefaultName();

        if (blank($name)) {
            throw new Exception("Entry of class [$entryClass] must have a unique name, passed to the [make()] method.");
        }

        $static = app($entryClass, ['name' => $name]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpHint();
    }

    public static function getDefaultName(): ?string
    {
        return null;
    }

    public function getState(): mixed
    {
        return $this->getConstantState();
    }

    public function getLabel(): string | Htmlable | null
    {
        $label = parent::getLabel() ?? (string) str($this->getName())
            ->before('.')
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();

        return (is_string($label) && $this->shouldTranslateLabel) ?
            __($label) :
            $label;
    }

    public function state(mixed $state): static
    {
        $this->constantState($state);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string> | Schema | Component | Action | ActionGroup | string | Closure | null  $components
     */
    public function aboveLabel(array | Schema | Component | Action | ActionGroup | string | Closure | null $components): static
    {
        $this->childComponents($components, static::ABOVE_LABEL_CONTAINER);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string> | Schema | Component | Action | ActionGroup | string | Closure | null  $components
     */
    public function belowLabel(array | Schema | Component | Action | ActionGroup | string | Closure | null $components): static
    {
        $this->childComponents($components, static::BELOW_LABEL_CONTAINER);

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

    /**
     * @param  array<Component | Action | ActionGroup | string> | Schema | Component | Action | ActionGroup | string | Closure | null  $components
     */
    public function beforeContent(array | Schema | Component | Action | ActionGroup | string | Closure | null $components): static
    {
        $this->childComponents($components, static::BEFORE_CONTENT_CONTAINER);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string> | Schema | Component | Action | ActionGroup | string | Closure | null  $components
     */
    public function afterContent(array | Schema | Component | Action | ActionGroup | string | Closure | null $components): static
    {
        $this->childComponents($components, static::AFTER_CONTENT_CONTAINER);

        return $this;
    }

    protected function makeSchemaForSlot(string $slot): Schema
    {
        $schema = parent::makeSchemaForSlot($slot);

        if (in_array($slot, [static::AFTER_LABEL_CONTAINER, static::AFTER_CONTENT_CONTAINER])) {
            $schema->alignEnd();
        }

        return $schema;
    }

    protected function configureSchemaForSlot(Schema $schema, string $slot): Schema
    {
        $schema = parent::configureSchemaForSlot($schema, $slot);

        if (in_array($slot, [
            static::ABOVE_LABEL_CONTAINER,
            static::BELOW_LABEL_CONTAINER,
            static::BEFORE_LABEL_CONTAINER,
            static::AFTER_LABEL_CONTAINER,
            static::ABOVE_CONTENT_CONTAINER,
            static::BELOW_CONTENT_CONTAINER,
            static::BEFORE_CONTENT_CONTAINER,
            static::AFTER_CONTENT_CONTAINER,
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
}
