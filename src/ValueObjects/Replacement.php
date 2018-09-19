<?php

declare(strict_types=1);

namespace Freelo\Translations\ValueObjects;

final class Replacement
{

    /** @var string */
    private $message;

    /** @var string */
    private $placeholder;

    /** @var string */
    private $file;


    public function __construct(string $message, string $placeholder, string $file)
    {
        $this->message = $message;
        $this->placeholder = $placeholder;
        $this->file = $file;
    }


    public function getMessage(): string
    {
        return $this->message;
    }


    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }


    public function getFile(): string
    {
        return $this->file;
    }

}
