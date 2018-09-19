<?php

declare(strict_types=1);

namespace Freelo\Translations;

use Consistence\Enum\Enum;

final class FileTypesEnum extends Enum
{

    public const TYPE_LATTE = 'latte';

    public const TYPE_PHP = 'php';

    public const TYPE_LATTE_HELPER = 'latte-helper';

}
