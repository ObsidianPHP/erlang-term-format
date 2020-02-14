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
use Obsidian\ETF\Port;
use PHPUnit\Framework\TestCase;

final class PortTest extends TestCase {
    function testPort(): void {
        // #Port<0.1226>
        $test = \base64_decode("g2Z3DW5vbm9kZUBub2hvc3QAAATKAA==");
        $expected = new Port((new Atom('nonode@nohost')), 1226, 0);
        
        $decoded = (new Decoder())->decode($test);
        $encoded = (new Encoder())->encode($expected);
        
        $this->assertEquals($expected, $decoded);
        $this->assertSame($test, $encoded);
    }
    
    function testToArray(): void {
        $h1 = new Port((new Atom('test')), 115, 5);
        $h2 = array(
            'node' => array('atom' => 'test'),
            'id' => 115,
            'creation' => 5
        );
        
        $this->assertSame($h2, $h1->toArray());
        $this->assertEquals($h1, Port::fromArray($h2));
    }
}
