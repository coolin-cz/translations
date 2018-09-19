<?php

declare(strict_types=1);

namespace Freelo\Translations\Persisters;

use DateTimeInterface;
use Freelo\Translations\ValueObjects\Translation;

interface ITranslationsPersister
{

    public function persist(Translation $translation, DateTimeInterface $dateFound): int;


    public function updateDateFoundAndOccurences(string $placeholder, string $occurence, DateTimeInterface $dateFound): int;


    public function reset(): void;

}
