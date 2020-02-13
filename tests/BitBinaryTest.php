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

use Obsidian\ETF\BitBinary;
use Obsidian\ETF\ETF;
use PHPUnit\Framework\TestCase;

final class BitBinaryTest extends TestCase {
    /** @var ETF */
    protected $etf;
    
    function __construct($name = null, array $data = [], $dataName = '') {
        $this->etf = new ETF();
        
        parent::__construct($name, $data, $dataName);
    }
    
    function testBitBinary(): void {
        // << 1 :: 7 >>
        $test = \base64_decode("g00AAAABBwI=");
        $expected = new BitBinary(7, array(1));
        
        $decoded = $this->etf->decode($test);
        $encoded = $this->etf->encode($expected);
        
        $this->assertEquals($expected, $decoded);
        $this->assertSame($test, $encoded);
    }
    
    function testToArray(): void {
        $b1 = new BitBinary(7, array(1));
        $b2 = array('bits' => 7, 'bytes' => array(1));
        
        $this->assertSame($b2, $b1->toArray());
        $this->assertEquals($b1, BitBinary::fromArray($b2));
    }
}