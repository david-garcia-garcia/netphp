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
  
  protected static $types = array();

  public static function RegisterTypes(array $types) {
    static::$types[] = $types;
  }

  /**
   * Map a .Net type to a PHP Proxy
   * 
   * @param string $NetType 
   * @return mixed
   */
  public static function GetProxyPHPType($NetType) {
    foreach (static::$types as $type) {
      if (isset($type[$NetType])) {
        return $type[$NetType];
      }
    }

    // By default the proxy is NetProxy.
    return NetProxy::class;
  }

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
      foreach($rf->getMethods(\ReflectionMethod::IS_PUBLIC) as $m) {
        $forbidden_methods[] = $m->name;
      }
    }
    if (isset($forbidden_methods[$method])) {
      throw new \Exception('Cannot call MagicWrapper methods on NetProxy');
    }
  }

  /**
   * Internal MagicWrapper instance
   * 
   * @var MagicWrapper
   */
  protected $wrapper;

  protected function __construct($host) {
    $this->wrapper = $host;
  }
  
  /**
   * 
   * @param MagicWrapper $host 
   * 
   * @return object
   */
  public static function Get(MagicWrapper $host) {
    $class = static::class;
    $instance = new $class($host);
    return $instance;
  }

  /**
   * Proxy  method calls to the internal wrapper.
   * 
   * @param string $method 
   * @param array $args 
   */
  function __call($method, $args) {
    $this->checkForbiddenMethods($method);
    $this->CallWithArrayArgs($method, $args);
  }
  
  /**
   * Call an internal method with arguments as an array.
   * 
   * @param string $method 
   * @param mixed $args 
   * @return MagicWrapper|object
   */
  function CallWithArrayArgs($method, $args) {
    $result = $this->wrapper->CallMethod($method, $args);
    $metadata = $this->wrapper->GetMetadata();

    /** @var NetProxy */
    $class = static::GetProxyPHPType($metadata['methods'][$method]['ReturnType']);

    return $class::Get($result);
  }

  /**
   * Call an internal method with arguments as non implicit parameters.
   * 
   * @param string $method 
   * @return MagicWrapper|object
   */
  function Call($method) {
    $args = Utilities::GetArgs(func_get_args(), __FUNCTION__, static::class);
    return $this->CallWithArrayArgs($method, $args);
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
  function Instantiate() {
    return $this->InstantiateArgsAsArray(func_get_args());
  }

  /**
   * Summary of InstantiateArgsAsArray
   * 
   * @param array $args 
   * 
   * @return mixed
   */
  function InstantiateArgsAsArray(array $args) {
    NetProxyUtils::UnpackParameters($args);
    $this->wrapper->Instantiate($args);
    return $this;
  }
  
  /**
   * Create a new Enum value instance.
   *
   * @param mixed $value
   * 
   * @return NetProxy
   */
  function Enum($value) {
    $this->wrapper->Enum($value);
    return $this;
  }
  
  /**
   * Summary of IsNull
   */
  function IsNull() {
    return $this->wrapper->IsNull();
  }

  /**
   * Summary of LoadMetadata
   */
  function GetMetadata() {
    return $this->wrapper->GetMetadata();
  }
  
  /**
   * Returns an instance of NetProxyCollection
   * wrapper around the internal MagicWrapper.
   *
   * Make sure that the native .Net type is iterable
   * before doing this!
   *
   * @return NetProxyCollection
   */
  function AsIterator() {
    return NetProxyCollection::Get($this->wrapper);
  }

  public static function CreateInstance($data = NULL) {
    return NetManager::CreateStatic(static::$assembly_full_name, static::$class_name, static::class, $data);
  }

  protected static $assembly_full_name = "";
  protected static $class_name = "";
}
