<?php

namespace NetPhp\Core;

/**
 * Wrapper for full PHP-.Net interoperability.
 * 
 * COM CLASS ID FOR THE 2.X VERSION OF THE BINARY: {B5A161C8-C2FD-45E8-AD93-8EEA98607F5F}
 */
class MagicWrapper extends ComProxy {

  /**
   * Type metadata array details.
   *
   * @var mixed
   */
  protected $type_metadata;

  public function __construct() { }

  /**
   * Gets a magic wrapper instance. You can wrap over a
   * current COM instance, or use a ResolvedClass
   * to get an empty instance ready to use.
   */
  public static function Get($source) {
    $result = new MagicWrapper();
    if($source != null) {
      $result->_Wrap($source);
      $result->GetMetadata();
    }
    return $result;
  }

  /**
   * Wrapp over a native MaticWrapper Instance
   *
   * @param mixed $source
   *
   * @return MagicWrapper
   */
  public function Wrap($source) {
    $this->_Wrap($source);
    return $this;
  }

  /**
   * Wrap over a .Net object. For null objects
   * it will not be able to infer the type.
   *
   * @param mixed $data
   */
  public function WrapOver($data) {
    $this->host->WrapOver($data);
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
   * Return the internal instance.
   *
   * @return mixed
   */
  public function UnPack() {
    return $this->host;
  }

  /**
   * Get a Json Copy of this .Net objecct
   *
   * @return string
   */
  public function GetJson() {
    return $this->host->GetJson();
  }

  /**
   * Get a PHP verion of this objects that results
   * from a JSON serialize/deserialize.
   *
   * @return mixed
   */
  public function GetPhpFromJson($assoc = FALSE) {
    return json_decode($this->GetJson(), $assoc);
  }

  /**
   * Call a method.
   *
   * @param string $method
   *
   * @param array $args
   *
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
   * Is the wrapped .Net instance null?
   */
  public function IsNull() {
    return $this->host->is_null();
  }

  #region Iterator

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

  #endregion

  #region Countable

  public function countable_count() {
    $result = $this->host->countable_count();
    // Do not wrap results as we are expecting native int.
    return $result;
  }

  #endregion

  #region ArrayAccess

  public function offsetGet($offset) {
    $result = $this->host->offsetGet($offset);
    return static::Get($result);
  }

  public function offsetSet($offset, $value) {
    $this->host->offsetSet($offset, $value);
  }

  public function offsetUnset($offset) {
    $this->host->offsetUnset($offset);
  }

  public function offsetExists($offset) {
    return $this->host->offsetExists($offset);
  }

  #endregion
}