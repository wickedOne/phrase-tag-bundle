<?php

declare(strict_types=1);

/*
 * This file is part of the Phrase Translation Helper.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WickedOne\PhraseTranslationBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WickedOne\PhraseTranslationBundle\WickedOnePhraseTranslationBundle;

/**
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class WickedOnePhraseTranslationBundleTest extends TestCase
{
    public function testGetPath(): void
    {
        $bundle = new WickedOnePhraseTranslationBundle();

        $this->assertSame(realpath(__DIR__.'/../../'), $bundle->getPath());
    }
}
