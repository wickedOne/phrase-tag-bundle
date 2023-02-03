<?php

declare(strict_types=1);

/*
 * This file is part of the Phrase Tag Bundle.
 * (c) wicliff <wicliff.wolda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WickedOne\PhraseTagBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WickedOne\PhraseTagBundle\WickedOnePhraseTagBundle;

/**
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class WickedOnePhraseTagBundleTest extends TestCase
{
    public function testGetPath(): void
    {
        $bundle = new WickedOnePhraseTagBundle();

        $this->assertSame(realpath(__DIR__.'/../../'), $bundle->getPath());
    }
}
