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
use Obsidian\ETF\NewPort;
use PHPUnit\Framework\TestCase;

final class NewPortTest extends TestCase {
    /** @var ETF */
    protected $etf;
    
    function __construct($name = null, array $data = [], $dataName = '') {
        $this->etf = new ETF();
        
        parent::__construct($name, $data, $dataName);
    }
    
    function testNewPort(): void {
        // #Port<0.1226>
        $test = \base64_decode("g1l3DW5vbm9kZUBub2hvc3QAAATKAAAAAA==");
        $expected = new NewPort((new Atom('nonode@nohost')), 1226, 0);
        
        $decoded = $this->etf->decode($test);
        $encoded = $this->etf->encode($expected);
        
        $this->assertEquals($expected, $decoded);
        $this->assertSame($test, $encoded);
    }
    
    function testToArray(): void {
        $m1 = new NewPort((new Atom('test')), 255, 152);
        $m2 = array(
            'node' => array('atom' => 'test'),
            'id' => 255,
            'creation' => 152
        );
        
        $this->assertSame($m2, $m1->toArray());
        $this->assertEquals($m1, NewPort::fromArray($m2));
    }
}
