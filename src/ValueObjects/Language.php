<?php

declare(strict_types=1);

namespace Freelo\Translations\ValueObjects;

use Nette\Utils\Validators;

final class Language
{

    public const LANGUAGE_CODE_CS = 'cs';

    public const LANGUAGE_CODE_EN = 'en';

    /** @var string */
    private $languageCode;

    /** @var string */
    private $locale;

    /** @var Plural */
    private $plural;


    public function __construct(string $locale, Plural $plural)
    {
        $this->setLocale($locale);
        $this->plural = $plural;
    }


    private function setLocale(string $locale): void
    {
        \assert(Validators::is($locale, 'string:5'));

        $this->locale = $locale;
        $this->languageCode = substr($locale, 0, 2);
    }


    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }


    public function getLocale(): string
    {
        return $this->locale;
    }


    public function getPlural(): Plural
    {
        return $this->plural;
    }

}
