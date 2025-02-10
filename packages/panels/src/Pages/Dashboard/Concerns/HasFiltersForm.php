<?php

namespace Filament\Pages\Dashboard\Concerns;

use Filament\Schemas\Schema;

trait HasFiltersForm /** @phpstan-ignore trait.unused */
{
    use HasFilters;

    protected function getHasFiltersForms(): array
    {
        return [
            'filtersForm' => $this->getFiltersForm(),
        ];
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema;
    }

    public function getFiltersForm(): Schema
    {
        if ((! $this->isCachingSchemas) && $this->hasCachedSchema('filtersForm')) {
            return $this->getSchema('filtersForm');
        }

        return $this->filtersForm($this->makeSchema()
            ->extraAttributes(['wire:partial' => 'table-filters-form'])
            ->columns([
                'md' => 2,
                'xl' => 3,
                '2xl' => 4,
            ])
            ->statePath('filters')
            ->live());
    }
}
