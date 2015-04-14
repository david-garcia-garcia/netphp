<?php

namespace NetPhp\Core;

use NetPhp\Core\MagicWrapperUtilities;
use NetPhp\Core\MagicWrapper;
use NetPhp\Core\NetProxyUtils;

/**
 * Wraps around a MagicWrapper class instance
 * to allow interaction with the MagicWrappers underlying
 * COM instance methods, properties and others.
 */
class NetProxy {
  
  /**
   * Do not allow external interaction with the
   * MagicWrapper methods.
   *
   * @param string $method 
   * @throws \Exception 
   */
  private function checkForbiddenMethods($method) {
    global $forbidden_methods;
    if (!isset($forbidden_methods)) {
      $forbidden_methods = array();
      $rf = new \ReflectionClass(MagicWrapper::class);
      foreach($rf->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
        $forbidden_methods[] = $method->name;
      }
    }
    if (isset($forbidden_methods[$method])) {
      throw new \Exception('Cannot call MagicWrapper methods on NetProxy');
    }
  }

  //@var MagicWrapper $host
  protected $wrapper;

  private function __construct($host) {
    $this->wrapper = $host;
  }
  
  public static function Get($host) {
    $instance = new NetProxy($host);
    return $instance;
  }

  function __call($method, $args) {
    $this->checkForbiddenMethods($method);
    $result = $this->wrapper->CallMethod($method, $args);
    if (isset($this->wrapper->type_metadata['method_with_native_return_types'][$method])) {
      return $result;
    }
    return NetProxy::Get($result);
  }
  
  function Call($method, ...$args) {
    $result = $this->wrapper->CallMethod($method, $args);
    if (isset($this->wrapper->type_metadata['method_with_native_return_types'][$method])) {
      return $result;
    }
    return NetProxy::Get($result);
  }

  function __set($name, $value){
    NetProxyUtils::UnpackParameter($value);
    $this->wrapper->PropertySet($name, $value);
  }

  function __get($name){
    $result =  $this->wrapper->PropertyGet($name);
    return NetProxy::Get($result);
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
  
  function GetWrapper() {
    return $this->wrapper;
  }
  
  /**
   * Create a new Instance.
   *
   * @param mixed $args 
   * @return NetProxy
   */
  function Instantiate(...$args) {
    NetProxyUtils::UnpackParameters($args);
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
  
  function IsNull() {
    return $this->wrapper->IsNull();
  }
}
