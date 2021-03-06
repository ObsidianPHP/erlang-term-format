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

use Obsidian\ETF\Atom;
use Obsidian\ETF\Decoder;
use Obsidian\ETF\Encoder;
use Obsidian\ETF\NewPID;
use PHPUnit\Framework\TestCase;

final class NewPIDTest extends TestCase {
    function testNewPID(): void {
        // #PID<0.81.0>
        $test = \base64_decode("g1h3DW5vbm9kZUBub2hvc3QAAABRAAAAAAAAAAA=");
        $expected = new NewPID((new Atom('nonode@nohost')), 81, 0, 0);
        
        $decoded = (new Decoder())->decode($test);
        $encoded = (new Encoder())->encode($expected);
        
        self::assertEquals($expected, $decoded);
        self::assertSame($test, $encoded);
    }
    
    function testToArray(): void {
        $l1 = new NewPID((new Atom('test')), 25, 15, 0);
        $l2 = array(
            'node' => array('atom' => 'test'),
            'id' => 25,
            'serial' => 15,
            'creation' => 0
        );
        
        self::assertSame($l2, $l1->toArray());
        self::assertEquals($l1, NewPID::fromArray($l2));
    }
}
