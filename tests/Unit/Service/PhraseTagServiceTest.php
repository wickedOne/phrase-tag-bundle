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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\Translation\Exception\ProviderException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use WickedOne\PhraseTagBundle\Service\PhraseTagService;

/**
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class PhraseTagServiceTest extends TestCase
{
    private MockObject&HttpClientInterface $httpClient;
    private MockObject&LoggerInterface $logger;

    /**
     * @dataProvider listProvider
     *
     * @param string[] $tags
     */
    public function testList(?string $key, array $tags, string $responseContent): void
    {
        $responses = [
            'list keys' => function (string $method, string $url) use ($key, $tags, $responseContent): ResponseInterface {
                $parts = [
                    'page' => '1',
                    'per_page' => '100',
                    'q' => $this->query($key, $tags),
                ];

                $this->assertSame('GET', $method);
                $this->assertSame('https://api.phrase.com/api/v2/projects/1/keys?'.http_build_query($parts, encoding_type: \PHP_QUERY_RFC3986), $url);

                return new MockResponse($responseContent);
            },
        ];

        $provider = $this->createTagService(httpClient: (new MockHttpClient($responses))->withOptions([
            'base_uri' => 'https://api.phrase.com/api/v2/projects/1/',
            'headers' => [
                'Authorization' => 'token API_TOKEN',
                'User-Agent' => 'myProject',
            ],
        ]));

        $result = $provider->list($key, $tags);

        self::assertContains('general.back', $result);
        self::assertContains('general.cancel', $result);
    }

    public function testListException(): void
    {
        $this->expectException(ProviderException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('phrase replied with an error (404): "server error"');

        $responses = [
            'list keys' => new MockResponse('server error', ['http_code' => 404]),
        ];

        $provider = $this->createTagService(httpClient: (new MockHttpClient($responses))->withOptions([
            'base_uri' => 'https://api.phrase.com/api/v2/projects/1/',
            'headers' => [
                'Authorization' => 'token API_TOKEN',
                'User-Agent' => 'myProject',
            ],
        ]));

        $provider->list('foo', ['bar']);
    }

    /**
     * @dataProvider tagProvider
     *
     * @param string[] $tags
     * @param string[] $newTags
     */
    public function testTag(?string $key, array $tags, array $newTags): void
    {
        $this->getLogger()
            ->expects(self::once())
            ->method('info')
                ->with(sprintf('tagged 10 keys matching "%s" with tag(s) "%s"', $this->query($key, $tags), implode(', ', $newTags)));

        $responses = [
            'tag keys' => function (string $method, string $url, array $options = []) use ($key, $tags, $newTags): ResponseInterface {
                $body = ['q' => $this->query($key, $tags), 'tags' => implode(',', $newTags)];
                $this->assertSame('PATCH', $method);
                $this->assertSame('https://api.phrase.com/api/v2/projects/1/keys/tag?page=1&per_page=100', $url);
                $this->assertSame(http_build_query($body), $options['body']);

                return new MockResponse(json_encode(['records_affected' => '10'], \JSON_THROW_ON_ERROR));
            },
        ];

        $provider = $this->createTagService(httpClient: (new MockHttpClient($responses))->withOptions([
            'base_uri' => 'https://api.phrase.com/api/v2/projects/1/',
            'headers' => [
                'Authorization' => 'token API_TOKEN',
                'User-Agent' => 'myProject',
            ],
        ]));

        $this->assertSame(10, $provider->tag($key, $tags, $newTags));
    }

    public function testTagException(): void
    {
        $this->expectException(ProviderException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('phrase replied with an error (500): "server error"');

        $responses = [
            'tag keys' => new MockResponse('server error', ['http_code' => 500]),
        ];

        $provider = $this->createTagService(httpClient: (new MockHttpClient($responses))->withOptions([
            'base_uri' => 'https://api.phrase.com/api/v2/projects/1/',
            'headers' => [
                'Authorization' => 'token API_TOKEN',
                'User-Agent' => 'myProject',
            ],
        ]));

        $provider->tag('foo', ['bar'], ['baz']);
    }

    /**
     * @dataProvider tagProvider
     *
     * @param string[] $tags
     * @param string[] $newTags
     */
    public function testUnTag(?string $key, array $tags, array $newTags): void
    {
        $this->getLogger()
            ->expects(self::once())
            ->method('info')
            ->with(sprintf('untagged 10 keys matching "%s" with tag(s) "%s"', $this->query($key, $tags), implode(', ', $newTags)));

        $responses = [
            'untag keys' => function (string $method, string $url, array $options = []) use ($key, $tags, $newTags): ResponseInterface {
                $body = ['q' => $this->query($key, $tags), 'tags' => implode(',', $newTags)];
                $this->assertSame('PATCH', $method);
                $this->assertSame('https://api.phrase.com/api/v2/projects/1/keys/untag?page=1&per_page=100', $url);
                $this->assertSame(http_build_query($body), $options['body']);

                return new MockResponse(json_encode(['records_affected' => '10'], \JSON_THROW_ON_ERROR));
            },
        ];

        $provider = $this->createTagService(httpClient: (new MockHttpClient($responses))->withOptions([
            'base_uri' => 'https://api.phrase.com/api/v2/projects/1/',
            'headers' => [
                'Authorization' => 'token API_TOKEN',
                'User-Agent' => 'myProject',
            ],
        ]));

        $this->assertSame(10, $provider->untag($key, $tags, $newTags));
    }

    public function testUntagException(): void
    {
        $this->expectException(ProviderException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('phrase replied with an error (344): "server error"');

        $responses = [
            'tag keys' => new MockResponse('server error', ['http_code' => 344]),
        ];

        $provider = $this->createTagService(httpClient: (new MockHttpClient($responses))->withOptions([
            'base_uri' => 'https://api.phrase.com/api/v2/projects/1/',
            'headers' => [
                'Authorization' => 'token API_TOKEN',
                'User-Agent' => 'myProject',
            ],
        ]));

        $provider->untag('foo', ['bar'], ['baz']);
    }

    public function tagProvider(): \Generator
    {
        yield 'one tag' => [
            'key' => 'myKey.*',
            'tags' => ['tag-one', 'tag-two'],
            'newTags' => ['new-tag'],
        ];

        yield 'multiple tag' => [
            'key' => 'myKey.*',
            'tags' => ['tag-one', 'tag-two'],
            'newTags' => ['new-tag', 'another-new-tag'],
        ];
    }

    public function listProvider(): \Generator
    {
        $content = <<<'JSON'
[
  {
    "id": "9497dff0fada7af21b3d3a32300d7ea6",
    "name": "general.back",
    "description": "not sure why i don't get a cdata section",
    "name_hash": "2a5fd24f37b982c1c9ff0d124e20a313",
    "plural": false,
    "max_characters_allowed": 0,
    "tags": [
      "messages"
    ],
    "created_at": "2022-12-25T15:36:52Z",
    "updated_at": "2022-12-27T05:21:20Z"
  },
  {
    "id": "ae3e95d1935eebe5c3138bf4fe782206",
    "name": "general.cancel",
    "description": null,
    "name_hash": "dceba40de5446d1df98a8826dc1798c6",
    "plural": false,
    "max_characters_allowed": 0,
    "tags": [
      "messages"
    ],
    "created_at": "2022-12-25T15:36:53Z",
    "updated_at": "2022-12-25T15:36:53Z"
  }
]

JSON;
        yield 'key no tags' => [
            'key' => 'translation.*',
            'tags' => [],
            'content' => $content,
        ];

        yield 'no key but tags' => [
            'key' => null,
            'tags' => ['tag-one', 'tag-two'],
            'content' => $content,
        ];

        yield 'key and tags' => [
            'key' => 'translation.*',
            'tags' => ['tag-one', 'tag-two'],
            'content' => $content,
        ];
    }

    private function createTagService(?MockHttpClient $httpClient = null): PhraseTagService
    {
        return new PhraseTagService(
            $httpClient ?? $this->getHttpClient(),
            $this->getLogger(),
        );
    }

    private function getHttpClient(): HttpClientInterface&MockObject
    {
        return $this->httpClient ??= $this->createMock(HttpClientInterface::class);
    }

    private function getLogger(): LoggerInterface&MockObject
    {
        return $this->logger ??= $this->createMock(LoggerInterface::class);
    }

    /**
     * @param string[] $tags
     */
    private function query(?string $key, array $tags): string
    {
        $query = $key ?? '';
        $query .= [] !== $tags ? ' tags:'.implode(',', $tags) : '';

        return trim($query);
    }
}
