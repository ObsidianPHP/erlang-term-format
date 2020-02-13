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
use Obsidian\ETF\Fun;
use Obsidian\ETF\PID;
use PHPUnit\Framework\TestCase;

final class FunTest extends TestCase {
    /** @var ETF */
    protected $etf;
    
    function __construct($name = null, array $data = [], $dataName = '') {
        $this->etf = new ETF();
        
        parent::__construct($name, $data, $dataName);
    }
    
    function testFun(): void {
        // fake test for old fun
        $expected = new Fun(
            1,
            (new PID((new Atom('ok')), 0, 0, 1)),
            (new Atom('test')),
            0,
            1,
            array('test')
        );
        
        $test = $this->etf->encode($expected);
        $decoded = $this->etf->decode($test);
        
        $this->assertEquals($expected, $decoded);
    }
    
    function testToArray(): void {
        $d1 = new Fun(
            0,
            (new PID((new Atom('ok')), 0, 0, 1)),
            (new Atom('test')),
            0,
            1,
            array()
        );
        $d2 = array(
            'numFree' => 0,
            'pid' => array(
                'node' => array('atom' => 'ok'),
                'id' => 0,
                'serial' => 0,
                'creation' => 1
            ),
            'module' => array('atom' => 'test'),
            'index' => 0,
            'uniq' => 1,
            'freeVars' => array()
        );
        
        $this->assertSame($d2, $d1->toArray());
        $this->assertEquals($d1, Fun::fromArray($d2));
    }
}
