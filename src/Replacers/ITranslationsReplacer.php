<?php

declare(strict_types=1);

namespace Freelo\Translations\Replacers;

use Freelo\Translations\ValueObjects\ExtractedString;
use Freelo\Translations\ValueObjects\Replacement;

interface ITranslationsReplacer
{

    /**
     * @param ExtractedString[] $extractedStrings
     *
     * @return Replacement[]
     */
    public function replace(array $extractedStrings): array;

}
