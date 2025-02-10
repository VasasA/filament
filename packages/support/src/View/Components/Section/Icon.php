<?php

namespace Filament\Support\View\Components\Section;

use Filament\Support\View\Components\Contracts\HasColor;
use Filament\Support\View\Components\Contracts\HasDefaultGrayColor;

class Icon implements HasColor, HasDefaultGrayColor
{
    /**
     * @param  array<int, string>  $color
     * @return array<string>
     */
    public function getColorClasses(array $color): array
    {
        return [];
    }
}
