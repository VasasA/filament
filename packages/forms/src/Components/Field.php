<?php

namespace Filament\Forms\Components;

use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Filament\Schemas\Components\StateCasts\EnumStateCast;
use Filament\Schemas\Schema;
use Filament\Support\Enums\ActionSize;

class Field extends Component implements Contracts\HasValidationRules
{
    use Concerns\CanBeAutofocused;
    use Concerns\CanBeMarkedAsRequired;
    use Concerns\CanBeValidated;
    use Concerns\CanDisableGrammarly;
    use Concerns\HasEnum;
    use Concerns\HasExtraFieldWrapperAttributes;
    use Concerns\HasHelperText;
    use Concerns\HasHint;
    use Concerns\HasName;

    protected string $viewIdentifier = 'field';

    const ABOVE_LABEL_CONTAINER = 'above_label';

    const BELOW_LABEL_CONTAINER = 'below_label';

    const BEFORE_LABEL_CONTAINER = 'before_label';

    const AFTER_LABEL_CONTAINER = 'after_label';

    const ABOVE_CONTENT_CONTAINER = 'above_content';

    const BELOW_CONTENT_CONTAINER = 'below_content';

    const BEFORE_CONTENT_CONTAINER = 'before_content';

    const AFTER_CONTENT_CONTAINER = 'after_content';

    const ABOVE_ERROR_MESSAGE_CONTAINER = 'above_error_message';

    const BELOW_ERROR_MESSAGE_CONTAINER = 'below_error_message';

    final public function __construct(string $name)
    {
        $this->name($name);
        $this->statePath($name);
    }

    public static function make(?string $name = null): static
    {
        $fieldClass = static::class;

        $name ??= static::getDefaultName();

        if ($name === null) {
            throw new Exception("Field of class [$fieldClass] must have a unique name, passed to the [make()] method.");
        }

        $static = app($fieldClass, ['name' => $name]);

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

    /**
     * @return array<StateCast>
     */
    public function getDefaultStateCasts(): array
    {
        $casts = parent::getDefaultStateCasts();

        if ($enumStateCast = $this->getEnumDefaultStateCast()) {
            $casts[] = $enumStateCast;
        }

        return $casts;
    }

    public function getEnumDefaultStateCast(): ?StateCast
    {
        $enum = $this->getEnum();

        if (blank($enum)) {
            return null;
        }

        return app(
            EnumStateCast::class,
            ['enum' => $enum],
        );
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

    /**
     * @param  array<Component | Action | ActionGroup | string> | Schema | Component | Action | ActionGroup | string | Closure | null  $components
     */
    public function aboveErrorMessage(array | Schema | Component | Action | ActionGroup | string | Closure | null $components): static
    {
        $this->childComponents($components, static::ABOVE_ERROR_MESSAGE_CONTAINER);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string> | Schema | Component | Action | ActionGroup | string | Closure | null  $components
     */
    public function belowErrorMessage(array | Schema | Component | Action | ActionGroup | string | Closure | null $components): static
    {
        $this->childComponents($components, static::BELOW_ERROR_MESSAGE_CONTAINER);

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
            static::ABOVE_ERROR_MESSAGE_CONTAINER,
            static::BELOW_ERROR_MESSAGE_CONTAINER,
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
