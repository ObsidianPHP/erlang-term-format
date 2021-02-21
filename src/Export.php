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
 * ETF Export.
 */
class Export extends BaseObject {
    /**
     * The atom.
     * @var Atom|bool
     */
    public $module;
    
    /**
     * The function.
     * @var Atom|bool
     */
    public $function;
    
    /**
     * The arity of the function.
     * @var int
     */
    public $arity;
    
    /**
     * Constructor.
     * @param Atom|bool  $module
     * @param Atom|bool  $function
     * @param int        $arity
     */
    function __construct($module, $function, int $arity) {
        $this->module = $module;
        $this->function = $function;
        $this->arity = $arity;
    }
    
    /**
     * {@inheritdoc}
     */
    function toArray(): array {
        return array(
            'module' => $this->module->toArray(),
            'function' => $this->function->toArray(),
            'arity' => $this->arity
        );
    }
    
    /**
     * {@inheritdoc}
     * @return self
     */
    static function fromArray(array $data): BaseObject {
        return (new static(Atom::fromArray($data['module']), Atom::fromArray($data['function']), $data['arity']));
    }
    
    /**
     * {@inheritdoc}
     */
    static function decode(Decoder $etf, string $data, int &$pos) {
        $module = $etf->parseAny($data[$pos], $data, $pos);
        
        $pos++;
        $function = $etf->parseAny($data[$pos], $data, $pos);
        
        $pos++;
        $arity = $etf->parseAny($data[$pos], $data, $pos);
        
        return (new static($module, $function, $arity));
    }
    
    /**
     * {@inheritdoc}
     */
    function encode(Encoder $encoder): string {
        $module = $encoder->encodeAny($this->module, false);
        $function = $encoder->encodeAny($this->function, false);
        $arity = $encoder->encodeAny($this->arity, false);
        
        return ETF::EXPORT_EXT.$module.$function.$arity;
    }
}
