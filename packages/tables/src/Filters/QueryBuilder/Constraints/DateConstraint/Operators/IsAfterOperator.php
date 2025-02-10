<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint\Operators;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Component;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class IsAfterOperator extends Operator
{
    public function getName(): string
    {
        return 'isAfter';
    }

    public function getLabel(): string
    {
        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.date.is_after.label.inverse' :
                'filament-tables::filters/query-builder.operators.date.is_after.label.direct',
        );
    }

    public function getSummary(): string
    {
        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.date.is_after.summary.inverse' :
                'filament-tables::filters/query-builder.operators.date.is_after.summary.direct',
            [
                'attribute' => $this->getConstraint()->getAttributeLabel(),
                'date' => Carbon::parse($this->getSettings()['date'])->toFormattedDateString(),
            ],
        );
    }

    /**
     * @return array<Component | Action | ActionGroup>
     */
    public function getFormSchema(): array
    {
        return [
            DatePicker::make('date')
                ->label(__('filament-tables::filters/query-builder.operators.date.form.date.label'))
                ->required(),
        ];
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        return $query->whereDate($qualifiedColumn, $this->isInverse() ? '<' : '>=', $this->getSettings()['date']);
    }
}
