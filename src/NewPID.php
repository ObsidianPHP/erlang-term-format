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
 * ETF New PID.
 */
class NewPID extends BaseObject {
    /**
     * The node.
     * @var Atom|bool
     */
    public $node;
    
    /**
     * The ID of the PID.
     * @var int
     */
    public $id;
    
    /**
     * The serial number.
     * @var int
     */
    public $serial;
    
    /**
     * Creation is a number containing a node serial number, which makes it possible to separate old (crashed) nodes from a new one.
     * @var int
     */
    public $creation;
    
    /**
     * Constructor.
     * @param Atom|bool  $node
     * @param int        $id
     * @param int        $serial
     * @param int        $creation
     */
    function __construct($node, int $id, int $serial, int $creation) {
        $this->node = $node;
        $this->id = $id;
        $this->serial = $serial;
        $this->creation = $creation;
    }
    
    /**
     * {@inheritdoc}
     */
    function toArray(): array {
        return array(
            'node' => $this->node->toArray(),
            'id' => $this->id,
            'serial' => $this->serial,
            'creation' => $this->creation
        );
    }
    
    /**
     * {@inheritdoc}
     * @return self
     */
    static function fromArray(array $data): BaseObject {
        return (new static(Atom::fromArray($data['node']), $data['id'], $data['serial'], $data['creation']));
    }
    
    /**
     * {@inheritdoc}
     */
    static function decode(Decoder $etf, string $data, int &$pos) {
        $node = Atom::decodeIncrement($etf, $data, $pos);
        
        $pos++;
        
        $id = \unpack('N', $data[$pos++].$data[$pos++].$data[$pos++].$data[$pos++])[1];
        $serial = \unpack('N', $data[$pos++].$data[$pos++].$data[$pos++].$data[$pos++])[1];
        $creation = \unpack('N', $data[$pos++].$data[$pos++].$data[$pos++].$data[$pos])[1];
        
        return (new static($node, $id, $serial, $creation));
    }
    
    /**
     * {@inheritdoc}
     */
    function encode(Encoder $encoder): string {
        $node = $encoder->encodeAny($this->node, false);
        $id = \pack('N', $this->id);
        $serial = \pack('N', $this->serial);
        $creation = \pack('N', $this->creation);
        
        return ETF::NEW_PID_EXT.$node.$id.$serial.$creation;
    }
}
