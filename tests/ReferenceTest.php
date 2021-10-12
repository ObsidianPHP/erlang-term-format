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
        // #Reference<0.220326>
        $test = \base64_decode("g2V3DW5vbm9kZUBub2hvc3QAA1ymAQ==");
        $expected = new Reference(
            (new Atom('nonode@nohost')),
            220326,
            1
        );
        
        $decoded = (new Decoder())->decode($test);
        $encoded = (new Encoder())->encode($expected);
        
        self::assertEquals($expected, $decoded);
        self::assertSame($test, $encoded);
    }
    
    function testReferenceIDTooLarge(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Parameter $id can not be larger than 262143');
        
        new Reference(
            (new Atom('nonode@nohost')),
            262144,
            4
        );
    }
    
    function testReferenceCreationTooLarge(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Parameter $creation can not be larger than 3');
        
        new Reference(
            (new Atom('nonode@nohost')),
            2,
            4
        );
    }
    
    function testReferenceWithResource(): void {
        // fake test for reference
        $res = \fopen(__FILE__, 'rb');
        $id = (int) $res;
        
        $binid = \pack('N', $id);
        $binid = \unpack('C*', $binid);
        
        $bytes = array(131, 101, 119, 6, 115, 116, 114, 101, 97, 109);
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
            2
        );
        $i2 = array(
            'node' => array('atom' => 'test'),
            'id' => 21,
            'creation' => 2
        );
        
        self::assertSame($i2, $i1->toArray());
        self::assertEquals($i1, Reference::fromArray($i2));
    }
}
