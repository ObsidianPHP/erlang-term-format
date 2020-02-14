<?php
/**
 * ETF
 * Copyright 2020 ObsidianPHP, All Rights Reserved
 *
 * Website: https://github.com/ObsidianPHP/ETF
 * License: https://github.com/ObsidianPHP/ETF/blob/master/LICENSE
*/

namespace Obsidian\ETF;

/**
 * ETF Bit Binary.
 */
class BitBinary extends BaseObject {
    /**
     * How many bits are used in an byte.
     * @var int
     */
    public $bits;
    
    /**
     * The bytes.
     * @var int[]
     */
    public $bytes;
    
    /**
     * Constructor.
     * @param int    $bits
     * @param int[]  $bytes
     */
    function __construct(int $bits, array $bytes) {
        $this->bits = $bits;
        $this->bytes = $bytes;
    }
    
    /**
     * {@inheritdoc}
     */
    function toArray(): array {
        return array(
            'bits' => $this->bits,
            'bytes' => $this->bytes
        );
    }
    
    /**
     * {@inheritdoc}
     * @return self
     */
    static function fromArray($data): BaseObject {
        return (new static($data['bits'], $data['bytes']));
    }
    
    /**
     * {@inheritdoc}
     */
    static function decode(Decoder $etf, string $data, int &$pos) {
        $length = \unpack('N', $data[$pos++].$data[$pos++].$data[$pos++].$data[$pos++])[1];
        
        $bits = \ord($data[$pos]);
        
        $bytes = array();
        for(; $length > 0; $length--) {
            $bin = \ord($data[++$pos]);
            
            if($length === 1) {
                $byte = \str_pad(\decbin($bin), 8, '0', \STR_PAD_LEFT);
                $bin = \bindec(\mb_substr($byte, 0, $bits));
            }
            
            $bytes[] = $bin;
        }
        
        return (new static($bits, $bytes));
    }
    
    /**
     * {@inheritdoc}
     */
    function encode(): string {
        $bits = $this->bytes;
        $bit = \array_pop($bits);
        
        $binbit = \str_pad(\decbin($bit), $this->bits, '0', \STR_PAD_LEFT);
        $byte = $binbit.\str_repeat('0', (8 - $this->bits));
        $bits = \pack('C*', ...$bits).\chr(\bindec($byte));
        
        $length = \pack('N', \count($this->bytes));
        return ETF::BIT_BINARY_EXT.$length.\chr($this->bits).$bits;
    }
}
