<?php

namespace Filament\Schemas\Components\Concerns;

trait Cloneable
{
    public function getClone(): static
    {
        $clone = clone $this;
        $clone->flushCachedAbsoluteKey();
        $clone->flushCachedAbsoluteStatePath();
        $clone->flushCachedInheritanceKey();
        $clone->cloneChildComponents();

        return $clone;
    }
}
