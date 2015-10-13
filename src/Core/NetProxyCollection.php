<?php

namespace NetPhp\Core;

use NetPhp\Core\MagicWrapperUtilities;
use NetPhp\Core\MagicWrapper;
use NetPhp\Core\NetProxyUtils;

/**
 *  Use PHP to iterate over .Net Collections.
 *
 *  As for now the binary .dll only supports objects that have the
 *  IList or ICollection interface. Support for Dictionary<> is on the way.
 */
class NetProxyCollection extends NetProxy implements \Iterator , \Countable {

  protected function __construct($host) {
    $this->wrapper = $host;
  }
  
  public static function Get(MagicWrapper $host) {
     // TODO: MagicWrapper should tell us if this is iterable in .Net
    if (!is_a($host, MagicWrapper::class)) {
      throw new \Exception('NetProxyCollection can only wrap over MagicWrapper that is iterable.');
    }
    
    $instance = new NetProxyCollection($host);
    return $instance;
  }

  public function count() {
    return $this->wrapper->countable_count();
  }

  function rewind() {
    $this->wrapper->iterator_rewind();
  }

  function current() {
    return NetProxy::Get($this->wrapper->iterator_current());
  }

  function key() {
    // Keys must always be of a PHP compatible type
    // so unpack.
    return $this->wrapper->iterator_key()->UnWrap();
  }

  function next() {
    $this->wrapper->iterator_next();
  }

  function valid() {
    return $this->wrapper->iterator_valid();
  }
  
}
