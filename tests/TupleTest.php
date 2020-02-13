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
use Obsidian\ETF\Tuple;
use PHPUnit\Framework\TestCase;

final class TupleTest extends TestCase {
    /** @var ETF */
    protected $etf;
    
    function __construct($name = null, array $data = [], $dataName = '') {
        $this->etf = new ETF();
        
        parent::__construct($name, $data, $dataName);
    }
    
    function testTupleAccess(): void {
        $tuple = new Tuple(array(0, 5, 2, 'hi'));
        
        $this->assertSame(0, $tuple[0]);
        
        $tuple[] = 'hello_world';
        $this->assertSame('hello_world', $tuple[4]);
        
        $this->assertFalse(isset($tuple['hi']));
        
        $this->assertFalse(isset($tuple['hi']));
        
        $tuple['hi'] = true;
        $this->assertSame(true, $tuple['hi']);
        
        $this->assertTrue(isset($tuple['hi']));
        
        unset($tuple['hi']);
        $this->assertSame(null, $tuple['hi']);
    }
    
    function testTuple(): void {
        // {:hallo, "hehehe"}
        $test = \base64_decode("g2gCdwVoYWxsb20AAAAGaGVoZWhl");
        $expected = new Tuple(array(
            (new Atom('hallo')),
            'hehehe'
        ));
        
        $decoded = $this->etf->decode($test);
        $encoded = $this->etf->encode($expected);
        
        $this->assertEquals($expected, $decoded);
        $this->assertSame($test, $encoded);
    }
    
    function testTupleSmall(): void {
        $array = array();
        for($i = 0; $i < 2; $i++) {
            $array[] = \bin2hex(\random_bytes(5));
        }
        
        $tuple = new Tuple($array);
        
        $encoded = $tuple->encode();
        
        $pos = 1;
        $decoded = Tuple::decode($this->etf, $encoded, $pos);
        
        $this->assertEquals($tuple, $decoded);
    }
    
    function testLargeTuple(): void {
        // generate large tuple
        $array = \array_fill(0, 256, '');
        foreach($array as &$val) {
            $val = \bin2hex(\random_bytes(5));
        }
        
        $test = $this->etf->encode((new Tuple($array)));
        $expected = new Tuple($array);
        
        $decoded = $this->etf->decode($test);
        $this->assertEquals($expected, $decoded);
        
        $pos = 2;
        $decoded2 = Tuple::decode($this->etf, $test, $pos);
        
        $this->assertEquals($expected, $decoded2);
    }
    
    function testToArray(): void {
        $j1 = new Tuple(array(
            'hehehe'
        ));
        $j2 = array('hehehe');
        
        $this->assertSame($j2, $j1->toArray());
        $this->assertEquals($j1, Tuple::fromArray($j2));
    }
}
