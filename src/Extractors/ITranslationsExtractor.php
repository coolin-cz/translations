<?php

declare(strict_types=1);

namespace Freelo\Translations\Extractors;

use Freelo\Translations\ValueObjects\ExtractedString;

interface ITranslationsExtractor
{

    /**
     * @return ExtractedString[]
     */
    public function extract(array $dirs, array $excludeDirs = []): array;

}
