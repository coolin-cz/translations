<?php

declare(strict_types=1);

namespace Freelo\Translations\Repositories;

use Nette\Utils\Validators;

final class PdoTranslationsCredentials
{

    private const DSN = 'dsn';

    private const PASSWORD = 'password';

    private const USER = 'user';

    private const TABLE = 'table';

    private const OPTIONS = 'options';

    /** @var array */
    private $credentials;


    public function __construct(array $credentials)
    {
        Validators::assertField($credentials, 'dsn', 'string:1..');
        Validators::assertField($credentials, 'password', 'string:1..');
        Validators::assertField($credentials, 'user', 'string:1..');
        Validators::assertField($credentials, 'table', 'string:1..');
        Validators::assertField($credentials, 'options', 'array');

        $this->credentials = $credentials;
    }


    public function getCredentials(): array
    {
        return $this->credentials;
    }


    public function getTable(): string
    {
        return $this->credentials[self::TABLE];
    }


    public function getDsn(): string
    {
        return $this->credentials[self::DSN];
    }


    public function getPassword(): string
    {
        return $this->credentials[self::PASSWORD];
    }


    public function getUser(): string
    {
        return $this->credentials[self::USER];
    }


    public function getOptions(): array
    {
        return $this->credentials[self::OPTIONS];
    }

}
