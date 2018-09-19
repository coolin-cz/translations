<?php

declare(strict_types=1);

namespace Freelo\Translations\ValueObjects;

final class Plural
{

    /** @var int */
    private $count;

    /** @var string */
    private $rule;


    public function __construct(int $count, string $rule)
    {
        $this->count = $count;
        $this->rule = $rule;
    }


    public function getCount(): int
    {
        return $this->count;
    }


    public function getRule(): string
    {
        return $this->rule;
    }

}
