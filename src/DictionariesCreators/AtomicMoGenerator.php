<?php

declare(strict_types=1);

namespace Freelo\Translations\DictionariesCreators;

use Gettext\Generators\Mo;
use Gettext\Translations;

final class AtomicMoGenerator extends Mo
{

    public static function toFile(Translations $translations, $file, array $options = [])
    {
        $content = static::toString($translations, $options);

        return !(file_put_contents('nette.safe://' . $file, $content) === false);
    }

}
