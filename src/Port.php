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
 * ETF Port.
 */
class Port extends BaseObject {
    /**
     * The node.
     * @var Atom|bool
     */
    public $node;
    
    /**
     * The ID of the port.
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
    static function decode(Decoder $etf, string $data, int &$pos) {
        $node = Atom::decodeIncrement($etf, $data, $pos);
        
        $pos++;
        
        $id = \unpack('N', $data[$pos++].$data[$pos++].$data[$pos++].$data[$pos++])[1];
        $creation = \ord($data[$pos]);
        
        return (new static($node, $id, $creation));
    }
    
    /**
     * {@inheritdoc}
     */
    function encode(): string {
        $node = Encoder::encodeAny($this->node);
        $id = \pack('N', $this->id);
        
        return ETF::PORT_EXT.$node.$id.\chr($this->creation);
    }
}
