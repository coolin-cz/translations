<?php

declare(strict_types=1);

namespace Freelo\Translations\Extractors;

final class TranslationsExtractorsChain implements ITranslationsExtractor
{

    /** @var ITranslationsExtractor[] */
    private $extractors = [];


    public function addExtractor(ITranslationsExtractor $extractor): void
    {
        $this->extractors[] = $extractor;
    }


    public function extract(array $dirs, array $excludeDirs = []): array
    {
        $extractedStrings = [];

        foreach ($this->extractors as $extractor) {
            $extractedStrings = array_merge($extractedStrings, $extractor->extract($dirs, $excludeDirs));
        }

        return $extractedStrings;
    }

}
