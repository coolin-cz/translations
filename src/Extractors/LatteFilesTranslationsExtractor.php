<?php

declare(strict_types=1);

namespace Freelo\Translations\Extractors;

use Freelo\Translations\Exceptions\RuntimeException;
use Freelo\Translations\Helpers\File;
use Freelo\Translations\ValueObjects\ExtractedString;
use Nette\Utils\Finder;
use Nette\Utils\Strings;
use SplFileInfo;

final class LatteFilesTranslationsExtractor implements ITranslationsExtractor
{

    private const FILE_MASK = '*.latte';

    /**
     * https://regex101.com/r/qMwjDs/1
     * @var string
     */
    private $macroRegex = '/{_([\'])(?:(?=(\\\\?))\2.)*?\1/u';

    /**
     * https://regex101.com/r/KaQFrE/1
     * @var string
     */
    private $filterRegex = '/\(([\'])(?:(?=(\\\\?))\2[^\'])*?\1\|translate/u';


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
     */
    private function findStringsInFile(SplFileInfo $file): array
    {
        $strings = [];
        $content = File::getContents($file);
        $strings = array_merge($strings, $this->findStringsWithRegex($file, $content, $this->filterRegex, 2, -11));
        $strings = array_merge($strings, $this->findStringsWithRegex($file, $content, $this->macroRegex, 3, -1));

        return $strings;
    }


    /**
     * @throws RuntimeException
     */
    private function findStringsWithRegex(SplFileInfo $file, string $content, string $regex, int $start, int $length): array
    {
        $strings = [];
        $matches = Strings::matchAll($content, $regex);

        foreach ($matches as $match) {
            $string = Strings::substring($match[0], $start, $length);
            $realpath = $file->getRealPath();

            if (!$realpath) {
                throw new RuntimeException(sprintf('File not found: %s', $realpath));
            }

            $strings[] = new ExtractedString($string, $match[0], $realpath);
        }

        return $strings;
    }

}
