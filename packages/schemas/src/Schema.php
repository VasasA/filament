<?php

namespace Filament\Schemas;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasExtraAttributes;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class Schema extends ViewComponent
{
    use Concerns\BelongsToLivewire;
    use Concerns\BelongsToModel;
    use Concerns\BelongsToParentComponent;
    use Concerns\CanBeDisabled;
    use Concerns\CanBeEmbeddedInParentComponent;
    use Concerns\CanBeHidden;
    use Concerns\CanBeInline;
    use Concerns\CanBeValidated;
    use Concerns\CanConfigureActions;
    use Concerns\Cloneable;
    use Concerns\HasColumns;
    use Concerns\HasComponents;
    use Concerns\HasEntryWrapper;
    use Concerns\HasFieldWrapper;
    use Concerns\HasGap;
    use Concerns\HasHeadings;
    use Concerns\HasInlineLabels;
    use Concerns\HasKey;
    use Concerns\HasOperation;
    use Concerns\HasState;
    use Concerns\HasStateBindingModifiers;
    use HasAlignment;
    use HasExtraAttributes;

    protected string $view = 'filament-schemas::schema';

    protected string $evaluationIdentifier = 'schema';

    protected string $viewIdentifier = 'schema';

    public static string $defaultCurrency = 'usd';

    public static string $defaultDateDisplayFormat = 'M j, Y';

    public static string $defaultIsoDateDisplayFormat = 'L';

    public static string $defaultDateTimeDisplayFormat = 'M j, Y H:i:s';

    public static string $defaultIsoDateTimeDisplayFormat = 'LLL';

    public static ?string $defaultNumberLocale = null;

    public static string $defaultTimeDisplayFormat = 'H:i:s';

    public static string $defaultIsoTimeDisplayFormat = 'LT';

    final public function __construct((Component & HasSchemas) | null $livewire = null)
    {
        $this->livewire($livewire);
    }

    public static function make((Component & HasSchemas) | null $livewire = null): static
    {
        $static = app(static::class, ['livewire' => $livewire]);
        $static->configure();

        return $static;
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'container' => [$this],
            'livewire' => [$this->getLivewire()],
            'model' => [$this->getModel()],
            'record' => [$this->getRecord()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        $record = $this->getRecord();

        if (! ($record instanceof Model)) {
            return match ($parameterType) {
                static::class, self::class => [$this],
                default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
            };
        }

        return match ($parameterType) {
            static::class, self::class => [$this],
            Model::class, $record::class => [$record],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }

    /**
     * @param  array<Component | Action | ActionGroup | string> | Schema | Component | Action | ActionGroup | string | Closure  $components
     */
    public static function start(array | Schema | Component | Action | ActionGroup | string | Closure $components): static
    {
        return static::make()
            ->components($components)
            ->alignStart();
    }

    /**
     * @param  array<Component | Action | ActionGroup | string> | Schema | Component | Action | ActionGroup | string | Closure  $components
     */
    public static function end(array | Schema | Component | Action | ActionGroup | string | Closure $components): static
    {
        return static::make()
            ->components($components)
            ->alignEnd();
    }

    /**
     * @param  array<Component | Action | ActionGroup | string> | Schema | Component | Action | ActionGroup | string | Closure  $components
     */
    public static function between(array | Schema | Component | Action | ActionGroup | string | Closure $components): static
    {
        return static::make()
            ->components($components)
            ->alignBetween();
    }
}
