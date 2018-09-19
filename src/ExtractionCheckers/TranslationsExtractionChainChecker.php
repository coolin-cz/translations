<?php

declare(strict_types=1);

namespace Freelo\Translations\ExtractionCheckers;

use Freelo\Translations\Exceptions\UnextractedStringFoundException;
use Freelo\Translations\Extractors\ITranslationsExtractor;
use Freelo\Translations\Placeholders\ITranslationsPlaceholder;

final class TranslationsExtractionChainChecker implements ITranslationsExtractionChecker
{

    /** @var ITranslationsExtractor[] */
    private $extractors = [];

    /** @var ITranslationsPlaceholder */
    private $placeholder;


    public function __construct(ITranslationsPlaceholder $placeholder)
    {
        $this->placeholder = $placeholder;
    }


    public function addExtractor(ITranslationsExtractor $extractor): void
    {
        $this->extractors[] = $extractor;
    }


    public function check(array $dirs, array $excludeDirs = []): bool
    {
        foreach ($this->extractors as $extractor) {
            $extractedStrings = $extractor->extract($dirs, $excludeDirs);

            foreach ($extractedStrings as $extratedString) {
                if (!$this->placeholder->isValid($extratedString->getValue())) {
                    throw new UnextractedStringFoundException(sprintf("Unextracted string '%s' found in '%s'.", $extratedString->getValue(), $extratedString->getFile()));
                }
            }
        }

        return true;
    }

}
