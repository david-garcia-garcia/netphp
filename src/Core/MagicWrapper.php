<?php

namespace NetPhp\Core;

/**
 * Wrapper for full PHP-.Net interoperability.
 */
class MagicWrapper extends ComProxy {

  /**
   * The .Net type as requested by the user.
   * 
   * @var ResolvedClass
   */
  protected $type_data;
  
  /**
   * Type metadata array details.
   * 
   * @var mixed
   */
  protected $type_metadata;
  
  protected function __construct() { }
  
  /**
   * Get a en empty magic wrapper to instantiate a class.
   */
  public static function GetFromType(ResolvedClass $source) {
    $result = new MagicWrapper();
    $result->type_data = $source;
    return $result;
  }
  
  /**
   * Gets a magic wrapper instance. You can wrap over a 
   * current COM instance, or use a ResolvedClass
   * to get an empty instance ready to use.
   */
  public static function Get($source) {
    $result = new MagicWrapper();
    if($source != null) {
      $result->_Wrap($source);
    }
    return $result;
  }
  
  public static function TypeFromFile() {
    
  }
  
  public static function TypeFromName() {
    
  }
  
  /**
   * Summary of Wrap
   * @param mixed $source 
   * @return MagicWrapper
   */
  public function Wrap($source) {
    $this->_Wrap($source);
    return $this;
  }
  
  public function UnPack() {
    return $this->host;
  }

  /**
   * Summary of CallMethod
   * @param mixed $method 
   * @param mixed $args 
   * @return MagicWrapper
   */
  public function CallMethod($method, $args) {
    // Unpack the parameters, both NetProxy and MagicWrapper
    // have an UnPack method.
    foreach ($args as &$param) {
      if (method_exists($param, 'UnPack')) {
        $param = $param->UnPack();
      }
    }
    // Wrap a new Magic Wrapper around the result.
    $result = $this->host->CallMethod($method, $args);
    // If this is a native, return as-is.
    if (isset($this->type_metadata['method_with_native_return_types'][$method])) {
      return $result;
    }
    // Otherwise wrap it.
    $result = static::Get($result);
    return $result;
  }
  
  /**
   * Summary of PropertySet
   * @param mixed $property 
   * @param mixed $value 
   */
  public function PropertySet($property, $value) {
    $this->host->PropertySet($property, $value);
  }
  
  /**
   * Summary of PropertyGet
   * @param mixed $property 
   * @return MagicWrapper
   */
  public function PropertyGet($property) {
    $result = $this->host->PropertyGet($property);
    return static::Get($result);
  }
  
  /**
   * Create an internal instance of the provided .Net type.
   *
   * @param mixed $assemblyPath
   *  Full Path to the .dll file to load.
   *
   * @param mixed $name 
   *  Full qualified name of the .Net type inside the assembly.
   *
   * @param array $args 
   *  Arguments to pass for the type constructor.
   */
  public function Instantiate($args = array()) {
    $this->LoadMagicWrapper();
    $this->host->Instantiate($args);
  }
  
  /**
   * Get this type metadata.
   * 
   * Lazy loads of course...
   */
  public function GetMetadata() {
    if (empty($this->type_metadata)) {
      $metadata = $this->host->GetMetadata();
      $this->type_metadata = json_decode($metadata, true);
    }
    return $this->type_metadata;
  }
  
  private function LoadMagicWrapper() {
    // If there is a host we are already wrapped, nothing to do.
    if ($this->host != null) {
      return;
    }
    
    // Make sure we have inited the binary MagicWrapper.
    $configuration = Configuration::GetConfiguration();
    $this->_InstantiateDOTNET($configuration->getAssemblyFullQualifiedName(), $configuration->GetMagicWrapperClassName());
    
    $assembly = $this->type_data->assemblyFullQualifiedName;
    if (file_exists($assembly)) {
      $this->host->TypeFromFile($assembly, $this->type_data->classFullQualifiedName);
    }
    else {
      $this->host->TypeFromName($assembly, $this->type_data->classFullQualifiedName);
    }
  }
  
  /**
   * Wraps over an Enum value
   *
   * @param mixed $assemblyPath
   *  Full Path to the .dll file to load.
   *
   * @param mixed $name 
   *  Full qualified name of the .Net type inside the assembly.
   *
   * @param mixed $value 
   *  The Enum value to wrap over.
   */
  public function Enum($value) {
    $this->host->Enum($value);
  }
  
  /**
   * Summary of WrappedType
   */
  public function WrappedType() {
    return (string) $this->host->WrappedType();
  }
  
  
  /**
   * Get the internal hosted object!
   */
  public function UnWrap() {
    if (in_array(gettype($this->host), array('variant', 'object'))) {
      return $this->host->UnWrap();
    }
    else {
      return $this->host;
    }
  }
  
  /**
   * Is the wrapped .Net instance null?
   */
  public function IsNull() {
    $this->host->is_null();
  }
  
  //*****************************************************
  // Start iterator section.
  //*****************************************************
  
  public function iterator_current() {
    $result = $this->host->iterator_current();
    return static::Get($result);
    
  }
  
  public function iterator_valid() {
    return $this->host->iterator_valid();
  }
  
  public function iterator_rewind() {
    $this->host->iterator_rewind();
  }
  
  public function iterator_next() {
    $this->host->iterator_next();
  }
  
  public function iterator_key() {
    $result = $this->host->iterator_key();
    // Not sure if return type will be or not native, so
    // try to wrap.
    return static::Get($result);
  }
  
  public function countable_count() {
    $result = $this->host->countable_count();
    // Do not wrap results as we are expecting native int.
    return $result;
  }
}