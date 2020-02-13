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
use Obsidian\ETF\NewReference;
use PHPUnit\Framework\TestCase;

final class NewReferenceTest extends TestCase {
    /** @var ETF */
    protected $etf;
    
    function __construct($name = null, array $data = [], $dataName = '') {
        $this->etf = new ETF();
        
        parent::__construct($name, $data, $dataName);
    }
    
    function testNewReference(): void {
        // #Reference<0.110178461.3583246337.247866>
        $test = \base64_decode("g3IAA3cNbm9ub2RlQG5vaG9zdAAAA8g61ZQAAQaRMJ0=");
        $expected = new NewReference(
            (new Atom("nonode@nohost")),
            0,
            array(
                247866, 3583246337, 110178461
            )
        );
        
        $decoded = $this->etf->decode($test);
        $encoded = $this->etf->encode($expected);
        
        $this->assertEquals($expected, $decoded);
        $this->assertSame($test, $encoded);
    }
    
    function testToArray(): void {
        $f1 = new NewReference(
            (new Atom('test')),
            0,
            array(50, 25)
        );
        $f2 = array(
            'node' => array('atom' => 'test'),
            'creation' => 0,
            'id' => array(50, 25)
        );
        
        $this->assertSame($f2, $f1->toArray());
        $this->assertEquals($f1, NewReference::fromArray($f2));
    }
}