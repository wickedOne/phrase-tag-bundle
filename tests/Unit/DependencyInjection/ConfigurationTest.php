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

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use WickedOne\PhraseTagBundle\DependencyInjection\Configuration;
use WickedOne\PhraseTagBundle\DependencyInjection\WickedOnePhraseTagExtension;

/**
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    public function testFullConfiguration(): void
    {
        $expectedConfiguration = [
            'dsn' => 'phrase://PROJECT_ID:API_TOKEN@default?userAgent=myProject',
        ];

        $formats = array_map(
            static function ($path) {
                return __DIR__.'/../../Stub/'.$path;
            },
            [
                'config/full.yaml',
                'config/full.php',
            ]
        );

        foreach ($formats as $format) {
            $this->assertProcessedConfigurationEquals($expectedConfiguration, [$format]);
        }
    }

    protected function getContainerExtension(): ExtensionInterface
    {
        return new WickedOnePhraseTagExtension();
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }
}
