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
use Obsidian\ETF\PID;
use PHPUnit\Framework\TestCase;

final class PIDTest extends TestCase {
    /** @var ETF */
    protected $etf;
    
    function __construct($name = null, array $data = [], $dataName = '') {
        $this->etf = new ETF();
        
        parent::__construct($name, $data, $dataName);
    }
    
    function testPID(): void {
        // #PID<0.81.0>
        $test = \base64_decode("g2d3DW5vbm9kZUBub2hvc3QAAABRAAAAAAA=");
        $expected = new PID((new Atom('nonode@nohost')), 81, 0, 0);
        
        $decoded = $this->etf->decode($test);
        $encoded = $this->etf->encode($expected);
        
        $this->assertEquals($expected, $decoded);
        $this->assertSame($test, $encoded);
    }
    
    function testToArray(): void {
        $g1 = new PID((new Atom('test')), 10, 15, 5);
        $g2 = array(
            'node' => array('atom' => 'test'),
            'id' => 10,
            'serial' => 15,
            'creation' => 5
        );
        
        $this->assertSame($g2, $g1->toArray());
        $this->assertEquals($g1, PID::fromArray($g2));
    }
}
