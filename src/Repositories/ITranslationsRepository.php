<?php

declare(strict_types=1);

namespace Freelo\Translations\Repositories;

use Freelo\Translations\Exceptions\NoPlaceholderFound;
use Freelo\Translations\Exceptions\NoTranslatedMessageFound;

interface ITranslationsRepository
{

    /**
     * @throws NoTranslatedMessageFound
     */
    public function getTranslatedMessage(string $message, string $fromLanguageCode, string $toLanguageCode): string;


    public function findTranslatedMessageByPlaceholder(string $message, string $placeholder, string $fromLanguageCode, string $toLanguageCode): ?string;


    public function findPlaceholderByMessageAndPrefix(string $message, string $prefix, string $fromLanguageCode): ?string;


    /**
     * @throws NoPlaceholderFound
     */
    public function getPlaceholder(string $placeholder): string;

}
