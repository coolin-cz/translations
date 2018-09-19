<?php

declare(strict_types=1);

namespace Freelo\Translations\Exceptions;

final class NoPlaceholderFound extends \InvalidArgumentException
{

    public function __construct(string $placeholder, \Exception $e = null)
    {
        parent::__construct(sprintf('No placeholder "%s" found', $placeholder), 0, $e);
    }

}
