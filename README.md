# ETF [![CircleCI](https://circleci.com/gh/ObsidianPHP/erlang-term-format.svg?style=svg)](https://circleci.com/gh/ObsidianPHP/erlang-term-format)

PHP decoder and encoder for the Erlang Term Format.

By default, the encoder encodes numeric strings as bigint. This behaviour can be changed by passing
`Encoder::ENCODE_DIRECT_STRINGS_AS_BINARY` to the constructor,  so all strings directly given to `Encoder->encode` will be encoded as string.
Or to always encode numeric strings as binary, pass `Encoder::ENCODE_STRINGS_AS_BINARY` to the constructor.

# Installation

Install this library through composer using
```
composer require obsidian/etf
```

# Example
Minimal decoding example:
```php
use Obsidian\ETF\Decoder;

// the binary erlang term format string (#PID<0.81.0>)
$binary = base64_decode("g2d3DW5vbm9kZUBub2hvc3QAAABRAAAAAAA=");

$etf = new Decoder();
$pid = $etf->decode($binary);

var_dump($pid);

/*
class Obsidian\ETF\PID#6 (4) {
    public $node =>
    class Obsidian\ETF\Atom#5 (1) {
        public $atom =>
        string(13) "nonode@nohost"
    }
    public $id =>
    int(81)
    public $serial =>
    int(0)
    public $creation =>
    int(0)
}
*/
```

Minimal encoding example:
```php
use Obsidian\ETF\Atom;
use Obsidian\ETF\Encoder;

$value = new Atom('hello');

$etf = new Encoder();
$binary = $etf->encode($value);

var_dump($binary);

/*
string(8) "w║hello"
*/
```

# Erlang External Term Format

http://erlang.org/doc/apps/erts/erl_ext_dist.html
