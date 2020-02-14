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
use Obsidian\ETF\Tuple;
use PHPUnit\Framework\TestCase;

final class ListTest extends TestCase {
    function testList(): void {
        // [ 255, 256, 257, 258, 259, 260 ]
        $test = \base64_decode("g2wAAAAGYf9iAAABAGIAAAEBYgAAAQJiAAABA2IAAAEEag==");
        $expected = array(255, 256, 257, 258, 259, 260);
        
        $decoded = (new Decoder())->decode($test);
        $encoded = (new Encoder())->encode($expected);
        
        $this->assertSame($expected, $decoded);
        $this->assertSame($test, $encoded);
    }
    
    function testListImproper(): void {
        // [ 0, 50, 215 | nil ]
        $test = \base64_decode("g2wAAAADYQBhMmHXdwNuaWw=");
        $expected = array(0, 50, 215, 'tail' => null);
        
        $decoded = (new Decoder())->decode($test);
        $encoded = (new Encoder())->encode($expected);
        
        $this->assertSame($expected, $decoded);
        $this->assertSame($test, $encoded);
    }
    
    function testListMix(): void {
        // [ :ok, {:hallo, "hehehe"}, "hello world", 50, 25.0, true, false, nil ]
        $test = \base64_decode("g2wAAAAIdwJva2gCdwVoYWxsb20AAAAGaGVoZWhlbQAAAAtoZWxsbyB3b3JsZGEyRkA5AAAAAAAAdwR0cnVldwVmYWxzZXcDbmlsag==");
        $expected = array(
            (new Atom('ok')),
            (new Tuple(array(
                (new Atom('hallo')),
                'hehehe'
            ))),
            'hello world',
            50,
            25.0,
            true,
            false,
            null
        );
        
        $decoded = (new Decoder())->decode($test);
        $encoded = (new Encoder())->encode($expected);
        
        $this->assertEquals($expected, $decoded);
        $this->assertSame($test, $encoded);
    }
    
    function testListTuple(): void {
        // [:ok, {:hallo, "hehehe"}]
        $test = \base64_decode("g2wAAAACdwJva2gCdwVoYWxsb20AAAAGaGVoZWhlag==");
        $expected = array(
            (new Atom('ok')),
            (new Tuple(array(
                (new Atom('hallo')),
                'hehehe'
            )))
        );
        
        $decoded = (new Decoder())->decode($test);
        $encoded = (new Encoder())->encode($expected);
        
        $this->assertEquals($expected, $decoded);
        $this->assertSame($test, $encoded);
    }
    
    function testListBinary(): void {
        // [ok: "muah"]
        $test = \base64_decode("g2wAAAABaAJ3Am9rbQAAAARtdWFoag==");
        $expected = array((new Tuple(
            array(
                (new Atom('ok')),
                'muah'
            )
        )));
        
        $decoded = (new Decoder())->decode($test);
        $encoded = (new Encoder())->encode($expected);
        
        $this->assertEquals($expected, $decoded);
        $this->assertSame($test, $encoded);
    }
    
    function testListAndMapWithTail() {
        $expected = array(-1 => 2, 'tail' => 'test', 0 => 5);
        $expected2 = array(-1 => 2, 0 => 5, 'tail' => 'test');
        
        $encoded = (new Encoder())->encode($expected);
        $encoded2 = (new Encoder())->encode($expected2);
        
        $this->assertNotSame($encoded[1], $encoded2[1]);
    }
}
