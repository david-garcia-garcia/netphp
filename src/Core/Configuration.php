<?php

namespace NetPhp\Core;

/**
 * Define the configuration (behaviour) of this
 * the NetPhp component.
 */
class Configuration {

  private static $configuration = NULL;
  
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

  public function GetMagicWrapperClassName() {
    return $this->magicWrapperClassName;
  }

  public function GetMagicWrapperUtilitiesClassName() {
    return $this->magicWrapperUilitiesClassName;
  }

  public static $types = array();

  public static function RegisterTypes(array $types) {
    static::$types[] = $types;
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
      throw new \Exception("Invalid load mode.");
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
