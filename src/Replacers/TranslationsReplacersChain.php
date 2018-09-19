<?php

declare(strict_types=1);

namespace Freelo\Translations\Replacers;

final class TranslationsReplacersChain implements ITranslationsReplacer
{

    /** @var ITranslationsReplacer[] */
    private $replacers = [];


    public function addReplacer(ITranslationsReplacer $replacer)
    {
        $this->replacers[] = $replacer;
    }


    public function replace(array $extractedStrings): array
    {
        $replacements = [];

        foreach ($this->replacers as $replacer) {
            $replacements = array_merge($replacements, $replacer->replace($extractedStrings));
            sleep(1);
        }

        return $replacements;
    }

}
