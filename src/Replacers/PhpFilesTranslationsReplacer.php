<?php

declare(strict_types=1);

namespace Freelo\Translations\Replacers;

use Freelo\Translations\Helpers\File;
use Freelo\Translations\Placeholders\ITranslationsPlaceholder;
use Freelo\Translations\ValueObjects\ExtractedString;
use Freelo\Translations\ValueObjects\Replacement;

final class PhpFilesTranslationsReplacer implements ITranslationsReplacer
{

    private const PHP_EXTENSION = 'php';

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
            if ($file->getExtension() !== self::PHP_EXTENSION) {
                continue;
            }

            $placeholder = $extractedString->getValue();
            if (!$this->placeholder->isValid($placeholder)) {
                $placeholder = $this->placeholder->create($placeholder);
            }

            $replace = "translate('{$placeholder}'";
            if ($extractedString->getMatched() !== $replace) {
                $content = File::getContents($file);
                $content = str_replace($extractedString->getMatched(), $replace, $content);
                file_put_contents($extractedString->getFile(), $content);
            }

            $replacements[] = new Replacement($extractedString->getValue(), $placeholder, $extractedString->getFile());
        }

        return $replacements;
    }

}
