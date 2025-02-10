<?php

namespace Filament\Widgets\View\Components\StatsOverviewWidget\Stat;

use Filament\Support\View\Components\Contracts\HasColor;
use Filament\Support\View\Components\Contracts\HasDefaultGrayColor;

class StatsOverviewWidgetStatChart implements HasColor, HasDefaultGrayColor
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
