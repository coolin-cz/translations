<?php

declare(strict_types=1);

namespace Freelo\Translations\Repositories;

use Freelo\Translations\Exceptions\NoPlaceholderFound;
use Freelo\Translations\Exceptions\NoTranslatedMessageFound;
use PDO;

final class PdoTranslationsRepository implements ITranslationsRepository
{

    /** @var IPdoTranslationsFactory */
    private $pdoTranslationsFactory;

    /** @var string */
    private $table;

    /** @var string */
    private $appName;

    /** @var PDO */
    private $pdo;

    /** @var PdoTranslationsCredentials */
    private $credentials;


    public function __construct(IPdoTranslationsFactory $pdoTranslationsFactory,
                                string $appName,
                                PdoTranslationsCredentials $credentials)
    {
        $this->table = $credentials->getTable();
        $this->appName = $appName;
        $this->pdoTranslationsFactory = $pdoTranslationsFactory;
        $this->credentials = $credentials;
    }


    public function getTranslatedMessage(string $message, string $fromLanguageCode, string $toLanguageCode): string
    {
        $row = $this->queryFetchSingleRow("SELECT lang_{$toLanguageCode}_0 as translated_message FROM $this->table WHERE lang_{$fromLanguageCode}_0 = ? AND app_name = ?", [$message, $this->appName]);

        if (empty($row)) {
            throw new NoTranslatedMessageFound($message, $fromLanguageCode, $toLanguageCode);
        }

        return $row['translated_message'];
    }


    public function findTranslatedMessageByPlaceholder(string $message, string $placeholder, string $fromLanguageCode, string $toLanguageCode): ?string
    {
        $row = $this->queryFetchSingleRow(
            "SELECT lang_{$toLanguageCode}_0 as translated_message FROM $this->table WHERE lang_{$fromLanguageCode}_0 <> ? AND placeholder = ? AND app_name = ?",
            [$message, $placeholder, $this->appName]
        );

        return !empty($row) ? $row['translated_message'] : null;
    }


    public function findPlaceholderByMessageAndPrefix(string $message, string $prefix, string $fromLanguageCode): ?string
    {
        $row = $this->queryFetchSingleRow(
            "SELECT placeholder FROM $this->table WHERE lang_{$fromLanguageCode}_0 = ? AND placeholder LIKE ? AND app_name = ?",
            [$message, $prefix, $this->appName]
        );

        return !empty($row) ? $row['placeholder'] : null;
    }


    public function getPlaceholder(string $placeholder): string
    {
        $row = $this->queryFetchSingleRow("SELECT placeholder FROM $this->table WHERE placeholder = ? AND app_name = ?", [$placeholder, $this->appName]);

        if (empty($row)) {
            throw new NoPlaceholderFound($placeholder);
        }

        return $row['placeholder'];
    }


    private function queryFetchSingleRow(string $query, array $params = null): array
    {
        $statement = $this->getPdo()->prepare($query);
        $statement->execute($params);
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        return \is_array($result) ? $result : [];
    }


    private function getPdo(): PDO
    {
        if ($this->pdo === null) {
            $this->pdo = $this->pdoTranslationsFactory->create($this->credentials);
        }

        return $this->pdo;
    }

}
