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

/**
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class PhraseKeyUntagCommand extends AbstractPhraseKeyCommand
{
    protected static $defaultName = 'phrase:keys:untag';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('batch untag keys in phrase')
            ->addOption('tag', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'tag(s) to remove from the matching keys')
            ->addUsage('-k error.* -t validators --tag validation --dry-run');
    }

    protected function executeCommand(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $queryKey */
        $queryKey = $input->getOption('query-key');
        /** @var string[] $queryTag */
        $queryTag = $input->getOption('query-tag');
        /** @var string[] $tag */
        $tag = $input->getOption('tag');

        try {
            $keys = $this->tagService->untag($queryKey, $queryTag, $tag);
        } catch (ProviderException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return Command::FAILURE;
        }

        $output->writeln(sprintf('<info>successfully untagged %d keys with "%s"</info>', $keys, implode(', ', $tag)));

        return Command::SUCCESS;
    }
}
