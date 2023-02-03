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
class PhraseKeyTagCommandTest extends TestCase
{
    private MockObject&PhraseTagService $tagService;

    /**
     * @dataProvider tagProvider
     *
     * @param string[] $tag
     * @param string[] $newTags
     */
    public function testTag(?string $key, array $tag, array $newTags, int $return, string $message): void
    {
        $this->getTagService()
            ->expects(self::once())
            ->method('tag')
            ->with($key, $tag, $newTags)
            ->willReturn($return)
        ;

        $commandTester = $this->createCommandTester();
        $commandTester->execute([
            'command' => PhraseKeyTagCommand::getDefaultName(),
            '-k' => $key,
            '-t' => $tag,
            '--tag' => $newTags,
        ]);

        $this->assertSame($message, trim($commandTester->getDisplay()));
        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
    }

    public function testTagProviderException(): void
    {
        $this->getTagService()
            ->expects(self::once())
            ->method('tag')
            ->willThrowException(new ProviderException('something went wrong', new MockResponse()))
        ;

        $commandTester = $this->createCommandTester();
        $commandTester->execute([
            'command' => PhraseKeyTagCommand::getDefaultName(),
            '-t' => ['tag'],
            '--tag' => ['new-tag'],
        ]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertSame('something went wrong', trim($commandTester->getDisplay()));
    }

    public function tagProvider(): \Generator
    {
        yield 'tag no key single new tag' => [
            'key' => null,
            'tag' => ['messages'],
            'newTag' => ['new-tag'],
            'return' => 1,
            'message' => 'successfully tagged 1 keys with "new-tag"',
        ];

        yield 'key no tag single new tag' => [
            'key' => 'error.*',
            'tag' => [],
            'newTag' => ['new-tag'],
            'return' => 6,
            'message' => 'successfully tagged 6 keys with "new-tag"',
        ];

        yield 'key & tag & multiple new tag' => [
            'key' => 'error.*',
            'tag' => ['messages'],
            'newTag' => ['new-tag', 'another-new-tag'],
            'return' => 6,
            'message' => 'successfully tagged 6 keys with "new-tag, another-new-tag"',
        ];
    }

    private function createCommandTester(): CommandTester
    {
        $application = new Application();
        $application->add($this->createCommand());

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
