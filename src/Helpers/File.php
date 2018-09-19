<?php

declare(strict_types=1);

namespace Freelo\Translations\Helpers;

use Freelo\Translations\Exceptions\RuntimeException;
use Nette\StaticClass;
use SplFileInfo;

final class File
{

    use StaticClass;


    /**
     * @throws RuntimeException
     */
    public static function getContents(SplFileInfo $file): string
    {
        $level = error_reporting(0);
        $content = file_get_contents($file->getPathname());
        error_reporting($level);

        if ($content === false) {
            $error = error_get_last();
            throw new RuntimeException($error['message']);
        }

        return $content;
    }

}
