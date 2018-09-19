<?php

declare(strict_types=1);

namespace Freelo\Translations\DictionariesCreators;

use Gettext\Translations;

class FreeloTranslations extends Translations
{

    public function __call($name, $arguments)
    {
        if ($name === 'toMoFile') {
            return AtomicMoGenerator::toFile($this, ...$arguments);
        }

        return parent::__call($name, $arguments);
    }

}
