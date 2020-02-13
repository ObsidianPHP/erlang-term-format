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

use Obsidian\ETF\ETF;
use PHPUnit\Framework\TestCase;

final class MapTest extends TestCase {
    /** @var ETF */
    protected $etf;
    
    function __construct($name = null, array $data = [], $dataName = '') {
        $this->etf = new ETF();
        
        parent::__construct($name, $data, $dataName);
    }
    
    function testMap(): void {
        // %{ ha: 500 }
        $test = \base64_decode("g3QAAAABdwJoYWIAAAH0");
        $expected = array(
            ':ha' => 500
        );
        
        $decoded = $this->etf->decode($test);
        $encoded = $this->etf->encode($expected);
        
        $this->assertSame($expected, $decoded);
        $this->assertSame($test, $encoded);
    }
}
