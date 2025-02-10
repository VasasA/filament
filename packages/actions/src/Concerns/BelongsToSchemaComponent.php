<?php

namespace Filament\Actions\Concerns;

use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;

trait BelongsToSchemaComponent
{
    protected ?Component $schemaComponent = null;

    protected ?Schema $schemaComponentContainer = null;

    public function schemaComponent(?Component $component): static
    {
        $this->schemaComponent = $component;

        return $this;
    }

    public function schemaComponentContainer(?Schema $schema): static
    {
        $this->schemaComponentContainer = $schema;

        return $this;
    }

    public function getSchemaComponent(): ?Component
    {
        return $this->schemaComponent ?? $this->getSchemaComponentContainer()?->getParentComponent() ?? $this->getGroup()?->getSchemaComponent();
    }

    public function getSchemaComponentContainer(): ?Schema
    {
        return $this->schemaComponentContainer ?? $this->getGroup()?->getSchemaComponentContainer();
    }
}
