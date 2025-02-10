<?php

namespace Filament\Schemas\Concerns;

trait Cloneable
{
    public function getClone(): static
    {
        $clone = clone $this;
        $clone->flushCachedAbsoluteKey();
        $clone->flushCachedAbsoluteStatePath();
        $clone->flushCachedInheritanceKey();
        $clone->cloneComponents();

        return $clone;
    }
}
