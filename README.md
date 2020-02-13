# ETF [![CircleCI](https://circleci.com/gh/ObsidianPHP/ETF.svg?style=svg)](https://circleci.com/gh/ObsidianPHP/ETF)

PHP decoder and encoder for the Erlang Term Format.

# Installation

Install this library through composer using
```
composer require obsidian/etf
```

# Example
Minimal example:
```php
use Obsidian\ETF\ETF;

// the binary erlang term format string (#PID<0.81.0>)
$binary = \base64_decode("g2d3DW5vbm9kZUBub2hvc3QAAABRAAAAAAA=");

$etf = new ETF();
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

# Erlang External Term Format

http://erlang.org/doc/apps/erts/erl_ext_dist.html
