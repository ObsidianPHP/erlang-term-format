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
 * ETF Atom.
 */
class Atom extends BaseObject {
    /**
     * The atom.
     * @var string
     */
    public $atom;
    
    /**
     * Constructor.
     * @param string  $atom
     * @throws Exception
     */
    function __construct(string $atom) {
        if(\mb_strlen($atom) > 255) {
            throw new Exception('Atom length must be smaller than 256 characters');
        }
        
        $this->atom = $atom;
    }
    
    /**
     * {@inheritdoc}
     */
    function toArray(): array {
        return array(
            'atom' => $this->atom
        );
    }
    
    /**
     * Returns the atom.
     * @return string
     */
    function __toString() {
        return $this->atom;
    }
    
    /**
     * {@inheritdoc}
     * @return self
     */
    static function fromArray($data): BaseObject {
        return (new static($data['atom']));
    }
    
    /**
     * This method will create an atom of the input.
     * @param string|bool|null  $data
     * @return Atom
     */
    static function from($data): Atom {
        if($data === true) {
            return (new static('true'));
        } elseif($data === false) {
            return (new static('false'));
        } elseif($data === null) {
            return (new static('nil'));
        } else {
            return (new static($data));
        }
    }
    
    /**
     * This method will create an atom (or bool or null) of the atomic input.
     * @param string  $data
     * @return Atom|bool|null
     */
    static function to(string $data) {
        switch($data) {
            case 'true': // @codeCoverageIgnore
                return true;
            break;
            case 'false': // @codeCoverageIgnore
                return false;
            break;
            case 'nil': // @codeCoverageIgnore
                return null;
            break;
            default: // @codeCoverageIgnore
                return (new static($data));
            break;
        }
    }
    
    /**
     * {@inheritdoc}
     * @return self|bool|null
     */
    static function decode(Decoder $etf, string $data, int &$pos) {
        $tag = (isset($data[($pos - 1)]) ? $data[($pos - 1)] : null);
        
        switch($tag) {
            case ETF::ATOM_UTF8_EXT:
                $atom = static::decodeAtomUtf8($etf, $data, $pos);
            break;
            case ETF::SMALL_ATOM_UTF8_EXT:
                $atom = static::decodeSmallAtomUtf8($etf, $data, $pos);
            break;
            case ETF::ATOM_EXT:
                $atom = static::decodeAtom($etf, $data, $pos);
            break;
            case ETF::SMALL_ATOM_EXT:
                $atom = static::decodeSmallAtom($etf, $data, $pos);
            break;
            default:
                throw new UnknownTagException('Invalid atom tag "'.$tag.'"');
            break;
        }
        
        return $atom;
    }
    
    /**
     * Decodes the ETF bytes array to an object, but with incrementing position.
     * @param Decoder  $etf
     * @param string   $data
     * @param int      $pos
     * @return self
     */
    static function decodeIncrement(Decoder $etf, string $data, int &$pos) {
        $pos++;
        
        return static::decode($etf, $data, $pos);
    }
    
    /**
     * Decodes the ETF bytes array to an object.
     * @param Decoder  $etf
     * @param string   $data
     * @param int      $pos
     * @return self|bool|null
     * @noinspection PhpUnusedParameterInspection
     */
    static function decodeAtomUtf8(Decoder $etf, string $data, int &$pos) {
        $length = \unpack('n', $data[$pos++].$data[$pos])[1];
        
        $atom = '';
        for(; $length > 0; $length--) {
            $atom .= $data[++$pos];
        }
        
        return static::to($atom);
    }
    
    /**
     * Decodes the ETF bytes array to an object.
     * @param Decoder  $etf
     * @param string   $data
     * @param int      $pos
     * @return self|bool|null
     * @noinspection PhpUnusedParameterInspection
     */
    static function decodeSmallAtomUtf8(Decoder $etf, string $data, int &$pos) {
        $length = \ord($data[$pos]);
        
        $atom = '';
        for(; $length > 0; $length--) {
            $atom .= $data[++$pos];
        }
        
        return static::to($atom);
    }
    
    /**
     * Decodes the ETF bytes array to an object.
     * @param Decoder  $etf
     * @param string   $data
     * @param int      $pos
     * @return self|bool|null
     * @noinspection PhpUnusedParameterInspection
     */
    static function decodeAtom(Decoder $etf, string $data, int &$pos) {
        $length = \unpack('n', $data[$pos++].$data[$pos])[1];
        
        $atom = '';
        for(; $length > 0; $length--) {
            $atom .= $data[++$pos];
        }
        
        return static::to($atom);
    }
    
    /**
     * Decodes the ETF bytes array to an object.
     * @param Decoder  $etf
     * @param string   $data
     * @param int      $pos
     * @return self|bool|null
     * @noinspection PhpUnusedParameterInspection
     */
    static function decodeSmallAtom(Decoder $etf, string $data, int &$pos) {
        $length = \ord($data[$pos]);
        
        $atom = '';
        for(; $length > 0; $length--) {
            $atom .= $data[++$pos];
        }
        
        return static::to($atom);
    }
    
    /**
     * {@inheritdoc}
     */
    function encode(): string {
        if(\strlen($this->atom) > 255) {
            return $this->encodeAtomUtf8($this->atom);
        }
        
        return $this->encodeSmallAtomUtf8($this->atom);
    }
    
    /**
     * Encodes the object to ETF bytes array, using the regular utf8 atom encoding.
     * @return string
     */
    function encodeBig(): string {
        return $this->encodeAtomUtf8($this->atom);
    }
    
    /**
     * Encodes the object to ETF bytes array, using the regular small latin atom encoding.
     * @return string
     */
    function encodeSmallLatin(): string {
        return $this->encodeSmallAtom($this->atom);
    }
    
    /**
     * Encodes the object to ETF bytes array, using the regular latin atom encoding.
     * @return string
     */
    function encodeLatin(): string {
        return $this->encodeAtom($this->atom);
    }
    
    /**
     * @param string  $data
     * @return string
     */
    protected function encodeAtomUtf8(string $data) {
        return ETF::ATOM_UTF8_EXT.\pack('n', \strlen($data)).$data;
    }
    
    /**
     * @param string  $data
     * @return string
     */
    protected function encodeSmallAtomUtf8(string $data) {
        return ETF::SMALL_ATOM_UTF8_EXT.\pack('C', \strlen($data)).$data;
    }
    
    /**
     * @param string  $data
     * @return string
     */
    protected function encodeAtom(string $data) {
        return ETF::ATOM_EXT.\pack('n', \strlen($data)).$data;
    }
    
    /**
     * @param string  $data
     * @return string
     */
    protected function encodeSmallAtom(string $data) {
        return ETF::SMALL_ATOM_EXT.\pack('C', \strlen($data)).$data;
    }
}
