<?php

declare(strict_types=1);

namespace Freelo\Translations\Repositories;

use PDO;

interface IPdoTranslationsFactory
{

    public function create(PdoTranslationsCredentials $credentials): PDO;

}
