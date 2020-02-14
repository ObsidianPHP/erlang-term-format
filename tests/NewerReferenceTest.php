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
use Obsidian\ETF\NewerReference;
use PHPUnit\Framework\TestCase;

final class NewerReferenceTest extends TestCase {
    function testNewerReference(): void {
        // #Reference<0.110178461.3583246337.247866>
        $test = \base64_decode("g1oAA3cNbm9ub2RlQG5vaG9zdAAAAAAAA8g61ZQAAQaRMJ0=");
        $expected = new NewerReference(
            (new Atom("nonode@nohost")),
            0,
            array(
                247866, 3583246337, 110178461
            )
        );
        
        $decoded = (new Decoder())->decode($test);
        $encoded = (new Encoder())->encode($expected);
        
        $this->assertEquals($expected, $decoded);
        $this->assertSame($test, $encoded);
    }
    
    function testToArray(): void {
        $k1 = new NewerReference((new Atom('test')), 5, array(0));
        $k2 = array(
            'node' => array('atom' => 'test'),
            'creation' => 5,
            'id' => array(0)
        );
        
        $this->assertSame($k2, $k1->toArray());
        $this->assertEquals($k1, NewerReference::fromArray($k2));
    }
}
