<?php

declare(strict_types=1);

namespace Freelo\Translations\Replacers;

use Freelo\Translations\Exceptions\RuntimeException;
use Freelo\Translations\Helpers\File;
use Freelo\Translations\Placeholders\ITranslationsPlaceholder;
use Freelo\Translations\ValueObjects\ExtractedString;
use Freelo\Translations\ValueObjects\Replacement;

final class LatteFilesTranslationsReplacer implements ITranslationsReplacer
{

    private const LATTE_EXTENSION = 'latte';

    /** @var ITranslationsPlaceholder */
    private $placeholder;


    public function __construct(ITranslationsPlaceholder $placeholder)
    {
        $this->placeholder = $placeholder;
    }


    /**
     * @param ExtractedString[] $extractedStrings
     *
     * @return Replacement[]
     */
    public function replace(array $extractedStrings): array
    {
        $replacements = [];

        foreach ($extractedStrings as $extractedString) {
            $file = new \SplFileInfo($extractedString->getFile());
            if ($file->getExtension() !== self::LATTE_EXTENSION) {
                continue;
            }

            $placeholder = $extractedString->getValue();
            if (!$this->placeholder->isValid($placeholder)) {
                $placeholder = $this->placeholder->create($placeholder);
            }

            $replaceTranslate = "{_'{$placeholder}'";
            $replaceFilter = "('{$placeholder}'|translate";

            if ($extractedString->getValue() !== $placeholder) {
                $content = File::getContents($file);
                if (strpos($extractedString->getMatched(), '|translate') !== false) {
                    $replace = $replaceFilter;

                } elseif (strpos($extractedString->getMatched(), "{_'") !== false) {
                    $replace = $replaceTranslate;

                } else {
                    throw new RuntimeException(sprintf(
                        'Cannot replace matched extracted string: %s, occurences: %s',
                        $extractedString->getMatched(),
                        $extractedString->getFile()
                    ));
                }

                $content = str_replace($extractedString->getMatched(), $replace, $content);
                file_put_contents($extractedString->getFile(), $content);
            }

            $replacements[] = new Replacement($extractedString->getValue(), $placeholder, $extractedString->getFile());
        }

        return $replacements;
    }

}
