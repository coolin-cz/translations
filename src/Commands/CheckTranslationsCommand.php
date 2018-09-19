<?php

declare(strict_types=1);

namespace Freelo\Translations\Commands\Translator;

use Freelo\Translations\Exceptions\UnextractedStringFoundException;
use Freelo\Translations\ExtractionCheckers\ITranslationsExtractionChecker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CheckTranslationsCommand extends Command
{

    /** @var ITranslationsExtractionChecker */
    private $extractionChecker;

    /** @var array */
    private $dirs;

    /** @var array */
    private $excludeDirs;


    public function __construct(ITranslationsExtractionChecker $extractionChecker,
                                array $dirs,
                                array $excludeDirs = [])
    {
        parent::__construct();

        $this->extractionChecker = $extractionChecker;
        $this->dirs = $dirs;
        $this->excludeDirs = $excludeDirs;
    }


    protected function configure()
    {
        parent::configure();

        $this->setName('freelo:translations:check')
            ->setDescription('Check if all strings from source codes are extracted and replaced with placeholders.');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->extractionChecker->check($this->dirs, $this->excludeDirs);

            return 0;

        } catch (UnextractedStringFoundException $e) {
            $output->writeln($e->getMessage());
            $output->writeln('You have to run freelo:translations:extract command before commiting.');

        } catch (\Throwable $e) {
            $output->writeln($e->getMessage());
        }

        return 1;
    }

}