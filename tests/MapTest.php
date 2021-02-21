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

use Obsidian\ETF\Decoder;
use Obsidian\ETF\Encoder;
use PHPUnit\Framework\TestCase;

final class MapTest extends TestCase {
    function testMap(): void {
        // %{ ha: 500 }
        $test = \base64_decode("g3QAAAABdwJoYWIAAAH0");
        $expected = array(
            ':ha' => 500
        );
        
        $decoded = (new Decoder())->decode($test);
        $encoded = (new Encoder())->encode($expected);
        
        self::assertSame($expected, $decoded);
        self::assertSame($test, $encoded);
    }
}
