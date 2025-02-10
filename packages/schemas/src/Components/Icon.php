<?php

namespace Filament\Schemas\Components;

use BackedEnum;
use Closure;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasTooltip;

class Icon extends Component
{
    use HasColor;
    use HasTooltip;

    protected string $view = 'filament-schemas::components.icon';

    protected string | BackedEnum | Closure $icon;

    final public function __construct(string | BackedEnum | Closure $icon)
    {
        $this->icon($icon);
    }

    public static function make(string | BackedEnum | Closure $icon): static
    {
        $static = app(static::class, ['icon' => $icon]);
        $static->configure();

        return $static;
    }

    public function icon(string | BackedEnum | Closure $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(): string | BackedEnum
    {
        return $this->evaluate($this->icon);
    }
}
