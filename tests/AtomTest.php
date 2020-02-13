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
use Obsidian\ETF\ETF;
use Obsidian\ETF\Exception;
use Obsidian\ETF\UnknownTagException;
use PHPUnit\Framework\TestCase;

final class AtomTest extends TestCase {
    /** @var ETF */
    protected $etf;
    
    function __construct($name = null, array $data = [], $dataName = '') {
        $this->etf = new ETF();
        
        parent::__construct($name, $data, $dataName);
    }
    
    function testAtomMaxLength(): void {
        $this->expectException(Exception::class);
        
        Atom::from(\bin2hex(\random_bytes(128)));
    }
    
    function testAtomUnknownTag(): void {
        $this->expectException(UnknownTagException::class);
        
        $data = "\x00";
        $pos = 0;
        
        Atom::decodeIncrement($this->etf, $data, $pos);
    }
    
    function testSmallUtf8AtomTrue(): void {
        $testt = \base64_decode("g3cEdHJ1ZQ==");
        $expectedt = true;
        
        $decodedt = $this->etf->decode($testt);
        $encodedt = $this->etf->encode($expectedt);
        
        $this->assertSame($expectedt, $decodedt);
        $this->assertSame($testt, $encodedt);
        
        $bytest = \substr($testt, 1);
        $pos = 0;
        
        $testt2 = Atom::decodeIncrement($this->etf, $bytest, $pos);
        $this->assertSame($expectedt, $testt2);
    }
    
    function testSmallUtf8AtomFalse(): void {
        $testf = \base64_decode("g3cFZmFsc2U=");
        $expectedf = false;
        
        $decodedf = $this->etf->decode($testf);
        $encodedf = $this->etf->encode($expectedf);
        
        $this->assertSame($expectedf, $decodedf);
        $this->assertSame($testf, $encodedf);
    }
    
    function testSmallUtf8AtomNull(): void {
        $testn = \base64_decode("g3cDbmls");
        $expectedn = null;
        
        $decodedn = $this->etf->decode($testn);
        $encodedn = $this->etf->encode($expectedn);
        
        $this->assertSame($expectedn, $decodedn);
        $this->assertSame($testn, $encodedn);
    }
    
    function testUtf8Atom(): void {
        $test = \base64_decode("g3YB7OKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYuuKYug==");
        $expected = new Atom("☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺☺");
        
        $decoded = $this->etf->decode($test);
        $encoded = $this->etf->encode($expected);
        
        $this->assertEquals($expected, $decoded);
        $this->assertSame($test, $encoded);
    }
    
    function testUtf8AtomTrue(): void {
        $testt = \base64_decode("g3YABHRydWU=");
        $expectedt = true;
        
        $decodedt = $this->etf->decode($testt);
        $encodedt = \chr(131).Atom::from($expectedt)->encodeBig();
        
        $this->assertSame($expectedt, $decodedt);
        $this->assertSame($testt, $encodedt);
        
        $bytest = \substr($testt, 1);
        $pos = 0;
        
        $testt2 = Atom::decodeIncrement($this->etf, $bytest, $pos);
        $this->assertSame($expectedt, $testt2);
    }
    
    function testUtf8AtomFalse(): void {
        $testf = \base64_decode("g3YABWZhbHNl");
        $expectedf = false;
        
        $decodedf = $this->etf->decode($testf);
        $encodedf = \chr(131).Atom::from($expectedf)->encodeBig();
        
        $this->assertSame($expectedf, $decodedf);
        $this->assertSame($testf, $encodedf);
    }
    
    function testUtf8AtomNull(): void {
        $testn = \base64_decode("g3YAA25pbA==");
        $expectedn = null;
        
        $decodedn = $this->etf->decode($testn);
        $encodedn = \chr(131).Atom::from($expectedn)->encodeBig();
        
        $this->assertSame($expectedn, $decodedn);
        $this->assertSame($testn, $encodedn);
    }
    
    function testSmallAtom(): void {
        $test = \base64_decode("g3MBYQ==");
        $expected = new Atom("a");
        
        $decoded = $this->etf->decode($test);
        $encoded = \chr(131).$expected->encodeSmallLatin();
        
        $this->assertEquals($expected, $decoded);
        $this->assertSame($test, $encoded);
    }
    
    function testSmallAtomTrue(): void {
        $testt = \base64_decode("g3MEdHJ1ZQ==");
        $expectedt = true;
        
        $decodedt = $this->etf->decode($testt);
        $encodedt = \chr(131).Atom::from($expectedt)->encodeSmallLatin();
        
        $this->assertSame($expectedt, $decodedt);
        $this->assertSame($testt, $encodedt);
        
        $bytest = \substr($testt, 1);
        $pos = 0;
        
        $testt2 = Atom::decodeIncrement($this->etf, $bytest, $pos);
        $this->assertSame($expectedt, $testt2);
    }
    
    function testSmallAtomFalse(): void {
        $testf = \base64_decode("g3MFZmFsc2U=");
        $expectedf = false;
        
        $decodedf = $this->etf->decode($testf);
        $encodedf = \chr(131).Atom::from($expectedf)->encodeSmallLatin();
        
        $this->assertSame($expectedf, $decodedf);
        $this->assertSame($testf, $encodedf);
    }
    
    function testSmallAtomNull(): void {
        $testn = \base64_decode("g3MDbmls");
        $expectedn = null;
        
        $decodedn = $this->etf->decode($testn);
        $encodedn = \chr(131).Atom::from($expectedn)->encodeSmallLatin();
        
        $this->assertSame($expectedn, $decodedn);
        $this->assertSame($testn, $encodedn);
    }
    
    function testAtom(): void {
        $test = \base64_decode("g2QAAWE=");
        $expected = new Atom("a");
        
        $decoded = $this->etf->decode($test);
        $encoded = \chr(131).$expected->encodeLatin();
        
        $this->assertEquals($expected, $decoded);
        $this->assertSame($test, $encoded);
    }
    
    function testAtomTrue(): void {
        $testt = \base64_decode("g2QABHRydWU=");
        $expectedt = true;
        
        $decodedt = $this->etf->decode($testt);
        $encodedt = \chr(131).Atom::from($expectedt)->encodeLatin();
        
        $this->assertSame($expectedt, $decodedt);
        $this->assertSame($testt, $encodedt);
        
        $bytest = \substr($testt, 1);
        $pos = 0;
        
        $testt2 = Atom::decodeIncrement($this->etf, $bytest, $pos);
        $this->assertSame($expectedt, $testt2);
    }
    
    function testAtomFalse(): void {
        $testf = \base64_decode("g2QABWZhbHNl");
        $expectedf = false;
        
        $decodedf = $this->etf->decode($testf);
        $encodedf = \chr(131).Atom::from($expectedf)->encodeLatin();
        
        $this->assertSame($expectedf, $decodedf);
        $this->assertSame($testf, $encodedf);
    }
    
    function testAtomNull(): void {
        $testn = \base64_decode("g2QAA25pbA==");
        $expectedn = null;
        
        $decodedn = $this->etf->decode($testn);
        $encodedn = \chr(131).Atom::from($expectedn)->encodeLatin();
        
        $this->assertSame($expectedn, $decodedn);
        $this->assertSame($testn, $encodedn);
    }
    
    function testToArray(): void {
        $a1 = new Atom('ok');
        $a2 = array('atom' => 'ok');
        
        $this->assertSame($a2, $a1->toArray());
        $this->assertEquals($a1, Atom::fromArray($a2));
    }
}
