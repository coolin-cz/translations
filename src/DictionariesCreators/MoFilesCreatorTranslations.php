<?php

declare(strict_types=1);

namespace Freelo\Translations\DictionariesCreators;

use Freelo\Translations\Exceptions\InvalidArgumentException;
use Freelo\Translations\Exceptions\RuntimeException;
use Freelo\Translations\Repositories\IPdoTranslationsFactory;
use Freelo\Translations\Repositories\PdoTranslationsCredentials;
use Freelo\Translations\ValueObjects\Language;
use Nette\Utils\Validators;
use PDO;
use Psr\Log\LoggerInterface;

final class MoFilesCreatorTranslations implements ITranslationsDictionaryCreator
{

    /** @var IPdoTranslationsFactory */
    private $pdoTranslationsFactory;

    /** @var string */
    private $table;

    /** @var string */
    private $appName;

    /** @var string */
    private $langsDir;

    /** @var PDO */
    private $pdo;

    /** @var PdoTranslationsCredentials */
    private $credentials;

    /** @var LoggerInterface */
    private $logger;


    public function __construct(IPdoTranslationsFactory $pdoTranslationsFactory,
                                string $appName,
                                string $langsDir,
                                PdoTranslationsCredentials $credentials,
                                LoggerInterface $logger)
    {
        $this->pdoTranslationsFactory = $pdoTranslationsFactory;
        $this->table = $credentials->getTable();
        $this->appName = $appName;
        $this->langsDir = $langsDir;
        $this->credentials = $credentials;
        $this->logger = $logger;

        if (!file_exists($this->langsDir)) {
            throw new InvalidArgumentException(sprintf("Langs directory '%s' does not exists. You have to create it.", $this->langsDir));
        }
    }


    /**
     * @param Language[] $languages
     * @throws RuntimeException
     */
    public function create(array $languages): void
    {
        \assert(Validators::everyIs($languages, Language::class));

        foreach ($languages as $language) {
            $cols = [];
            for ($i = 0; $i < $language->getPlural()->getCount(); $i++) {
                $cols[] = sprintf('lang_%s_%d', $language->getLanguageCode(), $i);
            }
            $query = sprintf("SELECT placeholder, %s FROM %s WHERE app_name = '%s'", implode(', ', $cols), $this->table, $this->appName);
            $statement = $this->getPdo()->query($query);

            if (!$statement) {
                $error = \json_encode($this->getPdo()->errorInfo());
                throw new RuntimeException($error === false ? sprintf('Query %s failed.', $query) : $error);
            }

            $translations = new FreeloTranslations();
            $translations->setPluralForms($language->getPlural()->getCount(), $language->getPlural()->getRule());

            while ($row = $statement->fetch(PDO::FETCH_NUM)) {
                $row = array_filter($row);
                $colsCount = \count($row);
                $hasPlural = $colsCount > 2;

                if ($colsCount <= 1 || $row[1] === '' || $row[1] === null) {
                    continue;
                }

                $translation = $translations->insert('', $row[0], $hasPlural ? $row[0] : '');
                $translation->setTranslation($row[1]);

                if ($hasPlural) {
                    $plural = [];
                    for ($i = 2; $i < $colsCount; $i++) {
                        $plural[] = $row[$i];
                    }

                    $translation->setPluralTranslations($plural);
                }
            }

            $translations->toMoFile($this->langsDir . "/{$language->getLanguageCode()}.mo");
        }
    }


    private function getPdo(): PDO
    {
        if ($this->pdo === null) {
            $this->pdo = $this->pdoTranslationsFactory->create($this->credentials);
        }

        return $this->pdo;
    }

}
