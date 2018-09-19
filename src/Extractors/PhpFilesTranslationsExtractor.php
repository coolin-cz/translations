<?php

declare(strict_types=1);

namespace Freelo\Translations\Extractors;

use Freelo\Translations\Exceptions\RuntimeException;
use Freelo\Translations\Helpers\File;
use Freelo\Translations\ValueObjects\ExtractedString;
use Nette\Utils\Finder;
use Nette\Utils\Strings;
use SplFileInfo;

final class PhpFilesTranslationsExtractor implements ITranslationsExtractor
{

    private const FILE_MASK = '*.php';

    /**
     * https://regex101.com/r/z2kz6O/1
     * @var string
     */
    private $regex = '/translate\(([\'])(?:(?=(\\\\?))\2.)*?\1/u';


    public function extract(array $dirs, array $excludeDirs = []): array
    {
        $strings = [];
        /** @var Finder $finder */
        $finder = Finder::findFiles(self::FILE_MASK)->from($dirs)->exclude($excludeDirs);

        foreach ($finder as $file) {
            /** @var SplFileInfo $file */
            $strings = array_merge($strings, $this->findStringsInFile($file));
        }

        return $strings;
    }


    /**
     * @return ExtractedString[]
     * @throws RuntimeException
     */
    private function findStringsInFile(SplFileInfo $file): array
    {
        $content = File::getContents($file);
        $matches = Strings::matchAll($content, $this->regex);

        $strings = [];

        foreach ($matches as $match) {
            $string = Strings::substring($match[0], 11, -1);
            $realpath = $file->getRealPath();

            if (!$realpath) {
                throw new RuntimeException(sprintf('File not found: %s', $realpath));
            }

            $strings[] = new ExtractedString($string, $match[0], $realpath);
        }

        return $strings;
    }

}
