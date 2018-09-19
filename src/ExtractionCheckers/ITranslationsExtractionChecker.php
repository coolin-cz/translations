<?php

declare(strict_types=1);

namespace Freelo\Translations\ExtractionCheckers;

use Freelo\Translations\Exceptions\UnextractedStringFoundException;

interface ITranslationsExtractionChecker
{

    /**
     * @throws UnextractedStringFoundException
     */
    public function check(array $dirs, array $excludeDirs = []): bool;

}
