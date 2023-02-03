<?php

declare(strict_types=1);

/*
 * This file is part of the Phrase Tag Bundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WickedOne\PhraseTagBundle\Tests\Unit\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use WickedOne\PhraseTagBundle\Command\PhraseKeyTagCommand;
use WickedOne\PhraseTagBundle\Command\PhraseKeyUntagCommand;
use WickedOne\PhraseTagBundle\DependencyInjection\WickedOnePhraseTagExtension;
use WickedOne\PhraseTagBundle\Service\PhraseTaggerFactory;
use WickedOne\PhraseTagBundle\Service\PhraseTagService;

/**
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class WickedOnePhraseTagBundleExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setParameter('kernel.project_dir', __DIR__.'/../../../');
    }

    public function testLoadServices(): void
    {
        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasService(PhraseTaggerFactory::class);

        $this->assertContainerBuilderHasService(PhraseTagService::class);

        $definition = $this->container->getDefinition(PhraseTagService::class);

        /** @var array{0: \Symfony\Component\DependencyInjection\Reference, 1: string} $factory */
        $factory = $definition->getFactory();

        $this->assertSame(PhraseTaggerFactory::class, (string) $factory[0]);
        $this->assertSame('create', $factory[1]);

        $this->assertContainerBuilderHasService(PhraseKeyTagCommand::class);
        $this->assertContainerBuilderHasServiceDefinitionWithTag(PhraseKeyTagCommand::class, 'console.command');

        $command = $this->container->getDefinition(PhraseKeyTagCommand::class);

        self::assertSame(PhraseTagService::class, $command->getArgument(0)->getClass());

        $this->assertContainerBuilderHasService(PhraseKeyUntagCommand::class);
        $this->assertContainerBuilderHasServiceDefinitionWithTag(PhraseKeyUntagCommand::class, 'console.command');

        $command = $this->container->getDefinition(PhraseKeyUntagCommand::class);

        self::assertSame(PhraseTagService::class, $command->getArgument(0)->getClass());

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(PhraseTagService::class, 0, $this->getMinimalConfiguration()['dsn']);
    }

    protected function getContainerExtensions(): array
    {
        return [new WickedOnePhraseTagExtension()];
    }

    /**
     * @return array{
     *   dsn: string,
     * }
     */
    protected function getMinimalConfiguration(): array
    {
        return [
            'dsn' => 'phrase://PROJECT_ID:API_TOKEN@default?userAgent=myProject',
        ];
    }
}
