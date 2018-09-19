<?php

declare(strict_types=1);

namespace Freelo\Translations\Commands\Translator;

use Freelo\Translations\DictionariesCreators\ITranslationsDictionaryCreator;
use Freelo\Translations\ValueObjects\Language;
use Nette\Utils\Validators;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateMoFilesCommand extends Command
{

    /** @var ITranslationsDictionaryCreator */
    private $dictionaryCreator;

    /** @var array */
    private $languages;


    public function __construct(ITranslationsDictionaryCreator $dictionaryCreator,
                                array $languages)
    {
        parent::__construct();

        $this->dictionaryCreator = $dictionaryCreator;

        \assert(Validators::everyIs($languages, Language::class));
        $this->languages = $languages;
    }


    protected function configure()
    {
        parent::configure();

        $this->setName('freelo:translations:create-mo-files')
            ->setDescription('Create dictionary files for all languages.');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->dictionaryCreator->create($this->languages);

        return 0;
    }

}