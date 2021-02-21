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
     * Numeric strings are encoded as numbers.
     * @source
     */
    const ENCODE_NUMERIC_STRING_AS_NUMBER = 0x1;

    /**
     * Encodes only directly given strings as binary.
     * Numeric strings in sub-elements are encoded as number.
     * @source
     */
    const ENCODE_DIRECT_STRINGS_AS_BINARY = 0x3;

    /**
     * Encodes all strings always as binary.
     * @source
     */
    const ENCODE_STRINGS_AS_BINARY = 0x9;

    /**
     * @var \GMP
     */
    protected static $gmpTop;
    
    /**
     * @var \GMP
     */
    protected static $gmpBottom;

    /**
     * @var int
     */
    protected $encodeStrings;
    
    /**
     * Constructor.
     * @param int  $encodeStrings  Controls how strings are handled. By default, numeric strings get encoded as numbers.
     * @codeCoverageIgnore
     */
    function __construct(int $encodeStrings = self::ENCODE_NUMERIC_STRING_AS_NUMBER) {
        if(static::$gmpTop === null) {
            static::$gmpTop = \gmp_init(((string) \PHP_INT_MAX));
            static::$gmpBottom = \gmp_init(((string) \PHP_INT_MIN));
        }

        $this->encodeStrings = $encodeStrings;
    }
    
    /**
     * Encodes PHP data into binary ETF.
     * @param mixed  $data
     * @return string
     * @throws Exception
     */
    function encode($data): string {
        return ETF::ETF_VERSION.$this->encodeAny($data, true);
    }

    /**
     * @param mixed $input
     * @param bool $external
     * @return string
     * @internal
     */
    function encodeAny($input, bool $external): string {
        $type = \gettype($input);
        
        switch($type) {
            case 'boolean': // @codeCoverageIgnore
            case 'NULL': // @codeCoverageIgnore
                return Atom::from($input)->encode($this);
            case 'integer': // @codeCoverageIgnore
                if ($input <= 255 && $input >= 0) {
                    return $this->encodeSmallInt($input);
                }

                if(-2147483648 <= $input && $input <= 2147483647) {
                    return $this->encodeInt($input);
                }

                $gmp = \gmp_init(((string) $input));

                if(\gmp_cmp($gmp, static::$gmpTop) <= 0 && \gmp_cmp($gmp, static::$gmpBottom) >= 0) {
                    return $this->encodeSmallBig($gmp);
                }

                throw new Exception('Large bignums must be passed as string'); // @codeCoverageIgnore
            case 'double': // @codeCoverageIgnore
            case 'float': // @codeCoverageIgnore
                return $this->encodeNewFloat($input);
            case 'string': // @codeCoverageIgnore
                if(
                    (
                        $this->encodeStrings === self::ENCODE_NUMERIC_STRING_AS_NUMBER ||
                        (!$external && $this->encodeStrings === self::ENCODE_DIRECT_STRINGS_AS_BINARY)
                    ) && \is_numeric($input) && \preg_match('/[^0-9\-]/u', $input) === 0
                ) {
                    $gmp = @\gmp_init($input);
                    
                    if($gmp instanceof \GMP) {
                        if(\gmp_cmp($gmp, static::$gmpTop) <= 0 && \gmp_cmp($gmp, static::$gmpBottom) >= 0) {
                            return $this->encodeSmallBig($gmp);
                        }
                        
                        return $this->encodeLargeBig($gmp);
                    }
                }
                
                return $this->encodeBinary($input);
            case 'object': // @codeCoverageIgnore
                if($input instanceof BaseObject) {
                    return $input->encode($this);
                }
                
                return $this->encodeAny(((array) $input), false);
            case 'array': // @codeCoverageIgnore
                if(\count($input) === 0) {
                    return ETF::NIL_EXT;
                }

                $keysNumeric = true;
                $smallInts = null;

                \end($input);
                $lastKey = \key($input);
                \reset($input);

                foreach($input as $key => $val) {
                    $smallInts = ($smallInts !== false && \is_int($val) && $val >= 0 && $val <= 255);

                    if(!\is_int($key) && ($key !== 'tail' || $lastKey !== 'tail')) {
                        $keysNumeric = false;
                        break;
                    }
                }

                if($keysNumeric) {
                    if($smallInts && \count($input) <= 65535) {
                        return $this->encodeString($input);
                    }

                    return $this->encodeList($input);
                }

                return $this->encodeMap($input);
            case 'resource': // @codeCoverageIgnore
            case 'resource (closed)': // @codeCoverageIgnore
                $data = Reference::fromArray(array('node' => array('atom' => \get_resource_type($input)), 'id' => ((int) $input), 'creation' => 0));
                return $data->encode($this);
            default: // @codeCoverageIgnore
                throw new Exception('Can not encode type "'.$type.'"'); // @codeCoverageIgnore
        }
    }
    
    /**
     * @param mixed  $data
     * @return string
     */
    protected function encodeSmallInt($data): string {
        return ETF::SMALL_INTEGER_EXT.\chr($data);
    }
    
    /**
     * @param mixed  $data
     * @return string
     */
    protected function encodeInt($data): string {
        return ETF::INTEGER_EXT.\pack('N', $data);
    }
    
    /**
     * @param mixed  $data
     * @return string
     */
    protected function encodeMap($data): string {
        $map = '';
        foreach($data as $key => $value) {
            if($key[0] === ':') {
                $map .= Atom::fromArray(array('atom' => \mb_substr($key, 1)))->encode($this);
            } else {
                $map .= $this->encodeAny($key, false);
            }
            
            $map .= $this->encodeAny($value, false);
        }
        
        $length = \pack('N', ((int) \ceil(\count($data))));
        return ETF::MAP_EXT.$length.$map;
    }
    
    /**
     * @param array  $data
     * @return string
     * @internal
     */
    protected function encodeString(array $data): string {
        $binlen = \pack('n', \count($data));
        return ETF::STRING_EXT.$binlen.\pack('C*', ...$data);
        
    }
    
    /**
     * @param array  $data
     * @return string
     */
    protected function encodeList(array $data): string {
        $size = \count($data);
        
        $list = '';
        $tail = null;
        
        foreach($data as $key => $value) {
            if($key === 'tail') {
                $size--;
                $tail = $this->encodeAny($value, false);
            } else {
                $list .= $this->encodeAny($value, false);
            }
        }
        
        $length = \pack('N', $size);
        return ETF::LIST_EXT.$length.$list.($tail ?? ETF::NIL_EXT);
    }
    
    /**
     * @param string  $data
     * @return string
     */
    protected function encodeBinary(string $data): string {
        return ETF::BINARY_EXT.\pack('N', \strlen($data)).$data;
    }
    
    /**
     * @param \GMP  $data
     * @return string
     */
    protected function encodeSmallBig(\GMP $data): string {
        $sign = (\gmp_cmp($data, '0') >= 0 ? 0 : 1);
        $ints = $this->encodeBig($data, $sign);
        
        return ETF::SMALL_BIG_EXT.\chr(\strlen($ints)).\chr($sign).$ints;
    }
    
    /**
     * @param \GMP  $data
     * @return string
     */
    protected function encodeLargeBig(\GMP $data): string {
        $sign = (\gmp_cmp($data, '0') >= 0 ? 0 : 1);
        
        $data = \gmp_abs($data);
        $ints = $this->encodeBig($data, $sign);
        
        return ETF::LARGE_BIG_EXT.\pack('N', \strlen($ints)).\chr($sign).$ints;
    }
    
    /**
     * @param \GMP  $data
     * @param int   $sign
     * @return string
     */
    protected function encodeBig(\GMP $data, int $sign): string {
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
    protected function encodeNewFloat(float $data): string {
        return ETF::NEW_FLOAT_EXT.\pack('E', $data);
    }
}
