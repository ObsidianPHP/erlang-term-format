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

final class ScalarTest extends TestCase {
    function testObject(): void {
        $test = new \stdClass();
        $test->one = false;
        $expected = (array) $test;
        
        $encoded = (new Encoder())->encode($test);
        $decoded = (new Decoder())->decode($encoded);
        
        $this->assertEquals($expected, $decoded);
    }
    
    function testSmallInteger(): void {
        // 52
        $test = \base64_decode("g2E0");
        $expected = 52;
        
        $decoded = (new Decoder())->decode($test);
        $encoded = (new Encoder())->encode($expected);
        
        $this->assertSame($expected, $decoded);
        $this->assertSame($test, $encoded);
    }
    
    function testInteger(): void {
        // 500
        $test = \base64_decode("g2IAAAH0");
        $expected = 500;
        
        $decoded = (new Decoder())->decode($test);
        $encoded = (new Encoder())->encode($expected);
        
        $this->assertSame($expected, $decoded);
        $this->assertSame($test, $encoded);
    }
    
    function testIntegerLarger(): void {
        // 551234500
        $test = \base64_decode("g2Ig2yvE");
        $expected = 551234500;
        
        $decoded = (new Decoder())->decode($test);
        $encoded = (new Encoder())->encode($expected);
        
        $this->assertSame($expected, $decoded);
        $this->assertSame($test, $encoded);
    }
    
    function testSmallBig(): void {
        // 265797079060840458
        $test = \base64_decode("g24IAAoAwv8ITbAD");
        $expected = 265797079060840458;
        
        $decoded = (new Decoder())->decode($test);
        $encoded = (new Encoder())->encode($expected);
        
        $this->assertSame($expected, $decoded);
        $this->assertSame($test, $encoded);
    }
    
    function testLargeBig(): void {
        // 525641648694896489645751548748949844684489498489489648496886952564164869489648964575154874894984468448949848948964849688695256416486948964896457515487489498446844894984894896484968869525641648694896489645751548748949844684489498489489648496886952564164869489648964575154874894984468448949848948964849688695256416486948964896457515487489498446844894984894896484968869525641648694896489645751548748949844684489498489489648496886952564164869489648964575154874894984468448949848948964849688695256416486948964896457515487489498446844894984894896484968869525641648694896489645751548748949844684489498489489648496886952564164869489648964575154874894984468448949848948964849688695256416486948964896457515487489498446844894984894896484968869525641648694896489645751548748949844684489498489489648496886952564164869489648964575154874894984468448949848948964849688695256416486948964896457515487489498446844894984894896484968869
        $test = \base64_decode("g28AAAF8AKXpzEIRBZAN24e9/M5KlAFKrT0y+mmhWrpU9DZ/359aq/aRwZVtRuewA3UPALxTU8GZcVlflZGbbbaRFXcxnAp2dNo7krZHVa1jeUouox8co55G+d4RMGVGEfQyKVQ6bUD1dsNyFfqsb3knJgzpeaEkkDZDrJw8U3mRGisVgZ4jhEoOXT41YhBmJ7wIERnt6RVoMjcWV9hKHq5c+xAGdgtuNgjjmCxvIjFj8X3VGZEtW0/SPPkkxRzBwRD2x+lkUb8XC1QTcLf9EjpeWTMfMH1m9NMkOgNLSlmWKyObElkGUBuuQKynp3hBJ/2ySjldEskYEtwA+rwQMKrQCqTnvvqmvHOSwv7jW2rljKsGlN4RJxaAOHW8T+OJKnxR6eM7YcvGOiR1ECnnrkOWT/NDNtBKj0cfTPxFzPNUUMvYp4zhr5Jw+XZ8FcOblDkDsWC9SKV/T1zuWxxdX/4qHtiMO9S8PMJhKLWg+uaodNeE+Tq6SEJ9sSr0ejdzTHtj");
        $expected = "525641648694896489645751548748949844684489498489489648496886952564164869489648964575154874894984468448949848948964849688695256416486948964896457515487489498446844894984894896484968869525641648694896489645751548748949844684489498489489648496886952564164869489648964575154874894984468448949848948964849688695256416486948964896457515487489498446844894984894896484968869525641648694896489645751548748949844684489498489489648496886952564164869489648964575154874894984468448949848948964849688695256416486948964896457515487489498446844894984894896484968869525641648694896489645751548748949844684489498489489648496886952564164869489648964575154874894984468448949848948964849688695256416486948964896457515487489498446844894984894896484968869525641648694896489645751548748949844684489498489489648496886952564164869489648964575154874894984468448949848948964849688695256416486948964896457515487489498446844894984894896484968869";
        
        $decoded = (new Decoder())->decode($test);
        $encoded = (new Encoder())->encode($expected);
                
        $this->assertSame($expected, $decoded);
        $this->assertSame($test, $encoded);
    }
    
    function testString(): void {
        // [ 87, 54, 109, 105, 243 ]
        $test = \base64_decode("g2sABVc2bWnz");
        $expected = array(87, 54, 109, 105, 243);
        
        $decoded = (new Decoder())->decode($test);
        $encoded = (new Encoder())->encode($expected);
        
        $this->assertSame($expected, $decoded);
        $this->assertSame($test, $encoded);
    }
    
    function testFloatString(): void {
        // 5.5
        $test = \base64_decode("g2M1LjUwMDAwMDAwMDAwMDAwMDAwMDAwZSswMAAAAAAA");
        $expected = 5.5;
        
        $decoded = (new Decoder())->decode($test);
        $this->assertSame($expected, $decoded);
    }
    
    function testNewFloat(): void {
        // 5.5
        $test = \base64_decode("g0ZAFgAAAAAAAA==");
        $expected = 5.5;
        
        $decoded = (new Decoder())->decode($test);
        $encoded = (new Encoder())->encode($expected);
        
        $this->assertSame($expected, $decoded);
        $this->assertSame($test, $encoded);
    }
    
    function testNegativeInteger() {
        $expected = -250539;
        
        $encoded = (new Encoder())->encode($expected);
        $decoded = (new Decoder())->decode($encoded);
        
        $this->assertSame($expected, $decoded);
    }
    
    function testNegativeSmallBigInteger() {
        $expected = -200317799350927360;
        $test = (string) $expected;
        
        $encoded = (new Encoder())->encode($test);
        $decoded = (new Decoder())->decode($encoded);
        
        $this->assertSame($expected, $decoded);
    }
}
