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
 * Erlang Term Format Encoder.
 */
class Encoder {
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
     * Encodes PHP data into binary ETF.
     * @param mixed  $data
     * @return string
     * @throws Exception
     */
    function encode($data) {
        return ETF::ETF_VERSION.static::encodeAny($data);
    }
    
    /**
     * @param mixed  $input
     * @return string
     * @internal
     */
    static function encodeAny($input) {
        $type = \gettype($input);
        
        switch($type) {
            case 'boolean': // @codeCoverageIgnore
            case 'NULL': // @codeCoverageIgnore
                return Atom::from($input)->encode();
            break;
            case 'integer': // @codeCoverageIgnore
                if($input <= 255 && $input >= 0) {
                    return static::encodeSmallInt($input);
                } elseif(-2147483648 <= $input && $input <= 2147483647) {
                    return static::encodeInt($input);
                } else {
                    $gmp = \gmp_init(((string) $input));
                    
                    if(\gmp_cmp($gmp, static::$gmpTop) <= 0 && \gmp_cmp($gmp, static::$gmpBottom) >= 0) {
                        return static::encodeSmallBig($gmp);
                    }
                    
                    throw new Exception('Large bignums must be passed as string'); // @codeCoverageIgnore
                }
            break;
            case 'double': // @codeCoverageIgnore
            case 'float': // @codeCoverageIgnore
                return static::encodeNewFloat($input);
            break;
            case 'string': // @codeCoverageIgnore
                if(\is_numeric($input) && \preg_match('/[^0-9\-]/su', $input) === 0) {
                    $gmp = @\gmp_init($input);
                    
                    if($gmp instanceof \GMP) {
                        if(\gmp_cmp($gmp, static::$gmpTop) <= 0 && \gmp_cmp($gmp, static::$gmpBottom) >= 0) {
                            return static::encodeSmallBig($gmp);
                        }
                        
                        return static::encodeLargeBig($gmp);
                    }
                }
                
                return static::encodeBinary($input);
            break;
            case 'object': // @codeCoverageIgnore
                if($input instanceof BaseObject) {
                    return $input->encode();
                }
                
                return static::encodeAny(((array) $input));
            break;
            case 'array': // @codeCoverageIgnore
                if(\count($input) === 0) {
                    return ETF::NIL_EXT;
                } else {
                    $keysNumeric = true;
                    $smallInts = null;
                    
                    \end($input);
                    $lastKey = \key($input);
                    \reset($input);
                    
                    foreach($input as $key => $val) {
                        $smallInts = ($smallInts !== false && \is_integer($val) && $val >= 0 && $val <= 255);
                        
                        if(!\is_integer($key) && ($key !== 'tail' || $lastKey !== 'tail')) {
                            $keysNumeric = false;
                            break;
                        }
                    }
                    
                    if($keysNumeric) {
                        if($smallInts && \count($input) <= 65535) {
                            return static::encodeString($input);
                        }
                        
                        return static::encodeList($input);
                    }
                    
                    return static::encodeMap($input);
                }
            break;
            case 'resource': // @codeCoverageIgnore
            case 'resource (closed)': // @codeCoverageIgnore
                $data = Reference::fromArray(array('node' => array('atom' => \get_resource_type($input)), 'id' => ((int) $input), 'creation' => 0));
                return $data->encode();
            break;
            default: // @codeCoverageIgnore
                throw new Exception('Can not encode type "'.$type.'"'); // @codeCoverageIgnore
            break;
        }
    }
    
    /**
     * @param mixed  $data
     * @return string
     */
    protected static function encodeSmallInt($data) {
        return ETF::SMALL_INTEGER_EXT.\chr($data);
    }
    
    /**
     * @param mixed  $data
     * @return string
     */
    protected static function encodeInt($data) {
        return ETF::INTEGER_EXT.\pack('N', $data);
    }
    
    /**
     * @param mixed  $data
     * @return string
     */
    protected static function encodeMap($data) {
        $map = '';
        foreach($data as $key => $value) {
            if($key[0] === ':') {
                $map .= Atom::fromArray(array('atom' => \mb_substr($key, 1)))->encode();
            } else {
                $map .= static::encodeAny($key);
            }
            
            $map .= static::encodeAny($value);
        }
        
        $length = \pack('N', ((int) \ceil(\count($data))));
        return ETF::MAP_EXT.$length.$map;
    }
    
    /**
     * @param array  $data
     * @return string
     * @internal
     */
    protected static function encodeString(array $data) {
        $binlen = \pack('n', \count($data));
        return ETF::STRING_EXT.$binlen.\pack('C*', ...$data);
        
    }
    
    /**
     * @param array  $data
     * @return string
     */
    protected static function encodeList(array $data) {
        $size = \count($data);
        
        $list = '';
        $tail = null;
        
        foreach($data as $key => $value) {
            if($key === 'tail') {
                $size--;
                $tail = static::encodeAny($value);
            } else {
                $list .= static::encodeAny($value);
            }
        }
        
        $length = \pack('N', $size);
        return ETF::LIST_EXT.$length.$list.($tail !== null ? $tail : ETF::NIL_EXT);
    }
    
    /**
     * @param string  $data
     * @return string
     */
    protected static function encodeBinary(string $data) {
        return ETF::BINARY_EXT.\pack('N', \strlen($data)).$data;
    }
    
    /**
     * @param \GMP  $data
     * @return string
     */
    protected static function encodeSmallBig(\GMP $data) {
        $sign = (\gmp_cmp($data, '0') >= 0 ? 0 : 1);
        $ints = static::encodeBig($data, $sign);
        
        return ETF::SMALL_BIG_EXT.\chr(\strlen($ints)).\chr($sign).$ints;
    }
    
    /**
     * @param \GMP  $data
     * @return string
     */
    protected static function encodeLargeBig(\GMP $data) {
        $sign = (\gmp_cmp($data, '0') >= 0 ? 0 : 1);
        
        $data = \gmp_abs($data);
        $ints = static::encodeBig($data, $sign);
        
        return ETF::LARGE_BIG_EXT.\pack('N', \strlen($ints)).\chr($sign).$ints;
    }
    
    /**
     * @param \GMP  $data
     * @param int   $sign
     * @return string
     */
    protected static function encodeBig(\GMP $data, int $sign) {
        $data = ($sign === 0 ? $data : \gmp_mul($data, '-1'));
        
        $bytes = '';
        while(\gmp_cmp($data, '0') > 0) {
            $bytes .= \chr(\gmp_intval(\gmp_and($data, 255)));
            $data = \gmp_div_q($data, \gmp_pow(2, 8));
        }
        
        return $bytes;
    }
    
    /**
     * @param float  $data
     * @return string
     */
    protected static function encodeNewFloat(float $data) {
        return ETF::NEW_FLOAT_EXT.\pack('E', $data);
    }
}