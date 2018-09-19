<?php

declare(strict_types=1);

namespace Freelo\Translations\Placeholders;

use Freelo\Translations\Repositories\ITranslationsRepository;
use Freelo\Translations\StringTranslators\IStringTranslator;
use Freelo\Translations\ValueObjects\Language;
use Nette\Utils\Strings;

final class FreeloTranslationsPlaceholder implements ITranslationsPlaceholder
{

    private const PREFIX = 'FR';

    private const DELIMITER = '.';

    /** @var IStringTranslator */
    private $stringTranslator;

    /** @var ITranslationsRepository */
    private $translationsRepository;

    /** @var string */
    private $sourceCodeLanguageCode;


    public function __construct(IStringTranslator $stringTranslator,
                                ITranslationsRepository $translationsRepository,
                                string $sourceCodeLanguageCode)
    {
        $this->stringTranslator = $stringTranslator;
        $this->translationsRepository = $translationsRepository;
        $this->sourceCodeLanguageCode = $sourceCodeLanguageCode;
    }


    public function create(string $message, string $languageCode = null): string
    {
        if ($languageCode === null) {
            $languageCode = $this->sourceCodeLanguageCode;
        }

        $placeholder = $this->stringTranslator->translate($message, $languageCode, Language::LANGUAGE_CODE_EN);
        $placeholder = Strings::webalize($placeholder, 'A-Z');
        $placeholder = str_replace('-', self::DELIMITER, $placeholder);
        $placeholder = strtoupper($placeholder);
        $placeholder = sprintf('%s%s%s', self::PREFIX, self::DELIMITER, $placeholder);

        // check if one placeholder can have more meanings in language defined by $languageCode
        // if yes - add random string to it
        $translatedMessage = $this->translationsRepository->findTranslatedMessageByPlaceholder($message, $placeholder, $languageCode, Language::LANGUAGE_CODE_EN);

        if ($translatedMessage !== null) {
            $placeholderFromDb = $this->translationsRepository->findPlaceholderByMessageAndPrefix($message, self::PREFIX . self::DELIMITER, $languageCode);
            if ($placeholderFromDb !== null) {
                $placeholder = $placeholderFromDb;
            } else {
                $placeholder .= self::DELIMITER . self::generateRandom();
            }
        }

        return $placeholder;
    }


    private static function generateRandom(int $length = 16): string
    {
        return bin2hex(random_bytes($length));
    }


    public function isValid(string $placeholder): bool
    {
        return Strings::match($placeholder, sprintf('~^%s.~', self::PREFIX)) !== null;
    }

}
