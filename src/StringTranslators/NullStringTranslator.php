<?php

declare(strict_types=1);

namespace Freelo\Translations\StringTranslators;

final class NullStringTranslator implements IStringTranslator
{

    public function translate(string $message, string $fromLanguage, string $toLanguage): string
    {
        return '';
    }

}
