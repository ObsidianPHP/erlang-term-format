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
 * ETF Tuple.
 */
class Tuple extends BaseObject implements \ArrayAccess {
    /**
     * The tuple entries.
     * @var array
     */
    public $entries;
    
    /**
     * Constructor.
     * @param array  $entries
     */
    function __construct(array $entries) {
        $this->entries = $entries;
    }
    
    /**
     * {@inheritdoc}
     */
    function toArray(): array {
        return $this->entries;
    }
    
    /**
     * {@inheritdoc}
     * @return self
     */
    static function fromArray($data): BaseObject {
        return (new static($data));
    }
    
    /**
     * {@inheritdoc}
     */
    static function decode(Decoder $etf, string $data, int &$pos) {
        if(isset($data[($pos - 1)]) && $data[($pos - 1)] === ETF::SMALL_TUPLE_EXT) {
            return static::decodeSmall($etf, $data, $pos);
        }
        
        return static::decodeLarge($etf, $data, $pos);
    }
    
    /**
     * Decodes the ETF bytes array (a small tuple) to an object.
     * @param Decoder  $etf
     * @param string   $data
     * @param int      $pos
     * @return BaseObject
     */
    static function decodeSmall(Decoder $etf, string $data, int &$pos) {
         $length = \ord($data[$pos]);
         
         $tuple = array();
         for(; $length > 0; $length--) {
             $pos++;
             $tuple[] = $etf->parseAny($data[$pos], $data, $pos);
         }
         
         return (new static($tuple));
    }
    
    /**
     * Decodes the ETF bytes array (a large tuple) to an object.
     * @param Decoder  $etf
     * @param string   $data
     * @param int      $pos
     * @return BaseObject
     */
     static function decodeLarge(Decoder $etf, string $data, int &$pos) {
         $length = \unpack('N', $data[$pos++].$data[$pos++].$data[$pos++].$data[$pos])[1];
         
         $tuple = array();
         for(; $length > 0; $length--) {
             $tuple[] = $etf->parseAny($data[++$pos], $data, $pos);
         }
         
         return (new static($tuple));
     }
    
    /**
     * {@inheritdoc}
     */
    function encode(): string {
        $countEntries = \count($this->entries);
        
        if($countEntries < 256) {
            $tuple = '';
            foreach($this->entries as $value) {
                $tuple .= Encoder::encodeAny($value);
            }
            
            $length = \chr($countEntries);
            return ETF::SMALL_TUPLE_EXT.$length.$tuple;
        }
        
        if($countEntries > 4294967296) {
            throw new Exception('Large tuple can not hold more than 4294967296 elements'); // @codeCoverageIgnore
        }
        
        $tuple = '';
        foreach($this->entries as $value) {
            $tuple .= Encoder::encodeAny($value);
        }
        
        $length = \pack('N', $countEntries);
        return ETF::LARGE_TUPLE_EXT.$length.$tuple;
    }
    
    /**
     * @param mixed  $offset
     * @return mixed|null
     * @internal
     */
    function offsetGet($offset) {
        if(\array_key_exists($offset, $this->entries)) {
            return $this->entries[$offset];
        }
        
        return null;
    }
    
    /**
     * @param mixed  $offset
     * @param mixed  $value
     * @internal
     */
    function offsetSet($offset, $value) {
        if(\is_null($offset)) {
            $this->entries[] = $value;
        } else {
            $this->entries[$offset] = $value;
        }
    }
    
    /**
     * @param mixed  $offset
     * @return bool
     * @internal
     */
    function offsetExists($offset) {
        return \array_key_exists($offset, $this->entries);
    }
    
    /**
     * @param mixed  $offset
     * @internal
     */
    function offsetUnset($offset) {
        unset($this->entries[$offset]);
    }
}
