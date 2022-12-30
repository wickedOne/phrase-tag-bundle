<?php

declare(strict_types=1);

/*
 * This file is part of the Phrase Translation Bundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WickedOne\PhraseTranslationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use WickedOne\PhraseTranslationBundle\Command\PhraseKeyTagCommand;
use WickedOne\PhraseTranslationBundle\Command\PhraseKeyUntagCommand;
use WickedOne\PhraseTranslationBundle\Service\PhraseTaggerFactory;
use WickedOne\PhraseTranslationBundle\Service\PhraseTagService;

/**
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class WickedOnePhraseTranslationExtension extends Extension
{
    /**
     * @param array<string, string[]> $config
     */
    public function getConfiguration(array $config, ContainerBuilder $container): ConfigurationInterface
    {
        return new Configuration();
    }

    /**
     * {@inheritdoc}
     *
     * @param array<string, string[]> $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $xmlLoader = new XmlFileLoader($container, new FileLocator(\dirname(__DIR__).'/../config'));
        $xmlLoader->load('services.xml');

        $this->loadTagService($container, $config);
        $this->loadTagCommand($container, $config);
        $this->loadUntagCommand($container, $config);
    }

    /**
     * @param array<string, string[]> $config
     */
    private function loadTagService(ContainerBuilder $container, array $config): void
    {
        $definition = new Definition(PhraseTagService::class);
        $definition->setFactory([new Reference(PhraseTaggerFactory::class), 'create'])
            ->setArguments([$config['dsn']]);

        $container->setDefinition(PhraseTagService::class, $definition);
    }

    /**
     * @param array<string, string[]> $config
     */
    private function loadTagCommand(ContainerBuilder $container, array $config): void
    {
        $definition = (new Definition(PhraseKeyTagCommand::class))
            ->setArguments([$container->getDefinition(PhraseTagService::class)])
            ->addTag('console.command');

        $container->setDefinition(PhraseKeyTagCommand::class, $definition);
    }

    /**
     * @param array<string, string[]> $config
     */
    private function loadUntagCommand(ContainerBuilder $container, array $config): void
    {
        $definition = (new Definition(PhraseKeyUntagCommand::class))
            ->setArguments([$container->getDefinition(PhraseTagService::class)])
            ->addTag('console.command');

        $container->setDefinition(PhraseKeyUntagCommand::class, $definition);
    }
}
