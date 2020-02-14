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
 * ETF Fun(ction).
 */
class Fun extends BaseObject {
    /**
     * The number of free variables.
     * @var int
     */
    public $numFree;
    
    /**
     * Represents the process in which the fun was created.
     * @var PID
     */
    public $pid;
    
    /**
     * The module that the fun is implemented in.
     * @var Atom|bool
     */
    public $module;
    
    /**
     * It is typically a small index into the module's fun table.
     * @var int
     */
    public $index;
    
    /**
     * Uniq is the hash value of the parse for the fun.
     * @var int
     */
    public $uniq;
    
    /**
     * The free variables.
     * @var array
     */
    public $freeVars;
    
    /**
     * Constructor.
     * @param int        $numFree
     * @param PID        $pid
     * @param Atom|bool  $module
     * @param int        $index
     * @param int        $uniq
     * @param array      $freeVars
     */
    function __construct(int $numFree, $pid, $module, int $index, int $uniq, array $freeVars) {
        $this->numFree = $numFree;
        $this->pid = $pid;
        $this->module = $module;
        $this->index = $index;
        $this->uniq = $uniq;
        $this->freeVars = $freeVars;
    }
    
    /**
     * {@inheritdoc}
     */
    function toArray(): array {
        return array(
            'numFree' => $this->numFree,
            'pid' => $this->pid->toArray(),
            'module' => $this->module->toArray(),
            'index' => $this->index,
            'uniq' => $this->uniq,
            'freeVars' => $this->freeVars
        );
    }
    
    /**
     * {@inheritdoc}
     * @return self
     */
    static function fromArray($data): BaseObject {
        return (new static($data['numFree'], PID::fromArray($data['pid']), Atom::fromArray($data['module']), $data['index'], $data['uniq'], $data['freeVars']));
    }
    
    /**
     * {@inheritdoc}
     */
    static function decode(Decoder $etf, string $data, int &$pos) {
        $numFree = \unpack('N', $data[$pos++].$data[$pos++].$data[$pos++].$data[$pos++])[1];
        
        $pid = $etf->parseAny($data[$pos], $data, $pos);
        
        $pos++;
        $module = Atom::decodeIncrement($etf, $data, $pos);
        
        $pos++;
        $index = $etf->parseAny($data[$pos], $data, $pos);
        
        $pos++;
        $uniq = $etf->parseAny($data[$pos], $data, $pos);
        
        $freeVars = array();
        for($j = $numFree; $j > 0; $j--) {
            $pos++;
            $freeVars[] = $etf->parseAny($data[$pos], $data, $pos);
        }
        
        return (new static($numFree, $pid, $module, $index, $uniq, $freeVars));
    }
    
    /**
     * {@inheritdoc}
     */
    function encode(): string {
        $numFree = \pack('N', $this->numFree);
        
        $pid = Encoder::encodeAny($this->pid);
        $module = Encoder::encodeAny($this->module);
        $index = Encoder::encodeAny($this->index);
        $uniq = Encoder::encodeAny($this->uniq);
        
        $freeVars = '';
        
        if(\count($this->freeVars) > 0) {
            foreach($this->freeVars as $var) {
                $freeVars .= Encoder::encodeAny($var);
            }
        }
        
        return ETF::FUN_EXT.$numFree.$pid.$module.$index.$uniq.$freeVars;
    }
}
