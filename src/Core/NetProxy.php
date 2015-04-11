<?php

namespace NetPhp\Core;

use NetPhp\Core\MagicWrapperUtilities;
use NetPhp\Core\MagicWrapper;

class NetProxy {

  private $forbidden_methods = array(
    'CallMethod' => TRUE,
    'PropertySet' => TRUE,
    'PropertyGet' => TRUE,
    'WrappedType' => TRUE,
    'UnWrap' => TRUE,
    'UnPack' => TRUE,
    'Instantiate' => TRUE,
    'Wrap' => TRUE
  );
  
  private function checkForbiddenMethods($method) {
    if (isset($forbidden_methods[$method])) {
      throw new \Exception('Cannot call MagicWrapper methods on NetProxy');
    }
  }

  // @var MagicWrapper $host
  private $wrapper;

  function __construct($host) {
    $this->wrapper = $host;
  }

  function __call($method, $args) {
    NetManager::UnpackParameters($args);
    $this->checkForbiddenMethods($method);
    $result = $this->wrapper->CallMethod($method, $args);
    return new NetProxy($result);
  }

  function __set($name, $value){
    NetManager::UnpackParameter($value);
    $this->wrapper->PropertySet($name, $value);
  }

  function __get($name){
    $result =  $this->wrapper->PropertyGet($name);
    return new NetProxy($result);
  }
  
  /**
   * Returns the .Net type of the wrapped object.
   */
  function GetType() {
    return $this->wrapper->WrappedType();
  }
  
  /**
   * Return the Internal wrapped value.
   */
  function Val() {
    return $this->wrapper->UnWrap();
  }
  
  /**
   * Gets the native COM MagicWrapper object.
   */
  function UnPack() {
    return $this->wrapper->UnPack();
  }
  
  /**
   * Create a new Instance.
   *
   * @param mixed $args 
   * @return NetProxy
   */
  function Instantiate(...$args) {
    NetManager::UnpackParameters($args);
    $this->wrapper->Instantiate($args);
    return $this;
  }
  
  /**
   * Create a new Enum value instance.
   *
   * @param mixed $value 
   * @return NetProxy
   */
  function Enum($value) {
    $this->wrapper->Enum($value);
    return $this;
  }
}
