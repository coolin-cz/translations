<?php

declare(strict_types=1);

namespace Freelo\Translations\Repositories;

use PDO;

final class PdoTranslationsFactory implements IPdoTranslationsFactory
{

    public function create(PdoTranslationsCredentials $credentials): PDO
    {
        return new PDO($credentials->getDsn(), $credentials->getUser(), $credentials->getPassword(), $credentials->getOptions());
    }

}
