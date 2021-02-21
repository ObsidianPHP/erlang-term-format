<?php
/**
 * ETF
 * Copyright 2020 ObsidianPHP, All Rights Reserved
 *
 * Website: https://github.com/ObsidianPHP/ETF
 * License: https://github.com/ObsidianPHP/ETF/blob/master/LICENSE
 * @noinspection PhpUnhandledExceptionInspection
*/

namespace Obsidian\ETF\Tests;

use Obsidian\ETF\BitBinary;
use Obsidian\ETF\Decoder;
use Obsidian\ETF\Encoder;
use PHPUnit\Framework\TestCase;

final class BitBinaryTest extends TestCase {
    function testBitBinary(): void {
        // << 1 :: 7 >>
        $test = \base64_decode("g00AAAABBwI=");
        $expected = new BitBinary(7, array(1));
        
        $decoded = (new Decoder())->decode($test);
        $encoded = (new Encoder())->encode($expected);
        
        self::assertEquals($expected, $decoded);
        self::assertSame($test, $encoded);
    }
    
    function testToArray(): void {
        $b1 = new BitBinary(7, array(1));
        $b2 = array('bits' => 7, 'bytes' => array(1));
        
        self::assertSame($b2, $b1->toArray());
        self::assertEquals($b1, BitBinary::fromArray($b2));
    }
}
