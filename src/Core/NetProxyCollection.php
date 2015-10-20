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
class NetProxyCollection extends NetProxy implements \Iterator , \Countable, \ArrayAccess {

  protected function __construct($host, $typeMap = NULL) {
    parent::__construct($host, $typeMap);
  }
  
  public static function Get(MagicWrapper $host, $typeMap = NULL) {
     // TODO: MagicWrapper should tell us if this is iterable in .Net
    if (!is_a($host, MagicWrapper::class)) {
      throw new \Exception('NetProxyCollection can only wrap over MagicWrapper that is iterable.');
    }
    
    $instance = new NetProxyCollection($host, $typeMap);
    return $instance;
  }

  #region \Countable methods

  public function count() {
    return $this->wrapper->countable_count();
  }

  #endregion

  #region \Iterator methods

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

  #endregion

  
  #region \ArrayAccess methods

  function offsetExists($offset) {
    $this->wrapper->offsetExists($offset);
  }

  function offsetGet($offset) {
    return NetProxy::Get($this->wrapper->offsetGet($offset));
  }

  function offsetSet($offset, $value) {
    $this->wrapper->offsetSet($offset, $value);
  }

  function offsetUnset($offset) {
    $this->wrapper->offsetUnset($offset);
  }

  #endregion
  
}
