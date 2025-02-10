<?php

namespace Filament\Tables\View\Components\Columns\TextColumn\Item;

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
