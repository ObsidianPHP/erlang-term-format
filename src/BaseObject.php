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
 * Generic ETF Object.
 */
abstract class BaseObject implements \ArrayAccess {
    /**
     * Converts object to an array.
     * @return array
     */
    abstract function toArray(): array;
    
    /**
     * Converts array to an object.
     * @param array  $data
     * @return static
     */
    abstract static function fromArray($data): self;
    
    /**
     * Decodes the ETF bytes array to an object.
     * @param ETF     $etf
     * @param string  $data
     * @param int     $pos
     * @return static
     */
    abstract static function decode(ETF $etf, string $data, int &$pos);
    
    /**
     * Encodes the object to ETF bytes array.
     * @return string
     * @throws Exception
     */
    abstract function encode(): string;

    /**
     * @codeCoverageIgnore
     * @param int|string  $offset
     * @return mixed|null
     * @internal
     */
    function offsetGet($offset) {
        if(\property_exists($this, $offset)) {
            return $this->$offset;
        }
        
        return null;
    }
    
    /**
     * @codeCoverageIgnore
     * @param int|string  $offset
     * @param mixed       $value
     * @return void
     * @internal
     */
    function offsetSet($offset, $value) {
        if(\is_null($offset)) {
            throw new \InvalidArgumentException('Invalid offset for Object');
        }
        
        $this->$offset = $value;
    }
    
    /**
     * @codeCoverageIgnore
     * @param int|string  $offset
     * @return bool
     * @internal
     */
    function offsetExists($offset) {
        return \property_exists($this, $offset);
    }
    
    /**
     * @codeCoverageIgnore
     * @param int|string  $offset
     * @return void
     * @internal
     */
    function offsetUnset($offset) {
        unset($this->$offset);
    }
}
