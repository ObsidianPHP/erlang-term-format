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
use Obsidian\ETF\Reference;
use PHPUnit\Framework\TestCase;

final class ReferenceTest extends TestCase {
    function testReference(): void {
        // fake test for reference
        $res = \fopen(__FILE__, 'r');
        $id = (int) $res;
        
        $binid = \pack('N', $id);
        $binid = \unpack('C*', $binid);
        
        $bytes = array(131, 101, 119, 4, 99, 117, 114, 108);
        $bytes = \array_merge($bytes, $binid);
        $bytes[] = 0;
        
        $test = \pack('C*', ...$bytes);
        $expected = new Reference((new Atom(\get_resource_type($res))), $id, 0);
        
        $decoded = (new Decoder())->decode($test);
        $encoded = (new Encoder())->encode($res);
        
        self::assertEquals($expected, $decoded);
        self::assertSame($test, $encoded);
    }
    
    function testToArray(): void {
        $i1 = new Reference(
            (new Atom('test')),
            21,
            25
        );
        $i2 = array(
            'node' => array('atom' => 'test'),
            'id' => 21,
            'creation' => 25
        );
        
        self::assertSame($i2, $i1->toArray());
        self::assertEquals($i1, Reference::fromArray($i2));
    }
}
