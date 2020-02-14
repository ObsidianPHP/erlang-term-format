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
 * ETF New Fun(ction).
 */
class NewFun extends BaseObject {
    /**
     * The total number of bytes, including this field.
     * @var int
     */
    public $size;
    
    /**
     * The arity of the function implementing the fun.
     * @var int
     */
    public $arity;
    
    /**
     * The 16 bytes MD5 of the significant parts of the Beam file.
     * @var string
     */
    public $uniq;
    
    /**
     * An index number. Each fun within a module has an unique index. Index is stored in big-endian byte order.
     * @var int
     */
    public $index;
    
    /**
     * The module that the fun is implemented in.
     * @var Atom|bool
     */
    public $module;
    
    /**
     * The number of free variables.
     * @var int
     */
    public $numFree;
    
    /**
     * 	Is typically a small index into the module's fun table.
     * @var int
     */
    public $oldIndex;
    
    /**
     * oldUniq is the hash value of the parse tree for the fun.
     * @var int
     */
    public $oldUniq;
    
    /**
     * Represents the process in which the fun was created.
     * @var PID
     */
    public $pid;
    
    /**
     * The free variables.
     * @var array
     */
    public $freeVars;
    
    /**
     * Constructor.
     * @param int        $size
     * @param int        $arity
     * @param string     $uniq
     * @param int        $index
     * @param Atom|bool  $module
     * @param int        $numFree
     * @param int        $oldIndex
     * @param int        $oldUniq
     * @param PID        $pid
     * @param array      $freeVars
     */
    function __construct(
        int $size,
        int $arity,
        string $uniq,
        int $index,
        $module,
        int $numFree,
        int $oldIndex,
        int $oldUniq,
        PID $pid,
        array $freeVars
    ) {
        $this->size = $size;
        $this->arity = $arity;
        $this->uniq = $uniq;
        $this->index = $index;
        $this->module = $module;
        $this->numFree = $numFree;
        $this->oldIndex = $oldIndex;
        $this->oldUniq = $oldUniq;
        $this->pid = $pid;
        $this->freeVars = $freeVars;
    }
    
    /**
     * {@inheritdoc}
     */
    function toArray(): array {
        return array(
            'size' => $this->size,
            'arity' => $this->arity,
            'uniq' => $this->uniq,
            'index' => $this->index,
            'module' => $this->module->toArray(),
            'numFree' => $this->numFree,
            'oldIndex' => $this->oldIndex,
            'oldUniq' => $this->oldUniq,
            'pid' => $this->pid->toArray(),
            'freeVars' => $this->freeVars
        );
    }
    
    /**
     * {@inheritdoc}
     * @return self
     */
    static function fromArray($data): BaseObject {
        return (new static(
            $data['size'],
            $data['arity'],
            $data['uniq'],
            $data['index'],
            Atom::fromArray($data['module']),
            $data['numFree'],
            $data['oldIndex'],
            $data['oldUniq'],
            PID::fromArray($data['pid']),
            $data['freeVars']
        ));
    }
    
    /**
     * {@inheritdoc}
     */
    static function decode(Decoder $etf, string $data, int &$pos) {
        $size = \unpack('N', $data[$pos++].$data[$pos++].$data[$pos++].$data[$pos++])[1];
        $endSize = $pos - 4 + $size;
        
        if($endSize !== \strlen($data)) {
            throw new Exception('Mismatch in size of new fun payload'); // @codeCoverageIgnore
        }
        
        $arity = \ord($data[$pos++]);
        
        $uniq = \bin2hex($data[$pos++].$data[$pos++].$data[$pos++].$data[$pos++].
                                $data[$pos++].$data[$pos++].$data[$pos++].$data[$pos++].
                                $data[$pos++].$data[$pos++].$data[$pos++].$data[$pos++].
                                $data[$pos++].$data[$pos++].$data[$pos++].$data[$pos++]);
        
        $index = \unpack('N', $data[$pos++].$data[$pos++].$data[$pos++].$data[$pos++])[1];
        $numFree = \unpack('N', $data[$pos++].$data[$pos++].$data[$pos++].$data[$pos++])[1];
        
        $module = Atom::decodeIncrement($etf, $data, $pos);
        
        $pos++;
        $oldIndex = $etf->parseAny($data[$pos], $data, $pos);
        
        $pos++;
        $oldUniq = $etf->parseAny($data[$pos], $data, $pos);
        
        $pos++;
        $pid = $etf->parseAny($data[$pos], $data, $pos);
        
        $freeVars = array();
        for($j = $numFree; $j > 0; $j--) {
            $pos++;
            $freeVars[] = $etf->parseAny($data[$pos], $data, $pos);
            
            if($pos === $endSize && $j > 1) {
                throw new Exception('More number of free variables terms than new fun bytes allocated'); // @codeCoverageIgnore
            }
        }
        
        $pos = $size + 1;
        return (new static($size, $arity, $uniq, $index, $module, $numFree, $oldIndex, $oldUniq, $pid, $freeVars));
    }
    
    /**
     * {@inheritdoc}
     */
    function encode(): string {
        $arity = \chr($this->arity);
        
        $uniq = \hex2bin($this->uniq);
        $index = \pack('N', $this->index);
        
        $module = Encoder::encodeAny($this->module);
        $oldIndex = Encoder::encodeAny($this->oldIndex);
        $oldUniq = Encoder::encodeAny($this->oldUniq);
        $pid = Encoder::encodeAny($this->pid);
        
        $freeVars = '';
        foreach($this->freeVars as $var) {
            $freeVars .= Encoder::encodeAny($var);
        }
        
        $numFree = \pack('N', $this->numFree);
        $size = \pack('N', $this->size);
        
        return ETF::NEW_FUN_EXT.$size.$arity.$uniq.$index.$numFree.$module.$oldIndex.$oldUniq.$pid.$freeVars;
    }
}
