<?php

declare(strict_types=1);

/*
 * This file is part of the Phrase Tag Bundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WickedOne\PhraseTagBundle\Tests\Unit\Service;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\Translation\Exception\MissingRequiredOptionException;
use Symfony\Component\Translation\Exception\UnsupportedSchemeException;
use WickedOne\PhraseTagBundle\Service\PhraseTaggerFactory;

/**
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class PhraseTaggerFactoryTest extends TestCase
{
    private MockObject&MockHttpClient $httpClient;
    private MockObject&LoggerInterface $logger;

    public function testCreate(): void
    {
        $dsn = 'phrase://PROJECT_ID:API_TOKEN@default:8080?userAgent=myProject';

        $options = [
            'base_uri' => 'https://api.phrase.com:8080/v2/projects/PROJECT_ID/',
            'headers' => [
                'Authorization' => 'token API_TOKEN',
                'User-Agent' => 'myProject',
            ],
        ];

        $this->getHttpClient()
            ->expects(self::once())
            ->method('withOptions')
            ->with($options);

        $this->createFactory()
            ->create($dsn);
    }

    public function testInvalidSchemeException(): void
    {
        $this->expectException(UnsupportedSchemeException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('The "https" scheme is not supported; supported schemes for translation provider "phrase" are: "phrase".');

        $this->createFactory()
            ->create('https://PROJECT_ID:API_TOKEN@default:8080?userAgent=myProject');
    }

    #[DataProvider('invalidDsnProvider')]
    public function testMissingUsernameOrPassword(string $dsn): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('please provide project id and api key');

        $this->createFactory()
            ->create($dsn);
    }

    public function testMissingRequiredOptionException(): void
    {
        $this->expectException(MissingRequiredOptionException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('The option "userAgent" is required but missing.');

        $this->createFactory()
            ->create('phrase://PROJECT_ID:API_TOKEN@default');
    }

    public static function invalidDsnProvider(): \Generator
    {
        yield 'no project id' => [
            'dsn' => 'phrase://:API_TOKEN@default',
        ];

        yield 'no api key' => [
            'dsn' => 'phrase://PROJECT_ID:@default:8080?userAgent=myProject',
        ];
    }

    private function createFactory(): PhraseTaggerFactory
    {
        return new PhraseTaggerFactory(
            $this->getHttpClient(),
            $this->getLogger()
        );
    }

    private function getHttpClient(): MockObject&MockHttpClient
    {
        return $this->httpClient ??= $this->createMock(MockHttpClient::class);
    }

    private function getLogger(): LoggerInterface&MockObject
    {
        return $this->logger ??= $this->createMock(LoggerInterface::class);
    }
}
