<?php

declare(strict_types=1);

namespace Freelo\Translations\ValueObjects;

final class ExtractedString
{

    /** @var string */
    private $value;

    /** @var string */
    private $file;

    /** @var string */
    private $matched;


    public function __construct(string $value, string $matched, string $file)
    {
        $this->value = trim($value);
        $this->matched = $matched;
        $this->file = $file;
    }


    public function getValue(): string
    {
        return $this->value;
    }


    public function getMatched(): string
    {
        return $this->matched;
    }


    public function getFile(): string
    {
        return $this->file;
    }

}
