<?php

namespace NetPhp\Core;

use NetPhp\Core\MagicWrapperUtilities;
use NetPhp\Core\MagicWrapper;
use NetPhp\Core\NetProxyUtils;
use NetPhp\Core\TypeMapBase;

/**
 * Wraps around a MagicWrapper class instance
 * to allow interaction with the MagicWrappers underlying
 * COM instance methods, properties and others.
 */
class NetProxy {

  protected static $assembly = "";
  protected static $class = "";

  /**
   * @var TypeMapBase
   */
  protected $runtimeTypeMap;

  protected static $typeMap = NULL;


  private static function __endsWith($needle, $haystack) {
    return preg_match('/' . preg_quote($needle, '/') . '$/', $haystack);
  }

  /**
   * Map a .Net type to a PHP Proxy
   *
   * @param string $NetType
   *
   * @return mixed
   */
  protected function GetProxyPHPType($NetType) {

    // If this is an Array, there is no representation in PHP
    // so use the Base Type.
    if (static::__endsWith('[]', $NetType)) {
      $NetType = "System.Array";
    }

    if (!empty($this->runtimeTypeMap)) {
      $tm = $this->runtimeTypeMap;
      return $tm::ResolveNetType($NetType);
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

  protected function __construct($host, $typeMap = null) {
    $this->runtimeTypeMap = $typeMap == null ? static::$typeMap : $typeMap;
    $this->wrapper = $host;
  }

  /**
   *
   * @param MagicWrapper $host
   *
   * @return mixed
   */
  public static function Get(MagicWrapper $host, $typeMap = null) {
    $class = static::class;
    $instance = new $class($host, $typeMap);
    return $instance;
  }

  /**
   * Proxy  method calls to the internal wrapper.
   *
   * @param string $method
   * @param array $args
   */
  public function __call($method, $args) {
    $this->checkForbiddenMethods($method);
    return $this->CallWithArrayArgs($method, $args);
  }

  /**
   * Call an internal method with arguments as an array.
   *
   * @param string $method
   *
   * @param mixed $args
   *
   * @return MagicWrapper|object
   */
  protected function CallWithArrayArgs($method, $args) {
    $result = $this->wrapper->CallMethod($method, $args);
    $metadata = $this->wrapper->GetMetadata();

    /** @var NetProxy */
    $class = NetProxy::class;

    if (isset($metadata['methods'][$method]['ReturnType'])) {
      $class = $this->GetProxyPHPType($metadata['methods'][$method]['ReturnType']);
    }
    else if (isset($metadata['properties'][$method]['PropertyType'])) {
      $class = $this->GetProxyPHPType($metadata['properties'][$method]['PropertyType']);
    }

    return $class::Get($result);
  }

  /**
   * Call an internal method with arguments as non implicit parameters.
   *
   * @param string $method
   * @return MagicWrapper|object
   */
  protected function Call($method) {
    $args = Utilities::GetArgs(func_get_args(), __FUNCTION__, static::class);
    return $this->CallWithArrayArgs($method, $args);
  }


  /**
   * Property setter
   *
   * @param string $name
   *
   * @param mixed $value
   */
  public function __set($name, $value){
    $this->UnpackParameter($value);
    $this->wrapper->PropertySet($name, $value);
  }

  /**
   * Property getter.
   *
   * @param string $name
   *
   * @return mixed
   */
  public function __get($name){
    $result =  $this->wrapper->PropertyGet($name);
    $metadata = $this->wrapper->GetMetadata();

    /** @var NetProxy */
    $class = $this->GetProxyPHPType($metadata['properties'][$name]['PropertyType']);

    return $class::Get($result);
  }

  /**
   * Returns the .Net type of the wrapped object.
   */
  public function GetType() {
    return $this->wrapper->WrappedType();
  }

  /**
   * Return the Internal wrapped value.
   */
  public function Val() {
    return $this->wrapper->UnWrap();
  }

  /**
   * Gets the native COM MagicWrapper object.
   */
  public function UnPack() {
    return $this->wrapper->UnPack();
  }

  /**
   * Get the internal instance, this is always a MagicWrapper
   *
   * @return MagicWrapper
   */
  function GetWrapper() {
    return $this->wrapper;
  }

  /**
   * Create a new Instance with variadic like arguments.
   *
   * @param mixed $args
   *
   * @return NetProxy
   */
  function Instantiate(...$args) {
    return $this->InstantiateArgsAsArray($args);
  }

  /**
   * Summary of InstantiateArgsAsArray
   *
   * @param array $args
   *
   * @return mixed
   */
  function InstantiateArgsAsArray(array $args) {

    $this->UnpackParameters($args);
    $this->wrapper->Instantiate($args);

    /** @var mixed $result */
    $result = $this;
    return $result;
  }


  /**
   * Create a new Enum value instance.
   *
   * @param mixed $value
   *
   * @return mixed
   */
  function Enum($value) {
    $this->wrapper->Enum($value);
    return $this;
  }

  /**
   * Retrieve a static NOT instantiated instance, used for
   * calling static members.
   * 
   * @return mixed
   */
  public static function __sti__() {
    /** @var TypeMapBase */
    $tm = static::$typeMap;
    return $tm::TypeFromAssembly(static::$class, static::$assembly, static::class);
  }

  /**
   * Summary of EnumStatic
   *
   * @param mixed $value
   *
   * @throws \Exception
   *
   * @return mixed
   */
  public static function EnumStatic($value) {
    if (empty(static::$typeMap)) {
      throw new \Exception("Canot use EnumStatic without a TypeMap.");
    }
    return static::__sti__()->Enum($value);
  }

  /**
   * Only use this to create an instance from a native type.
   *
   * @param mixed $data
   *
   * @return mixed|NetProxy|NetProxyCollection
   */
  public static function FromNative($data = NULL) {
    if (empty(static::$typeMap)) {
      throw new \Exception("Canot use FromNative without a TypeMap.");
    }
    /** @var TypeMapBase */
    $tm = static::$typeMap;
    return $tm::TypeFromAssembly(static::$class, static::$assembly, static::class, $data);
  }

  /**
   * Summary of IsNull
   */
  public function IsNull() {
    return $this->wrapper->IsNull();
  }

  /**
   * Summary of LoadMetadata
   */
  public function GetMetadata() {
    return $this->wrapper->GetMetadata();
  }

  /**
   * Get a Json representation of this object.
   *
   * @return string
   */
  public function GetJson() {
    return $this->wrapper->GetJson();
  }

  /**
   * Get a PHP represenation of this object from JSON
   *
   * @return mixed
   */
  public function GetPhpFromJson($assoc = FALSE) {
    return $this->wrapper->GetPhpFromJson($assoc);
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
  public function AsIterator() {
    return NetProxyCollection::Get($this->wrapper);
  }

  /**
   * Iterates a an array of objects and unpacks them as much
   * as possible.
   * 
   * From either NetProxy or MagicWrapper wrappers
   * to the COM instance, so that they can be sent
   * to .Net.
   *
   * @param array $params 
   */
  public static function UnpackParameters(array &$params) {
    foreach ($params as &$param) {
      if (method_exists($param, 'UnPack')) {
        $param = $param->UnPack();
      }
    }
  }
  
  /**
   * Unpacks a NetProxy or MagicWrapper to the COM instance.
   *
   * @param mixed $param 
   */
  public static function UnpackParameter(&$param) {
    if (method_exists($param, 'UnPack')) {
      $param = $param->UnPack();
    }
  }
}
