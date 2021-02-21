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
use Obsidian\ETF\Exception;
use Obsidian\ETF\UnknownTagException;
use PHPUnit\Framework\TestCase;

final class AtomTest extends TestCase {
    function testAtomMaxLength(): void {
        $this->expectException(Exception::class);
        
        Atom::from(\bin2hex(\random_bytes(128)));
    }
    
    function testAtomUnknownTag(): void {
        $this->expectException(UnknownTagException::class);
        
        $data = "\x00";
        $pos = 0;
        
        Atom::decodeIncrement((new Decoder()), $data, $pos);
    }
    
    function testSmallUtf8AtomTrue(): void {
        $testt = \base64_decode("g3cEdHJ1ZQ==");
        $expectedt = true;
        
        $decodedt = (new Decoder())->decode($testt);
        $encodedt = (new Encoder())->encode($expectedt);
        
        self::assertSame($expectedt, $decodedt);
        self::assertSame($testt, $encodedt);
        
        $bytest = \substr($testt, 1);
        $pos = 0;
        
        $testt2 = Atom::decodeIncrement((new Decoder()), $bytest, $pos);
        self::assertSame($expectedt, $testt2);
    }
    
    function testSmallUtf8AtomFalse(): void {
        $testf = \base64_decode("g3cFZmFsc2U=");
        $expectedf = false;
        
        $decodedf = (new Decoder())->decode($testf);
        $encodedf = (new Encoder())->encode($expectedf);
        
        self::assertSame($expectedf, $decodedf);
        self::assertSame($testf, $encodedf);
    }
    
    function testSmallUtf8AtomNull(): void {
        $testn = \base64_decode("g3cDbmls");
        $expectedn = null;
        
        $decodedn = (new Decoder())->decode($testn);
        $encodedn = (new Encoder())->encode($expectedn);
        
        self::assertSame($expectedn, $decodedn);
        self::assertSame($testn, $encodedn);
    }
    
    function testUtf8Atom(): void {
        $test = \base64_decode("g3YB7OKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYug==");
        $expected = new Atom("☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺");
        
        $decoded = (new Decoder())->decode($test);
        $encoded = (new Encoder())->encode($expected);
        
        self::assertEquals($expected, $decoded);
        self::assertSame($test, $encoded);
    }
    
    function testUtf8AtomTrue(): void {
        $testt = \base64_decode("g3YABHRydWU=");
        $expectedt = true;
        
        $decodedt = (new Decoder())->decode($testt);
        $encodedt = \chr(131).Atom::from($expectedt)->encodeBig();
        
        self::assertSame($expectedt, $decodedt);
        self::assertSame($testt, $encodedt);
        
        $bytest = \substr($testt, 1);
        $pos = 0;
        
        $testt2 = Atom::decodeIncrement((new Decoder()), $bytest, $pos);
        self::assertSame($expectedt, $testt2);
    }
    
    function testUtf8AtomFalse(): void {
        $testf = \base64_decode("g3YABWZhbHNl");
        $expectedf = false;
        
        $decodedf = (new Decoder())->decode($testf);
        $encodedf = \chr(131).Atom::from($expectedf)->encodeBig();
        
        self::assertSame($expectedf, $decodedf);
        self::assertSame($testf, $encodedf);
    }
    
    function testUtf8AtomNull(): void {
        $testn = \base64_decode("g3YAA25pbA==");
        $expectedn = null;
        
        $decodedn = (new Decoder())->decode($testn);
        $encodedn = \chr(131).Atom::from($expectedn)->encodeBig();
        
        self::assertSame($expectedn, $decodedn);
        self::assertSame($testn, $encodedn);
    }
    
    function testSmallAtom(): void {
        $test = \base64_decode("g3MBYQ==");
        $expected = new Atom("a");
        
        $decoded = (new Decoder())->decode($test);
        $encoded = \chr(131).$expected->encodeSmallLatin();
        
        self::assertEquals($expected, $decoded);
        self::assertSame($test, $encoded);
    }
    
    function testSmallAtomTrue(): void {
        $testt = \base64_decode("g3MEdHJ1ZQ==");
        $expectedt = true;
        
        $decodedt = (new Decoder())->decode($testt);
        $encodedt = \chr(131).Atom::from($expectedt)->encodeSmallLatin();
        
        self::assertSame($expectedt, $decodedt);
        self::assertSame($testt, $encodedt);
        
        $bytest = \substr($testt, 1);
        $pos = 0;
        
        $testt2 = Atom::decodeIncrement((new Decoder()), $bytest, $pos);
        self::assertSame($expectedt, $testt2);
    }
    
    function testSmallAtomFalse(): void {
        $testf = \base64_decode("g3MFZmFsc2U=");
        $expectedf = false;
        
        $decodedf = (new Decoder())->decode($testf);
        $encodedf = \chr(131).Atom::from($expectedf)->encodeSmallLatin();
        
        self::assertSame($expectedf, $decodedf);
        self::assertSame($testf, $encodedf);
    }
    
    function testSmallAtomNull(): void {
        $testn = \base64_decode("g3MDbmls");
        $expectedn = null;
        
        $decodedn = (new Decoder())->decode($testn);
        $encodedn = \chr(131).Atom::from($expectedn)->encodeSmallLatin();
        
        self::assertSame($expectedn, $decodedn);
        self::assertSame($testn, $encodedn);
    }
    
    function testAtom(): void {
        $test = \base64_decode("g2QAAWE=");
        $expected = new Atom("a");
        
        $decoded = (new Decoder())->decode($test);
        $encoded = \chr(131).$expected->encodeLatin();
        
        self::assertEquals($expected, $decoded);
        self::assertSame($test, $encoded);
    }
    
    function testAtomTrue(): void {
        $testt = \base64_decode("g2QABHRydWU=");
        $expectedt = true;
        
        $decodedt = (new Decoder())->decode($testt);
        $encodedt = \chr(131).Atom::from($expectedt)->encodeLatin();
        
        self::assertSame($expectedt, $decodedt);
        self::assertSame($testt, $encodedt);
        
        $bytest = \substr($testt, 1);
        $pos = 0;
        
        $testt2 = Atom::decodeIncrement((new Decoder()), $bytest, $pos);
        self::assertSame($expectedt, $testt2);
    }
    
    function testAtomFalse(): void {
        $testf = \base64_decode("g2QABWZhbHNl");
        $expectedf = false;
        
        $decodedf = (new Decoder())->decode($testf);
        $encodedf = \chr(131).Atom::from($expectedf)->encodeLatin();
        
        self::assertSame($expectedf, $decodedf);
        self::assertSame($testf, $encodedf);
    }
    
    function testAtomNull(): void {
        $testn = \base64_decode("g2QAA25pbA==");
        $expectedn = null;
        
        $decodedn = (new Decoder())->decode($testn);
        $encodedn = \chr(131).Atom::from($expectedn)->encodeLatin();
        
        self::assertSame($expectedn, $decodedn);
        self::assertSame($testn, $encodedn);
    }
    
    function testToArray(): void {
        $a1 = new Atom('ok');
        $a2 = array('atom' => 'ok');
        
        self::assertSame($a2, $a1->toArray());
        self::assertEquals($a1, Atom::fromArray($a2));
    }
}
