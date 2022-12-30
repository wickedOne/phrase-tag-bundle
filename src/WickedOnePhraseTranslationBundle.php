<?php

declare(strict_types=1);

/*
 * This file is part of the Phrase Translation Bundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WickedOne\PhraseTranslationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class WickedOnePhraseTranslationBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
