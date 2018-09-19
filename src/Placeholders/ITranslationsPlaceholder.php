<?php

declare(strict_types=1);

namespace Freelo\Translations\Placeholders;

use Freelo\Translations\ValueObjects\Language;

interface ITranslationsPlaceholder
{

    public function create(string $placeholder, string $languageCode = Language::LANGUAGE_CODE_CS): string;


    public function isValid(string $placeholder): bool;

}
