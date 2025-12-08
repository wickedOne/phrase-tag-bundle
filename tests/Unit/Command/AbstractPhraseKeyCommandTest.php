<?php

declare(strict_types=1);

/*
 * This file is part of the Phrase Tag Bundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WickedOne\PhraseTagBundle\Tests\Unit\Command;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\Translation\Exception\ProviderException;
use WickedOne\PhraseTagBundle\Command\PhraseKeyTagCommand;
use WickedOne\PhraseTagBundle\Service\PhraseTagService;

/**
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class AbstractPhraseKeyCommandTest extends TestCase
{
    private MockObject&PhraseTagService $tagService;

    /**
     * @param string[] $tag
     * @param string[] $return
     */
    #[DataProvider('listProvider')]
    public function testList(?string $key, array $tag, string $command, array $return, string $output): void
    {
        $this->getTagService()
            ->expects(self::once())
            ->method('list')
            ->with($key, $tag)
            ->willReturn($return)
        ;

        $commandTester = $this->createCommandTester();
        $commandTester->execute([
            'command' => $command,
            '-k' => $key,
            '-t' => $tag,
            '--dry-run' => null,
        ]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertSame($output, trim($commandTester->getDisplay()));
    }

    public function testListProviderException(): void
    {
        $this->getTagService()
            ->expects(self::once())
            ->method('list')
            ->willThrowException(new ProviderException('something went wrong', new MockResponse()))
        ;

        $commandTester = $this->createCommandTester();
        $commandTester->execute([
            'command' => 'phrase:keys:tag',
            '-t' => ['tag'],
            '--dry-run' => null,
        ]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertSame('something went wrong', trim($commandTester->getDisplay()));
    }

    public function testInputFailureNoDryRun(): void
    {
        $commandTester = $this->createCommandTester();
        $commandTester->execute([
            'command' => 'phrase:keys:tag',
            '-t' => ['current-tag'],
        ]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertSame('no tag provided', trim($commandTester->getDisplay()));
    }

    public static function listProvider(): \Generator
    {
        yield 'key no tag one hit' => [
            'key' => 'error.*',
            'tag' => [],
            'command' => 'phrase:keys:tag',
            'return' => [
                'error.general.back',
            ],
            'output' => 'your query would match the following keys (sample):
> error.general.back',
        ];

        yield 'tag no key no hits' => [
            'key' => null,
            'tag' => ['messages'],
            'command' => 'phrase:keys:tag',
            'return' => [],
            'output' => 'your query does not match any keys',
        ];

        yield 'tag and key multiple hits' => [
            'key' => 'error.*',
            'tag' => ['messages'],
            'command' => 'phrase:keys:tag',
            'return' => [
                'error.general.back',
                'error.general.cancel',
            ],
            'output' => 'your query would match the following keys (sample):
> error.general.back
> error.general.cancel',
        ];
    }

    private function createCommandTester(): CommandTester
    {
        $application = new Application();

        /* @phpstan-ignore function.alreadyNarrowedType */
        \is_callable([$application, 'addCommand']) ? $application->addCommand($this->createCommand()) : $application->add($this->createCommand());

        $command = $application->find('phrase:keys:tag');

        return new CommandTester($command);
    }

    private function createCommand(): PhraseKeyTagCommand
    {
        return new PhraseKeyTagCommand(
            $this->getTagService(),
        );
    }

    private function getTagService(): PhraseTagService&MockObject
    {
        return $this->tagService ??= $this->createMock(PhraseTagService::class);
    }
}
