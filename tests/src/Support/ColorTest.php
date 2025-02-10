<?php

use Filament\Support\Colors\Color;
use Filament\Support\Colors\ColorManager;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\View\Components\Badge;
use Filament\Support\View\Components\Button;
use Filament\Support\View\Components\Contracts\HasColor;
use Filament\Support\View\Components\Dropdown\Header as DropdownHeader;
use Filament\Support\View\Components\Dropdown\Item as DropdownItem;
use Filament\Support\View\Components\Dropdown\Item\Icon as DropdownItemIcon;
use Filament\Support\View\Components\IconButton;
use Filament\Support\View\Components\Input\Wrapper\Icon as InputWrapperIcon;
use Filament\Support\View\Components\Link;
use Filament\Support\View\Components\Modal\Icon as ModalIcon;
use Filament\Support\View\Components\Section\Icon as SectionIcon;
use Filament\Support\View\Components\Toggle;
use Filament\Tables\View\Components\Columns\IconColumn\Icon as TableIconColumnIcon;
use Filament\Tables\View\Components\Columns\Summarizers\Count\Icon as TableColumnCountSummarizerIcon;
use Filament\Tables\View\Components\Columns\TextColumn\Item as TableTextColumnItem;
use Filament\Tables\View\Components\Columns\TextColumn\Item\Icon as TableTextColumnItemIcon;
use Filament\Tests\TestCase;
use Filament\Widgets\View\Components\ChartWidget;
use Filament\Widgets\View\Components\StatsOverviewWidget\Stat\Description as StatsOverviewWidgetStatDescription;
use Filament\Widgets\View\Components\StatsOverviewWidget\Stat\StatsOverviewWidgetStatChart;
use Illuminate\Support\Str;

uses(TestCase::class);

it('generates colors from a HEX value', function (string $color) {
    expect(Color::generatePalette($color))
        ->toMatchSnapshot();
})->with([
    '#49D359',
    '#8A2BE2',
    '#A52A2A',
    '#000000',
    '#FFFFFF',
]);

it('generates colors from an RGB value', function (string $color) {
    expect(Color::generatePalette($color))
        ->toMatchSnapshot();
})->with([
    'rgb(128, 8, 8)',
    'rgb(93, 255, 2)',
    'rgb(243, 243, 21)',
    'rgb(0, 0, 0)',
    'rgb(255, 255, 255)',
]);

it('returns all colors', function () {
    $colors = [];

    foreach ((new ReflectionClass(Color::class))->getConstants() as $name => $color) {
        $colors[Str::lower($name)] = $color;
    }

    expect(Color::all())
        ->toBe($colors);
});

it('generates component classes', function (string | HasColor $component, string $color) {
    expect(FilamentColor::getComponentClasses($component, $color))
        ->toMatchSnapshot();
})
    ->with([
        'badge' => Badge::class,
        'button' => new Button(isOutlined: false),
        'outlined button' => new Button(isOutlined: true),
        'chart widget' => ChartWidget::class,
        'dropdown header' => DropdownHeader::class,
        'dropdown item icon' => DropdownItemIcon::class,
        'dropdown item' => DropdownItem::class,
        'icon button' => IconButton::class,
        'input wrapper icon' => InputWrapperIcon::class,
        'link' => Link::class,
        'modal icon' => ModalIcon::class,
        'section icon' => SectionIcon::class,
        'stats overview widget stat description' => StatsOverviewWidgetStatDescription::class,
        'stats overview widget stat chart' => StatsOverviewWidgetStatChart::class,
        'table column count summarizer icon' => TableColumnCountSummarizerIcon::class,
        'table icon column icon' => TableIconColumnIcon::class,
        'table text column item' => TableTextColumnItem::class,
        'table text column item icon' => TableTextColumnItemIcon::class,
        'toggle' => Toggle::class,
    ])
    ->with(fn (): array => array_keys(app(ColorManager::class)->getColors()));
