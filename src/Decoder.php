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
 * Erlang Term Format Decoder.
 */
class Decoder {
    /**
     * @var \GMP
     */
    protected static $gmpTop = null;
    
    /**
     * @var \GMP
     */
    protected static $gmpBottom = null;
    
    /**
     * Constructor.
     * @codeCoverageIgnore
     */
    function __construct() {
        if(static::$gmpTop === null) {
            static::$gmpTop = \gmp_init(((string) \PHP_INT_MAX));
            static::$gmpBottom = \gmp_init(((string) \PHP_INT_MIN));
        }
    }
    
    /**
     * Decodes binary ETF to a PHP usable format.
     * @param string  $message
     * @return mixed
     * @throws Exception
     * @throws UnknownTagException
     */
    function decode(string $message) {
        if($message[0] !== ETF::ETF_VERSION) {
            throw new Exception('ETF version mismatch, expected '.\ord(ETF::ETF_VERSION).' got '.\ord($message[0]));
        }
        
        $intlen = \strlen($message);
        $output = null;
        
        for($pos = 1; $pos < $intlen; $pos++) {
            $output = $this->parseAny($message[$pos], $message, $pos);
        }
        
        return $output;
    }
    
    
    
    /**
     * @param mixed   $input
     * @param string  $data
     * @param int     $pos
     * @return mixed
     * @internal
     */
    function parseAny($input, string $data, int &$pos) {
        if(\strlen($data) <= $pos) {
            throw new Exception('Unexpected end of data');
        }
        
        $pos++;
        switch($input) {
            case ETF::SMALL_INTEGER_EXT:
                return $this->parseSmallInt($data, $pos);
            break;
            case ETF::INTEGER_EXT:
                return $this->parseInt($data, $pos);
            break;
            case ETF::FLOAT_EXT:
                return $this->parseFloat($data, $pos);
            break;
            case ETF::REFERENCE_EXT:
                return Reference::decode($this, $data, $pos);
            break;
            case ETF::PORT_EXT:
                return Port::decode($this, $data, $pos);
            break;
            case ETF::NEW_PORT_EXT:
                return NewPort::decode($this, $data, $pos);
            break;
            case ETF::PID_EXT:
                return PID::decode($this, $data, $pos);
            break;
            case ETF::NEW_PID_EXT:
                return NewPID::decode($this, $data, $pos);
            break;
            case ETF::SMALL_TUPLE_EXT:
                return Tuple::decodeSmall($this, $data, $pos);
            break;
            case ETF::LARGE_TUPLE_EXT:
                return Tuple::decodeLarge($this, $data, $pos);
            break;
            case ETF::MAP_EXT:
                return $this->parseMap($data, $pos);
            break;
            case ETF::NIL_EXT:
                $pos--;
                return array();
            break;
            case ETF::STRING_EXT:
                return $this->parseString($data, $pos);
            break;
            case ETF::LIST_EXT:
                return $this->parseList($data, $pos);
            break;
            case ETF::BINARY_EXT:
                return $this->parseBinary($data, $pos);
            break;
            case ETF::SMALL_BIG_EXT:
                return $this->parseSmallBig($data, $pos);
            break;
            case ETF::LARGE_BIG_EXT:
                return $this->parseLargeBig($data, $pos);
            break;
            case ETF::NEW_REFERENCE_EXT:
                return NewReference::decode($this, $data, $pos);
            break;
            case ETF::NEWER_REFERENCE_EXT:
                return NewerReference::decode($this, $data, $pos);
            break;
            case ETF::FUN_EXT;
                return Fun::decode($this, $data, $pos);
            break;
            case ETF::NEW_FUN_EXT:
                return NewFun::decode($this, $data, $pos);
            break;
            case ETF::EXPORT_EXT:
                return Export::decode($this, $data, $pos);
            break;
            case ETF::BIT_BINARY_EXT:
                return BitBinary::decode($this, $data, $pos);
            break;
            case ETF::NEW_FLOAT_EXT;
                return $this->parseNewFloat($data, $pos);
            break;
            case ETF::ATOM_UTF8_EXT:
                return Atom::decodeAtomUtf8($this, $data, $pos);
            break;
            case ETF::SMALL_ATOM_UTF8_EXT:
                return Atom::decodeSmallAtomUtf8($this, $data, $pos);
            break;
            case ETF::ATOM_EXT:
                return Atom::decodeAtom($this, $data, $pos);
            break;
            case ETF::SMALL_ATOM_EXT:
                return Atom::decodeSmallAtom($this, $data, $pos);
            break;
            default:
                throw new UnknownTagException('Unknown tag "'.$input.'" ('.\bindec($input).')');
            break;
        }
    }
    
    /**
     * @param string  $data
     * @param int     $pos
     * @return int
     */
    protected function parseSmallInt(string $data, int &$pos) {
        return \ord($data[$pos]);
    }
    
    /**
     * @param string  $data
     * @param int     $pos
     * @return int
     */
    protected function parseInt(string $data, int &$pos) {
        $value = \unpack('N', $data[$pos++].$data[$pos++].$data[$pos++].$data[$pos])[1];
        
        if($value & 0x80000000) {
            $value = -2147483648 + ($value & 0x7fffffff);
        }
        
        return $value;
    }
    
    /**
     * @param string  $data
     * @param int     $pos
     * @return float
     */
    protected function parseFloat(string $data, int &$pos) {
        $pos--;
        
        $float = '';
        for($i = 31; $i > 0; $i--) {
            $float .= $data[++$pos];
        }
        
        return \sscanf($float, '%20e')[0];
    }
    
    /**
     * @param string  $data
     * @param int     $pos
     * @return array
     */
    protected function parseMap(string $data, int &$pos) {
        $length = \unpack('N', $data[$pos++].$data[$pos++].$data[$pos++].$data[$pos])[1];
        
        $map = array();
        for(; $length > 0; $length--) {
            $pos++;
            $key = $this->parseAny($data[$pos], $data, $pos);
            
            if($key instanceof Atom) {
                $key = ':'.((string) $key);
            } else {
                $key = ((string) ($key === null ? ':nil' : ($key === true ? ':true' : ($key === false ? ':false' : $key))));
            }
            
            $pos++;
            $map[$key] = $this->parseAny($data[$pos], $data, $pos);
        }
        
        return $map;
    }
    
    /**
     * @param string  $data
     * @param int     $pos
     * @return int[]
     */
    protected function parseString(string $data, int &$pos) {
        $length = \unpack('n', $data[$pos++].$data[$pos])[1];
        
        $str = array();
        for(; $length > 0; $length--) {
            $str[] = \ord($data[++$pos]);
        }
        
        return $str;
    }
    
    /**
     * @param string  $data
     * @param int     $pos
     * @return array
     */
    protected function parseList(string $data, int &$pos) {
        $length = \unpack('N', $data[$pos++].$data[$pos++].$data[$pos++].$data[$pos])[1];
        
        $list = array();
        for(; $length > 0; $length--) {
            $pos++;
            $list[] = $this->parseAny($data[$pos], $data, $pos);
        }
        
        try {
            $peek = $pos + 1;
            
            $tail = $this->parseAny($data[$peek], $data, $peek);
            if($tail !== array()) {
                $list['tail'] = $tail;
            }
            
            $pos = $peek;
        } catch (Exception $e) { // @codeCoverageIgnore
            /* Continue regardless of error */
        }
        
        return $list;
    }
    
    /**
     * @param string  $data
     * @param int     $pos
     * @return string
     */
    protected function parseBinary(string $data, int &$pos) {
        $length = \unpack('N', $data[$pos++].$data[$pos++].$data[$pos++].$data[$pos])[1];
        
        $binary = '';
        for(; $length > 0; $length--) {
            $binary .= $data[++$pos];
        }
        
        return $binary;
    }
    
    /**
     * @param string  $data
     * @param int     $pos
     * @return int|string
     */
    protected function parseSmallBig(string $data, int &$pos) {
        $length = \ord($data[$pos++]);
        return $this->parseBig($data, $pos, $length, \ord($data[$pos]));
    }
    
    /**
     * @param string  $data
     * @param int     $pos
     * @return string
     */
    protected function parseLargeBig(string $data, int &$pos) {
        $length = \unpack('N', $data[$pos++].$data[$pos++].$data[$pos++].$data[$pos++])[1];
        
        return $this->parseBig($data, $pos, $length, \ord($data[$pos]));
    }
    
    /**
     * @param string  $data
     * @param int     $pos
     * @param int     $length
     * @param int     $sign
     * @return int|string
     */
    protected function parseBig(string $data, int &$pos, int $length, int $sign) {
        $b = \gmp_init('256', 10);
        $exp = 0;
        
        $int = \gmp_init('0', 10);
        for(; $length > 0; $length--) {
            $ex = \gmp_pow($b, $exp);
            $mul = \gmp_mul(\ord($data[++$pos]), $ex);
            
            $int = \gmp_add($int, $mul);
            $exp++;
        }
        
        if($sign === 1) {
            $int = \gmp_init('-'.\gmp_strval($int));
        }
        
        if(\gmp_cmp($int, static::$gmpTop) <= 0 && \gmp_cmp($int, static::$gmpBottom) >= 0) {
            $int = \gmp_intval($int);
        } else {
            $int = \gmp_strval($int);
        }
        
        return $int;
    }
    
    /**
     * @param string  $data
     * @param int     $pos
     * @return float
     */
    protected function parseNewFloat(string $data, int &$pos) {
        return \unpack('E', $data[$pos++].$data[$pos++].$data[$pos++].$data[$pos++].
                              $data[$pos++].$data[$pos++].$data[$pos++].$data[$pos])[1];
    }
}