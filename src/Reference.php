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
     * @param int        $id        This value can not be larger than 262143.
     * @param int        $creation  This value can not be larger than 3.
     */
    function __construct($node, int $id, int $creation) {
        if($id > 262143) {
            throw new \InvalidArgumentException('Parameter $id can not be larger than 262143');
        } elseif($creation > 3) {
            throw new \InvalidArgumentException('Parameter $creation can not be larger than 3');
        }
        
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
    static function fromArray(array $data): BaseObject {
        return (new static(Atom::fromArray($data['node']), $data['id'], $data['creation']));
    }
    
    /**
     * {@inheritdoc}
     */
    static function decode(Decoder $etf, string $data, int &$pos) {
        $node = Atom::decodeIncrement($etf, $data, $pos);
        $pos++;
        
        $id = \unpack('N', $data[$pos++].$data[$pos++].$data[$pos++].$data[$pos++])[1];
        $creation = \unpack('N', "\0\0\0".$data[$pos])[1];
        
        return (new static($node, $id, $creation));
    }
    
    /**
     * {@inheritdoc}
     */
    function encode(Encoder $encoder): string {
        $node = $encoder->encodeAny($this->node, false);
        $id = \pack('N', $this->id);
        $creation = \pack('C', $this->creation);
        
        return ETF::REFERENCE_EXT.$node.$id.$creation;
    }
}
