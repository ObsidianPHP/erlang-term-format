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
use Obsidian\ETF\NewFun;
use Obsidian\ETF\PID;
use Obsidian\ETF\Tuple;
use PHPUnit\Framework\TestCase;

final class NewFunTest extends TestCase {
    function testNewFun(): void {
        // fn a -> a * 2 end
        $test = \base64_decode("g3AAAACtAb2Qtpq77M9gWRIioTSYENgAAAAGAAAAAXcIZXJsX2V2YWxhBmIF7IW0Z3cNbm9ub2RlQG5vaG9zdAAAAFEAAAAAAGgEancEbm9uZXcEbm9uZWwAAAABaAV3BmNsYXVzZWEKbAAAAAFoA3cDdmFyYQp3BFZhQDFqamwAAAABaAV3Am9wYQp3ASpoA3cDdmFyYQp3BFZhQDFoA3cHaW50ZWdlcmEAYQJqag==");
        $expected = new NewFun(
            173,
            1,
            'bd90b69abbeccf60591222a1349810d8',
            6,
            (new Atom('erl_eval')),
            1,
            6,
            99386804,
            (new PID((new Atom('nonode@nohost')), 81, 0, 0)),
            array(
                (new Tuple(array(
                    array(),
                    (new Atom('none')),
                    (new Atom('none')),
                    array(
                        (new Tuple(array(
                            (new Atom('clause')),
                            10,
                            array(
                                (new Tuple(array(
                                    (new Atom('var')),
                                    10,
                                    (new Atom('Va@1'))
                                )))
                            ),
                            array(),
                            array((new Tuple(array(
                                (new Atom('op')),
                                10,
                                (new Atom('*')),
                                (new Tuple(array(
                                    (new Atom('var')),
                                    10,
                                    (new Atom('Va@1'))
                                ))),
                                (new Tuple(array(
                                    (new Atom('integer')),
                                    0,
                                    2
                                )))
                            ))))
                        )))
                    )
                )))
            )
        );
        
        $decoded = (new Decoder())->decode($test);
        $encoded = (new Encoder())->encode($expected);
        
        $this->assertEquals($expected, $decoded);
        $this->assertSame($test, $encoded);
    }
    
    function testToArray(): void {
        $e1 = new NewFun(
            25,
            0,
            '1',
            2,
            (new Atom('test')),
            0,
            35,
            59,
            (new PID((new Atom('ok')), 0, 0, 1)),
            array()
        );
        $e2 = array(
            'size' => 25,
            'arity' => 0,
            'uniq' => '1',
            'index' => 2,
            'module' => array('atom' => 'test'),
            'numFree' => 0,
            'oldIndex' => 35,
            'oldUniq' => 59,
            'pid' => array(
                'node' => array('atom' => 'ok'),
                'id' => 0,
                'serial' => 0,
                'creation' => 1
            ),
            'freeVars' => array()
        );
        
        $this->assertSame($e2, $e1->toArray());
        $this->assertEquals($e1, NewFun::fromArray($e2));
    }
}
