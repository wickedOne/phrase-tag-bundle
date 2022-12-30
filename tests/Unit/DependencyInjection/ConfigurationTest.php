<?php

declare(strict_types=1);

/*
 * This file is part of the Phrase Translation Bundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WickedOne\PhraseTranslationBundle\Tests\Unit\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use WickedOne\PhraseTranslationBundle\DependencyInjection\Configuration;
use WickedOne\PhraseTranslationBundle\DependencyInjection\WickedOnePhraseTranslationExtension;

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
                'config/full.xml',
                'config/full.php',
            ]
        );

        foreach ($formats as $format) {
            $this->assertProcessedConfigurationEquals($expectedConfiguration, [$format]);
        }
    }

    protected function getContainerExtension(): ExtensionInterface
    {
        return new WickedOnePhraseTranslationExtension();
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }
}
