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
use Symfony\Component\Translation\Exception\UnsupportedSchemeException;
use Symfony\Component\Translation\Provider\Dsn;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class PhraseTaggerFactory
{
    private const HOST = 'api.phrase.com';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function create(string $config): PhraseTagService
    {
        $dsn = new Dsn($config);

        if ('phrase' !== $dsn->getScheme()) {
            throw new UnsupportedSchemeException($dsn, 'phrase', $this->getSupportedSchemes());
        }

        $endpoint = 'default' === $dsn->getHost() ? self::HOST : $dsn->getHost();
        $endpoint .= $dsn->getPort() ? ':'.$dsn->getPort() : '';

        $client = $this->httpClient->withOptions([
            'base_uri' => 'https://'.$endpoint.'/v2/projects/'.$dsn->getUser().'/',
            'headers' => [
                'Authorization' => 'token '.$dsn->getPassword(),
                'User-Agent' => $dsn->getRequiredOption('userAgent'),
            ],
        ]);

        return new PhraseTagService($client, $this->logger);
    }

    /**
     * @return string[]
     */
    private function getSupportedSchemes(): array
    {
        return ['phrase'];
    }
}
