<?php

declare(strict_types=1);

/*
 * This file is part of the Phrase Translation Helper.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WickedOne\PhraseTranslationBundle\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Translation\Exception\ProviderException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class PhraseTagService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @param string[] $tags
     *
     * @return string[]
     */
    public function list(?string $key, array $tags): array
    {
        $response = $this->httpClient->request('GET', 'keys', [
            'query' => [
                'page' => '1',
                'per_page' => '100',
                'q' => $this->createQuery($key, $tags),
            ],
        ]);

        if (200 !== $statusCode = $response->getStatusCode()) {
            throw new ProviderException(sprintf('phrase replied with an error (%d): "%s"', $statusCode, $response->getContent(false)), $response);
        }

        return array_column($response->toArray(), 'name');
    }

    /**
     * @param string[] $tags
     * @param string[] $addTags
     */
    public function tag(?string $key, array $tags, array $addTags): int
    {
        $query = $this->createQuery($key, $tags);
        $response = $this->httpClient->request('PATCH', 'keys/tag', [
            'query' => [
                'page' => '1',
                'per_page' => '100',
            ],
            'body' => [
                'q' => $query,
                'tags' => implode(',', $addTags),
            ],
        ]);

        if (200 !== $statusCode = $response->getStatusCode()) {
            throw new ProviderException(sprintf('phrase replied with an error (%d): "%s"', $statusCode, $response->getContent(false)), $response);
        }

        $records = $response->toArray()['records_affected'];

        $this->logger->info(sprintf('tagged %d keys matching "%s" with tag(s) "%s"', $records, $query, implode(', ', $addTags)));

        return $records;
    }

    /**
     * @param string[] $tags
     * @param string[] $removeTags
     */
    public function untag(?string $key, array $tags, array $removeTags): int
    {
        $query = $this->createQuery($key, $tags);
        $response = $this->httpClient->request('PATCH', 'keys/untag', [
            'query' => [
                'page' => '1',
                'per_page' => '100',
            ],
            'body' => [
                'q' => $query,
                'tags' => implode(',', $removeTags),
            ],
        ]);

        if (200 !== $statusCode = $response->getStatusCode()) {
            throw new ProviderException(sprintf('phrase replied with an error (%d): "%s"', $statusCode, $response->getContent(false)), $response);
        }

        $records = (int) $response->toArray()['records_affected'];

        $this->logger->info(sprintf('untagged %d keys matching "%s" with tag(s) "%s"', $records, $query, implode(', ', $removeTags)));

        return $records;
    }

    /**
     * @param string[] $tags
     */
    private function createQuery(?string $key, array $tags): string
    {
        $query = $key ?? '';
        $query .= [] !== $tags ? ' tags:'.implode(',', $tags) : '';

        return trim($query);
    }
}
