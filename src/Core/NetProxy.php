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

  // @var MagicWrapper $host
  protected $wrapper;

  private function __construct($host) {
    $this->wrapper = $host;
  }
  
  public static function Get($host) {
    if (!is_a($host, MagicWrapper::class)) {
      throw new \Exception('Net Proxy can only wrap over MagicWrapper');
    }
    
    $instance = new NetProxy($host);
    return $instance;
  }

  function __call($method, $args) {
    NetProxyUtils::UnpackParameters($args);
    $this->checkForbiddenMethods($method);
    $result = $this->wrapper->CallMethod($method, $args);
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
}
