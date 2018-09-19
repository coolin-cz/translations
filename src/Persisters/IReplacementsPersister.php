<?php

declare(strict_types=1);

namespace Freelo\Translations\Persisters;

use Freelo\Translations\ValueObjects\Replacement;

interface IReplacementsPersister
{

    /**
     * @param Replacement[] $replacements
     */
    public function persist(array $replacements, callable $callback = null): void;

}
