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
 * ETF Newer Reference.
 */
class NewerReference extends BaseObject {
    /**
     * The node.
     * @var Atom|bool
     */
    public $node;
    
    /**
     * Creation is a number containing a node serial number, which makes it possible to separate old (crashed) nodes from a new one.
     * @var int
     */
    public $creation;
    
    /**
     * The ID of the reference.
     * @var int[]
     */
    public $id;
    
    /**
     * Constructor.
     * @param Atom|bool  $node
     * @param int        $creation
     * @param int[]      $id
     */
    function __construct($node, int $creation, array $id) {
        $this->node = $node;
        $this->creation = $creation;
        $this->id = $id;
    }
    
    /**
     * {@inheritdoc}
     */
    function toArray(): array {
        return array(
            'node' => $this->node->toArray(),
            'creation' => $this->creation,
            'id' => $this->id
        );
    }
    
    /**
     * {@inheritdoc}
     * @return self
     */
    static function fromArray($data): BaseObject {
        return (new static(Atom::fromArray($data['node']), $data['creation'], $data['id']));
    }
    
    /**
     * {@inheritdoc}
     */
    static function decode(Decoder $etf, string $data, int &$pos) {
        $length = \unpack('n', $data[$pos++].$data[$pos++])[1];
        
        $node = Atom::decodeIncrement($etf, $data, $pos);
        $creation = (int) \unpack('N', $data[++$pos].$data[++$pos].$data[++$pos].$data[++$pos])[1];
        
        $id = array();
        for(; $length > 0; $length--) {
            $id[] = \unpack('N', $data[++$pos].$data[++$pos].$data[++$pos].$data[++$pos])[1];
        }
        
        return (new static($node, $creation, $id));
    }
    
    /**
     * {@inheritdoc}
     */
    function encode(): string {
        $node = Encoder::encodeAny($this->node);
        $creation = \pack('N', $this->creation);
        
        $id = '';
        foreach($this->id as $i) {
            $id .= \pack('N', $i);
        }
        
        $length = \pack('n', \count($this->id));
        return ETF::NEWER_REFERENCE_EXT.$length.$node.$creation.$id;
    }
}
