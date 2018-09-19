<?php

declare(strict_types=1);

namespace Freelo\Translations\Persisters;

use DateTimeImmutable;
use Freelo\Translations\Exceptions\NoPlaceholderFound;
use Freelo\Translations\Placeholders\ITranslationsPlaceholder;
use Freelo\Translations\Repositories\ITranslationsRepository;
use Freelo\Translations\StringTranslators\IStringTranslator;
use Freelo\Translations\ValueObjects\Language;
use Freelo\Translations\ValueObjects\Replacement;
use Freelo\Translations\ValueObjects\Translation;

final class FreeloReplacementsPersister implements IReplacementsPersister
{

    /** @var ITranslationsPersister */
    private $translationsPersister;

    /** @var ITranslationsRepository */
    private $translationsRepository;

    /** @var ITranslationsPlaceholder */
    private $placeholder;

    /** @var IStringTranslator */
    private $stringTranslator;

    /** @var string */
    private $baseDir;

    /** @var string */
    private $sourceCodeLanguageCode;

    /** @var Language[] */
    private $languages;


    public function __construct(ITranslationsPersister $translationsPersister,
                                ITranslationsRepository $translationsRepository,
                                ITranslationsPlaceholder $placeholder,
                                IStringTranslator $stringTranslator,
                                string $baseDir,
                                string $sourceCodeLanguageCode,
                                array $languages)
    {
        $this->translationsPersister = $translationsPersister;
        $this->translationsRepository = $translationsRepository;
        $this->placeholder = $placeholder;
        $this->stringTranslator = $stringTranslator;
        $this->baseDir = $baseDir;
        $this->sourceCodeLanguageCode = $sourceCodeLanguageCode;
        $this->languages = $languages;
    }


    /**
     * @param Replacement[] $replacements
     */
    public function persist(array $replacements, callable $callback = null): void
    {
        $this->translationsPersister->reset();

        $dateFound = new DateTimeImmutable();

        foreach ($replacements as $replacement) {
            if ($this->placeholder->isValid($replacement->getMessage())) {
                $this->handleValidPlaceholder($replacement, $dateFound);
            } else {
                $this->handleNewMessage($replacement, $dateFound);
            }

            if (\is_callable($callback)) {
                $callback($replacement);
            }
        }
    }


    private function handleValidPlaceholder(Replacement $replacement, DateTimeImmutable $dateFound): void
    {
        $itemsUpdated = $this->translationsPersister->updateDateFoundAndOccurences(
            $replacement->getPlaceholder(),
            $this->trimFilename($replacement->getFile()),
            $dateFound
        );

        if ($itemsUpdated === 0) {
            $this->translationsPersister->persist(
                new Translation(
                    $replacement->getMessage(),
                    $replacement->getPlaceholder(),
                    $this->trimFilename($replacement->getFile())
                ),
                $dateFound
            );
        }
    }


    private function handleNewMessage(Replacement $replacement, DateTimeImmutable $dateFound): void
    {
        // check if placeholder is already in DB, if yes - add another occurence
        try {
            $this->translationsRepository->getPlaceholder($replacement->getPlaceholder());
            $this->translationsPersister->updateDateFoundAndOccurences(
                $replacement->getPlaceholder(),
                $this->trimFilename($replacement->getFile()),
                $dateFound
            );

            return;

        } catch (NoPlaceholderFound $e) {

        }

        // if not - translate and save it
        $translations = [];
        foreach ($this->languages as $language) {
            if ($this->sourceCodeLanguageCode === $language->getLanguageCode()) {
                $translations[sprintf('lang_%s_0', $language->getLanguageCode())] = $replacement->getMessage();
            } else {
                $translations[sprintf('lang_%s_0', $language->getLanguageCode())] = $this->stringTranslator->translate(
                    $replacement->getMessage(),
                    $this->sourceCodeLanguageCode,
                    $language->getLanguageCode()
                );
            }
        }

        $this->translationsPersister->persist(
            new Translation(
                $replacement->getMessage(),
                $replacement->getPlaceholder(),
                $this->trimFilename($replacement->getFile()),
                $translations
            ),
            $dateFound
        );
    }


    private function trimFilename(string $file): string
    {
        return str_replace($this->baseDir, '', $file);
    }

}
