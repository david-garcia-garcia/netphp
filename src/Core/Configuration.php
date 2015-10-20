<?php

namespace NetPhp\Core;

/**
 * Define the behaviour for the .Net compatibility
 * layer.
 */
class Configuration {

  private static $configuration = NULL;


  /**
   * Get the current configuration object.
   *
   * @return Configuration
   */
  public static function GetConfiguration() {
    if (static::$configuration == NULL) {
      static::$configuration = new Configuration();
    }
    return static::$configuration;
  }

  /**
   * Full qualified class name for the MagicWrapper class.
   */
  private $magicWrapperClassName = 'netutilities.MagicWrapper';

  /**
   * Full qualified class name for the MagicWrapperUtilities class.
   */
  private $magicWrapperUilitiesClassName = 'netutilities.MagicWrapperUtilities';

  /**
   * Full qualified name for the TypeDumper class.
   */
  private $typeDumperClassName = 'netutilities.TypeGenerator.TypeDumper';

  public function GetMagicWrapperClassName() {
    return $this->magicWrapperClassName;
  }

  public function GetMagicWrapperUtilitiesClassName() {
    return $this->magicWrapperUilitiesClassName;
  }

  public function GetTypeDumperClassName() {
    return $this->typeDumperClassName;
  }

  public static $types = array();

  /**
   * Register runtime type mappings for .Net to PHP classes.
   *
   * @param array $types
   */
  public static function RegisterTypes(array $types) {
    static::$types = array_merge(static::$types, $types);
  }


  #region LoadMode

  protected $_loadMode = "DOTNET";

  /**
   * Returns the load mode used to instantiate the
   * NetPhp binaries.
   *
   * @return mixed
   */
  public function getLoadMode() {
    return $this->_loadMode;
  }

  /**
   * Set the binary load mode.
   *
   *   DOTNET: Use the..
   *   COM: ...
   *
   * @param string $mode
   *
   */
  public function setLoadMode($mode) {

    if (!in_array($mode, array('DOTNET', 'COM'))) {
      throw new \Exception("Invalid load mode. Use COM or DOTNET.");
    }

    $this->_loadMode = $mode;
  }

  #endregion

  #region AssemblyFullQualifiedName

  private $_assemblyFullQualifiedName = "netutilities";

  /**
   * Returns the assembly quelified name for the NetPhp
   * binaries. This is used when instantiating using DOTNET.
   *
   * @return mixed
   */
  public function getAssemblyFullQualifiedName() {
    return $this->_assemblyFullQualifiedName;
  }

  /**
   * Set the assembly quelified name. Only used for DOTNET load mode.

   * @param string $mode
   */
  public function setAssemblyFullQualifiedName($name) {
    $this->_assemblyFullQualifiedName = $name;
  }

  #endregion

}
