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
 * Erlang Term Format specification.
 */
interface ETF {
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
}
