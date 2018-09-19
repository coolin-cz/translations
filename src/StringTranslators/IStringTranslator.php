<?php

declare(strict_types=1);

namespace Freelo\Translations\StringTranslators;

interface IStringTranslator
{

    public function translate(string $message, string $fromLanguage, string $toLanguage): string;

}
