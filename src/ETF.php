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
 * This piece of class is the interface to the encoder and decoder for Erlang's External Term Format (ETF).
 */
class ETF {
    /**
     * Supported ETF version.
     * @source
     */
    const ETF_VERSION = "\x83"; // 131
    
    /**
     * = integer.
     * @source
     */
    const SMALL_INTEGER_EXT = "\x61"; // 97
    
    /**
     * = integer.
     * @source
     */
    const INTEGER_EXT = "\x62"; // 98
    
    /**
     * = float.
     * @source
     */
    const FLOAT_EXT = "\x63"; // 99
    
    /**
     * = Atom|bool|null.
     * @source
     */
    const ATOM_EXT = "\x64"; // 100
    
    /**
     * = Reference.
     * @source
     */
    const REFERENCE_EXT = "\x65"; // 101
    
    /**
     * = Port.
     * @source
     */
    const PORT_EXT = "\x66"; // 102
    
    /**
     * = NewPort.
     * @source
     */
    const NEW_PORT_EXT = "\x59"; // 89
    
    /**
     * = PID.
     * @source
     */
    const PID_EXT = "\x67"; // 103
    
    /**
     * = NewPID.
     * @source
     */
    const NEW_PID_EXT = "\x58"; // 88
    
    /**
     * = Tuple.
     * @source
     */
    const SMALL_TUPLE_EXT = "\x68"; // 104
    
    /**
     * = Tuple.
     * @source
     */
    const LARGE_TUPLE_EXT = "\x69"; // 105
    
    /**
     * = array. Atom keys will start with `:`.
     * @source
     */
    const MAP_EXT = "\x74"; // 116
    
    /**
     * = empty array.
     * @source
     */
    const NIL_EXT = "\x6A"; // 106
    
    /**
     * =  integer[].
     * @source
     */
    const STRING_EXT = "\x6B"; // 107
    
    /**
     * = array. May have an element keyed with tail at the end to mark an improper list.
     * @source
     */
    const LIST_EXT = "\x6C"; // 108
    
    /**
     * = string.
     * @source
     */
    const BINARY_EXT = "\x6D"; // 109
    
    /**
     * = integer|string.
     * @source
     */
    const SMALL_BIG_EXT = "\x6E"; // 110
    
    /**
     * = string.
     * @source
     */
    const LARGE_BIG_EXT = "\x6F"; // 111
    
    /**
     * = NewReference.
     * @source
     */
    const NEW_REFERENCE_EXT = "\x72"; // 114
    
    /**
     * = NewerReference.
     * @source
     */
    const NEWER_REFERENCE_EXT = "\x5A"; // 90
    
    /**
     * = Atom.
     * @source
     */
    const SMALL_ATOM_EXT = "\x73"; // 115
    
    /**
     * = Fun.
     * @source
     */
    const FUN_EXT = "\x75"; // 117
    
    /**
     * = NewFun.
     * @source
     */
    const NEW_FUN_EXT = "\x70"; // 112
    
    /**
     * = Export.
     * @source
     */
    const EXPORT_EXT = "\x71"; // 113
    
    /**
     * = BitBinary.
     * @source
     */
    const BIT_BINARY_EXT = "\x4D"; // 77
    
    /**
     * = float.
     * @source
     */
    const NEW_FLOAT_EXT = "\x46"; // 70
    
    /**
     * = Atom.
     * @source
     */
    const ATOM_UTF8_EXT = "\x76"; // 118
    
    /**
     * = Atom.
     * @source
     */
    const SMALL_ATOM_UTF8_EXT = "\x77"; // 119
    
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
        if($message[0] !== static::ETF_VERSION) {
            throw new Exception('ETF version mismatch, expected '.\ord(static::ETF_VERSION).' got '.\ord($message[0]));
        }
        
        $intlen = \strlen($message);
        $output = null;
        
        for($pos = 1; $pos < $intlen; $pos++) {
            $output = $this->parseAny($message[$pos], $message, $pos);
        }
        
        return $output;
    }
    
    /**
     * Encodes PHP data into binary ETF.
     * @param mixed  $data
     * @return string
     * @throws Exception
     */
    function encode($data) {
        return static::ETF_VERSION.static::encodeAny($data);
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
            case static::SMALL_INTEGER_EXT:
                return $this->parseSmallInt($data, $pos);
            break;
            case static::INTEGER_EXT:
                return $this->parseInt($data, $pos);
            break;
            case static::FLOAT_EXT:
                return $this->parseFloat($data, $pos);
            break;
            case static::REFERENCE_EXT:
                return Reference::decode($this, $data, $pos);
            break;
            case static::PORT_EXT:
                return Port::decode($this, $data, $pos);
            break;
            case static::NEW_PORT_EXT:
                return NewPort::decode($this, $data, $pos);
            break;
            case static::PID_EXT:
                return PID::decode($this, $data, $pos);
            break;
            case static::NEW_PID_EXT:
                return NewPID::decode($this, $data, $pos);
            break;
            case static::SMALL_TUPLE_EXT:
                return Tuple::decodeSmall($this, $data, $pos);
            break;
            case static::LARGE_TUPLE_EXT:
                return Tuple::decodeLarge($this, $data, $pos);
            break;
            case static::MAP_EXT:
                return $this->parseMap($data, $pos);
            break;
            case static::NIL_EXT:
                $pos--;
                return array();
            break;
            case static::STRING_EXT:
                return $this->parseString($data, $pos);
            break;
            case static::LIST_EXT:
                return $this->parseList($data, $pos);
            break;
            case static::BINARY_EXT:
                return $this->parseBinary($data, $pos);
            break;
            case static::SMALL_BIG_EXT:
                return $this->parseSmallBig($data, $pos);
            break;
            case static::LARGE_BIG_EXT:
                return $this->parseLargeBig($data, $pos);
            break;
            case static::NEW_REFERENCE_EXT:
                return NewReference::decode($this, $data, $pos);
            break;
            case static::NEWER_REFERENCE_EXT:
                return NewerReference::decode($this, $data, $pos);
            break;
            case static::FUN_EXT;
                return Fun::decode($this, $data, $pos);
            break;
            case static::NEW_FUN_EXT:
                return NewFun::decode($this, $data, $pos);
            break;
            case static::EXPORT_EXT:
                return Export::decode($this, $data, $pos);
            break;
            case static::BIT_BINARY_EXT:
                return BitBinary::decode($this, $data, $pos);
            break;
            case static::NEW_FLOAT_EXT;
                return $this->parseNewFloat($data, $pos);
            break;
            case static::ATOM_UTF8_EXT:
                return Atom::decodeAtomUtf8($this, $data, $pos);
            break;
            case static::SMALL_ATOM_UTF8_EXT:
                return Atom::decodeSmallAtomUtf8($this, $data, $pos);
            break;
            case static::ATOM_EXT:
                return Atom::decodeAtom($this, $data, $pos);
            break;
            case static::SMALL_ATOM_EXT:
                return Atom::decodeSmallAtom($this, $data, $pos);
            break;
            default:
                throw new UnknownTagException('Unknown tag "'.$input.'" ('.\bindec($input).')');
            break;
        }
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
                    return static::NIL_EXT;
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
    
    /**
     * @param mixed  $data
     * @return string
     */
    protected static function encodeSmallInt($data) {
        return static::SMALL_INTEGER_EXT.\chr($data);
    }
    
    /**
     * @param mixed  $data
     * @return string
     */
    protected static function encodeInt($data) {
        return static::INTEGER_EXT.\pack('N', $data);
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
        return static::MAP_EXT.$length.$map;
    }
    
    /**
     * @param array  $data
     * @return string
     * @internal
     */
    protected static function encodeString(array $data) {
        $binlen = \pack('n', \count($data));
        return static::STRING_EXT.$binlen.\pack('C*', ...$data);
        
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
        return static::LIST_EXT.$length.$list.($tail !== null ? $tail : static::NIL_EXT);
    }
    
    /**
     * @param string  $data
     * @return string
     */
    protected static function encodeBinary(string $data) {
        return static::BINARY_EXT.\pack('N', \strlen($data)).$data;
    }
    
    /**
     * @param \GMP  $data
     * @return string
     */
    protected static function encodeSmallBig(\GMP $data) {
        $sign = (\gmp_cmp($data, '0') >= 0 ? 0 : 1);
        $ints = static::encodeBig($data, $sign);
        
        return static::SMALL_BIG_EXT.\chr(\strlen($ints)).\chr($sign).$ints;
    }
    
    /**
     * @param \GMP  $data
     * @return string
     */
    protected static function encodeLargeBig(\GMP $data) {
        $sign = (\gmp_cmp($data, '0') >= 0 ? 0 : 1);
        
        $data = \gmp_abs($data);
        $ints = static::encodeBig($data, $sign);
        
        return static::LARGE_BIG_EXT.\pack('N', \strlen($ints)).\chr($sign).$ints;
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
        return static::NEW_FLOAT_EXT.\pack('E', $data);
    }
}
