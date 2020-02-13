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
 * ETF Reference.
 */
class Reference extends BaseObject {
    /**
     * The node.
     * @var Atom|bool
     */
    public $node;
    
    /**
     * The ID of the reference.
     * @var int
     */
    public $id;
    
    /**
     * Creation is a number containing a node serial number, which makes it possible to separate old (crashed) nodes from a new one.
     * @var int
     */
    public $creation;
    
    /**
     * Constructor.
     * @param Atom|bool  $node
     * @param int        $id
     * @param int        $creation
     */
    function __construct($node, int $id, int $creation) {
        $this->node = $node;
        $this->id = $id;
        $this->creation = $creation;
    }
    
    /**
     * {@inheritdoc}
     */
    function toArray(): array {
        return array(
            'node' => $this->node->toArray(),
            'id' => $this->id,
            'creation' => $this->creation
        );
    }
    
    /**
     * {@inheritdoc}
     * @return self
     */
    static function fromArray($data): BaseObject {
        return (new static(Atom::fromArray($data['node']), $data['id'], $data['creation']));
    }
    
    /**
     * {@inheritdoc}
     */
    static function decode(ETF $etf, string $data, int &$pos) {
        $node = Atom::decodeIncrement($etf, $data, $pos);
        
        $pos++;
        
        $unid = \unpack('N', $data[$pos++].$data[$pos++].$data[$pos++].$data[$pos++])[1];
        
        $bid = \substr(\decbin($unid), 0, 18);
        $id = (int) \bindec($bid);
        
        $bcreation = \substr(\decbin($data[$pos]), 0, 2);
        $creation = (int) \bindec($bcreation);
        
        return (new static($node, $id, $creation));
    }
    
    /**
     * {@inheritdoc}
     */
    function encode(): string {
        $node = ETF::encodeAny($this->node);
        
        $bid = \substr(\decbin($this->id), 0, 18);
        $id = \pack('N', \bindec($bid));
        
        $bcreation = \substr(\decbin($this->creation), 0, 2);
        $creation = \bindec($bcreation);
        
        return ETF::REFERENCE_EXT.$node.$id.\pack('C', $creation);
    }
}
