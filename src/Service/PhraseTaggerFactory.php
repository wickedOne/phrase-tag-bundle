<?php

declare(strict_types=1);

/*
 * This file is part of the Phrase Translation Bundle.
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

        if (null !== $port = $dsn->getPort()) {
            $endpoint .= ':'.$port;
        }

        if (null === ($user = $dsn->getUser()) || null === ($password = $dsn->getPassword())) {
            throw new \LogicException('please provide project id and api key');
        }

        $client = $this->httpClient->withOptions([
            'base_uri' => 'https://'.$endpoint.'/v2/projects/'.$user.'/',
            'headers' => [
                'Authorization' => 'token '.$password,
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
