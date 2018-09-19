<?php

declare(strict_types=1);

namespace Freelo\Translations\ValueObjects;

final class Translation
{

    /** @var string */
    private $message;

    /** @var string */
    private $placeholder;

    /** @var string */
    private $occurence;

    /** @var array */
    private $translations;


    public function __construct(string $message, string $placeholder, string $occurence, array $translations = [])
    {
        $this->message = $message;
        $this->placeholder = $placeholder;
        $this->occurence = $occurence;
        $this->translations = $translations;
    }


    public function getMessage(): string
    {
        return $this->message;
    }


    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }


    public function getOccurence(): string
    {
        return $this->occurence;
    }


    public function getTranslations(): array
    {
        return $this->translations;
    }

}
