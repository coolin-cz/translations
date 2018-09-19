<?php

declare(strict_types=1);

namespace Freelo\Translations\Exceptions;

final class NoTranslatedMessageFound extends \InvalidArgumentException
{

    public function __construct(string $message, string $fromLanguageCode, string $toLanguageCode, \Exception $e = null)
    {
        parent::__construct(sprintf('No translated message "%s" from lang "%s" to lang "%s" was found', $message, $fromLanguageCode, $toLanguageCode), 0, $e);
    }

}
