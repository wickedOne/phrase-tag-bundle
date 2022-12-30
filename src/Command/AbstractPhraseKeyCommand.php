<?php

declare(strict_types=1);

/*
 * This file is part of the Phrase Translation Bundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WickedOne\PhraseTranslationBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\Exception\ProviderException;
use WickedOne\PhraseTranslationBundle\Service\PhraseTagService;

/**
 * @author wicliff <wicliff.wolda@gmail.com>
 */
abstract class AbstractPhraseKeyCommand extends Command
{
    public function __construct(
        protected readonly PhraseTagService $tagService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDefinition([
                new InputOption('query-key', 'k', InputOption::VALUE_OPTIONAL, 'keys matching this query (wildcard \'*\' supported)'),
                new InputOption('query-tag', 't', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'key tagged with these tag(s)'),
                new InputOption('dry-run', null, InputOption::VALUE_NONE, 'list the first 100 matches'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $key = $input->getOption('query-key');
        $tags = $input->getOption('query-tag');

        if (null === $key && [] === $tags) {
            $output->writeln('<error>no query parameters provided</error>');

            return Command::FAILURE;
        }

        if (true === $input->getOption('dry-run')) {
            return $this->list($output, $key, $tags);
        }

        if ([] === $input->getOption('tag')) {
            $output->writeln('<error>no tag provided</error>');

            return Command::FAILURE;
        }

        return $this->executeCommand($input, $output);
    }

    abstract protected function executeCommand(InputInterface $input, OutputInterface $output): int;

    /**
     * @param string[] $tags
     */
    private function list(OutputInterface $output, ?string $key, array $tags): int
    {
        try {
            $result = $this->tagService->list($key, $tags);
        } catch (ProviderException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return Command::FAILURE;
        }

        if ([] === $result) {
            $output->writeln('<info>your query does not match any keys</info>');

            return Command::SUCCESS;
        }

        $output->writeln('<info>your query would match the following keys (sample):</info>');

        foreach ($result as $item) {
            $output->writeln(sprintf('<comment>></comment> <info>%s</info>', $item));
        }

        return Command::SUCCESS;
    }
}
