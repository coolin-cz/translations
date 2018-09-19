<?php

declare(strict_types=1);

namespace Freelo\Translations\DictionariesCreators;

use Freelo\Translations\ValueObjects\Language;

interface ITranslationsDictionaryCreator
{

    /**
     * @param Language[] $languages
     */
    public function create(array $languages): void;

}
