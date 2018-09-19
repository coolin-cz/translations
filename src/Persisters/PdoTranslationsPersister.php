<?php

declare(strict_types=1);

namespace Freelo\Translations\Persisters;

use DateTimeInterface;
use Freelo\Translations\Repositories\IPdoTranslationsFactory;
use Freelo\Translations\Repositories\PdoTranslationsCredentials;
use Freelo\Translations\ValueObjects\Translation;
use PDO;

final class PdoTranslationsPersister implements ITranslationsPersister
{

    /** @var IPdoTranslationsFactory */
    private $pdoTranslationsFactory;

    /** @var PdoTranslationsCredentials */
    private $credentials;

    /** @var string */
    private $table;

    /** @var string */
    private $appName;

    /** @var PDO */
    private $pdo;


    public function __construct(IPdoTranslationsFactory $pdoTranslationsFactory,
                                PdoTranslationsCredentials $credentials,
                                string $appName)
    {
        $this->pdoTranslationsFactory = $pdoTranslationsFactory;
        $this->credentials = $credentials;
        $this->table = $credentials->getTable();
        $this->appName = $appName;
    }


    public function persist(Translation $translation, DateTimeInterface $dateFound): int
    {
        $params = [
            'usages' => $translation->getOccurence(),
            'placeholder' => $translation->getPlaceholder(),
            'file' => $translation->getOccurence(),
            'date_found' => $dateFound->format('Y-m-d'),
            'app_name' => $this->appName,
        ];
        $params = array_merge($params, $translation->getTranslations());
        $columns = array_keys($params);
        $values = array_fill(0, \count($params), '?');
        $query = sprintf('INSERT INTO %s (%s) VALUES (%s)', $this->table, implode(',', $columns), implode(',', $values));

        $statement = $this->getPdo()->prepare($query);
        $statement->execute(array_values($params));

        return (int) $this->getPdo()->lastInsertId();
    }


    public function updateDateFoundAndOccurences(string $placeholder, string $occurence, DateTimeInterface $dateFound): int
    {
        $params = ['placeholder' => $placeholder, 'datefound' => $dateFound->format('Y-m-d'), 'appname' => $this->appName];
        $statement = $this->getPdo()->prepare("UPDATE $this->table SET date_found = :datefound, usage_count = usage_count + 1, usages = CONCAT(usages, '{$occurence}') WHERE placeholder = :placeholder AND app_name = :appname");
        $statement->execute($params);

        return $statement->rowCount();
    }


    public function reset(): void
    {
        $this->getPdo()->exec("UPDATE $this->table SET usage_count = 0, usages = '' WHERE app_name = '{$this->appName}'");
        $this->getPdo()->exec("UPDATE $this->table SET date_found = NULL WHERE app_name = '{$this->appName}'");
    }


    private function getPdo(): PDO
    {
        if ($this->pdo === null) {
            $this->pdo = $this->pdoTranslationsFactory->create($this->credentials);
        }

        return $this->pdo;
    }

}
