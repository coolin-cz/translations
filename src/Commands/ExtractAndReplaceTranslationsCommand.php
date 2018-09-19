<?php

declare(strict_types=1);

namespace Freelo\Translations\Commands\Translator;

use Freelo\Translations\Extractors\ITranslationsExtractor;
use Freelo\Translations\Persisters\IReplacementsPersister;
use Freelo\Translations\Replacers\ITranslationsReplacer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ExtractAndReplaceTranslationsCommand extends Command
{

    /** @var ITranslationsExtractor */
    private $extractor;

    /** @var ITranslationsReplacer */
    private $replacer;

    /** @var IReplacementsPersister */
    private $replacementsPersister;

    /** @var array */
    private $dirs;

    /** @var array */
    private $excludeDirs;

    /** @var LoggerInterface */
    private $logger;


    public function __construct(ITranslationsExtractor $extractor,
                                ITranslationsReplacer $replacer,
                                IReplacementsPersister $replacementsPersister,
                                array $dirs,
                                array $excludeDirs,
                                LoggerInterface $logger)
    {
        parent::__construct();

        $this->extractor = $extractor;
        $this->replacer = $replacer;
        $this->replacementsPersister = $replacementsPersister;
        $this->dirs = $dirs;
        $this->excludeDirs = $excludeDirs;
        $this->logger = $logger;
    }


    protected function configure()
    {
        parent::configure();

        $this->setName('freelo:translations:extract-replace')
            ->setDescription('Extract, replace and save in DB all untranslated strings from source code.');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $progress = null;

        try {
            $extractedStrings = $this->extractor->extract($this->dirs, $this->excludeDirs);
            $output->writeln(sprintf('%d translateable strings found.', \count($extractedStrings)));

            $replacements = $this->replacer->replace($extractedStrings);

            $progress = new ProgressBar($output, \count($replacements));
            $progress->start();

            $this->replacementsPersister->persist($replacements, function () use ($progress) {
                $progress->advance();
            });

        } catch (\Throwable $e) {
            $output->writeln($e->getMessage());
            $this->logger->critical($e);

            return 1;
        } finally {
            if ($progress !== null) {
                $progress->finish();
                $output->writeln('');
            }
        }

        return 0;
    }

}