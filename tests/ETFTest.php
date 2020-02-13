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
use Obsidian\ETF\Exception;
use Obsidian\ETF\UnknownTagException;
use PHPUnit\Framework\TestCase;

final class ETFTest extends TestCase {
    /** @var ETF */
    protected $etf;
    
    function __construct($name = null, array $data = [], $dataName = '') {
        $this->etf = new ETF();
        
        parent::__construct($name, $data, $dataName);
    }
    
    function testETFVersion(): void {
        $this->expectException(Exception::class);
        
        $this->etf->decode("\x00");
    }
    
    function testETFOutOfRange(): void {
        $this->expectException(Exception::class);
        
        $input = '';
        $data = '';
        $pos = 0;
        
        $this->etf->parseAny($input, $data, $pos);
    }
    
    function testETFUnknownTag(): void {
        $this->expectException(UnknownTagException::class);
        
        $input = 30;
        $data = "\x00\x50\x25\x00";
        $pos = 0;
        
        $this->etf->parseAny($input, $data, $pos);
    }
}
