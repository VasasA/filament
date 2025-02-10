<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;

trait HasChildComponents
{
    /**
     * @var array<string, array<Component | Action | ActionGroup | string> | Schema | Component | Action | ActionGroup | string | Closure | null>
     */
    protected array $childComponents = [];

    /**
     * @param  array<Component | Action | ActionGroup | string> | Closure  $components
     */
    public function components(array | Closure $components): static
    {
        $this->childComponents($components);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string> | Schema | Component | Action | ActionGroup | string | Closure | null  $components
     */
    public function childComponents(array | Schema | Component | Action | ActionGroup | string | Closure | null $components, string $slot = 'default'): static
    {
        $this->childComponents[$slot] = $components;

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string> | Closure  $components
     */
    public function schema(array | Closure $components): static
    {
        $this->childComponents($components);

        return $this;
    }

    /**
     * @return array<Component | Action | ActionGroup | string>
     */
    public function getChildComponents(?string $slot = null): array
    {
        return $this->getChildComponentContainer($slot)->getComponents();
    }

    /**
     * @return array<Component | Action | ActionGroup | string>
     */
    public function getDefaultChildComponents(): array
    {
        return $this->evaluate($this->childComponents['default'] ?? []) ?? [];
    }

    /**
     * @param  array-key  $key
     */
    public function getChildComponentContainer($key = null): ?Schema
    {
        if (filled($key) && array_key_exists($key, $containers = $this->getDefaultChildComponentContainers())) {
            return $containers[$key];
        }

        $slot = $key ?? 'default';

        $components = ($slot === 'default')
            ? $this->getDefaultChildComponents()
            : $this->evaluate($this->childComponents[$slot] ?? []) ?? [];

        if (blank($components)) {
            return ($slot === 'default')
                ? $this->configureSchemaForSlot(
                    $this->makeSchemaForSlot($slot),
                    $slot,
                )
                : null;
        }

        if ($components instanceof Schema) {
            return $this->configureSchemaForSlot(
                $components
                    ->livewire($this->getLivewire())
                    ->parentComponent($this),
                $slot,
            );
        }

        return $this->configureSchemaForSlot(
            $this->makeSchemaForSlot($slot)
                ->components($components),
            $slot,
        );
    }

    protected function makeSchemaForSlot(string $slot): Schema
    {
        return Schema::make($this->getLivewire())
            ->parentComponent($this);
    }

    protected function configureSchemaForSlot(Schema $schema, string $slot): Schema
    {
        return $schema;
    }

    /**
     * @return array<Schema>
     */
    public function getChildComponentContainers(bool $withHidden = false): array
    {
        if ((! $withHidden) && $this->isHidden()) {
            return [];
        }

        return [
            ...(array_key_exists('default', $this->childComponents) ? $this->getDefaultChildComponentContainers() : []),
            ...array_reduce(
                array_keys($this->childComponents),
                function (array $carry, string $slot): array {
                    if ($slot === 'default') {
                        return $carry;
                    }

                    if ($container = $this->getChildComponentContainer($slot)) {
                        $carry[$slot] = $container;
                    }

                    return $carry;
                },
                initial: [],
            ),
        ];
    }

    /**
     * @return array<Schema>
     */
    public function getDefaultChildComponentContainers(): array
    {
        return ['default' => $this->getChildComponentContainer()];
    }

    protected function cloneChildComponents(): static
    {
        foreach ($this->childComponents as $slot => $childComponents) {
            if (is_array($childComponents)) {
                $this->childComponents[$slot] = array_map(
                    fn (Component | Action | ActionGroup $component): Component | Action | ActionGroup => match (true) {
                        $component instanceof Component => $component->getClone(),
                        default => clone $component,
                    },
                    $childComponents,
                );
            } elseif (! ($childComponents instanceof Closure)) {
                $this->childComponents[$slot] = $childComponents->getClone();
            }
        }

        return $this;
    }
}
