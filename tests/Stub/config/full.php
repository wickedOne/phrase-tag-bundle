<?php

/*
 * This file is part of the Phrase Tag Bundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/* @phpstan-ignore-next-line */
$container->loadFromExtension('wicked_one_phrase_tag', [
    'dsn' => 'phrase://PROJECT_ID:API_TOKEN@default?userAgent=myProject',
]);
