<?php

namespace NetPhp\Core;

use NetPhp\Core\MagicWrapper;

class MagicWrapperUtilities extends ComProxy {

  /**
   * Private constructor!
   */
  protected function __construct() {}

  /**
   * Persistent instance of the Utilities class.
   *
   * @var MagicWrapperUtilities
   */
  private static $instance = NULL;

  /**
   * Get an instance of MagicWrapperUtilities, this is stored statically throughout
   * the request.
   *
   * @return MagicWrapperUtilities
   */
  public static function GetInstance() {

    if (static::$instance == NULL) {

      static::$instance = new MagicWrapperUtilities();

      $configuration = Configuration::GetConfiguration();
      if ($configuration->getLoadMode() == "DOTNET") {
        static::$instance->_InstantiateDOTNET($configuration->getAssemblyFullQualifiedName(), $configuration->GetMagicWrapperUtilitiesClassName());
      }
      else if ($configuration->getLoadMode() == "COM") {
        static::$instance->_InstantiateCOM($configuration->GetMagicWrapperUtilitiesClassName());
      }

    }

    return static::$instance;
  }

  /**
   * Wrap over an existing COM object
   * @param mixed $source
   */
  public static function Wrap($source) {
    $instance = new MagicWrapperUtilities();
    $instance->_Wrap($source);
    return $instance;
  }

  /**
   * Summary of GetTypeAsString
   * @param mixed $object
   * @return string
   */
  public function GetTypeAsString($object) {
    return (string) $this->host->GetTypeAsString($object);
  }

  /**
   * Just for experimenting .Net to PHP native type conversions.
   */
  public function GetTypeSample($index) {
    return $this->host->GetTypeSample($index);
  }

  /**
   * Throw an Exception from .Net
   */
  public function TestException() {
    $this->host->TestException();
  }

  /**
   * Summary of GetStringVersion
   *
   * @return string
   */
  public function GetStringVersion() {
    return $this->host->GetStringVersion();
  }

  /**
   * Summary of GetSampleTypes
   */
  public function GetSampleTypes() {
    $instance = MagicWrapper::Get($this->host->GetSampleTypes());
    return NetProxyCollection::Get($instance);
  }

  /**
   * Summary of GetSampleTypes
   */
  public function GetSamples() {
    $instance = MagicWrapper::Get($this->host->GetSamples());
    return NetProxyCollection::Get($instance);
  }

  /**
   * Get the AssemblyName instance.
   */
  public function GetVersion() {
    $instance = MagicWrapper::Get($this->host->GetVersion());
    return NetProxyCollection::Get($instance);
  }

  /**
   * The the list of installed .Net framwork versions.
   */
  public function GetAvailableFrameworkVersions() {
    $instance = MagicWrapper::Get($this->host->getAvailableFrameworkVersions());
    return NetProxyCollection::Get($instance);
  }

  /**
   * Return a VARIANT MagicWrapper instance over the specified type.
   *
   * @param string $assemblyName
   *
   * @param string $className
   *
   * @return mixed
   */
  public function TypeFromName($assemblyName, $className)
  {
    return $this->host->TypeFromName($assemblyName, $className);
  }

  /**
   * Return a VARIANT MagicWrapper instance over the specified type.
   *
   * @param string $assemblyPath
   *
   * @param string $className
   *
   * @return mixed
   */
  public function TypeFromFile($assemblyPath, $className)
  {
    return $this->host->TypeFromFile($assemblyPath, $className);
  }

  /**
   * Inspect all the assemblies in a directory to get their FQDN.
   *
   * @param string $path
   */
  public function InspectDirectoryAssemblies($path) {
    return MagicWrapper::Get($this->host->InspectDirectoryAssemblies($path))->GetPhpFromJson();
  }
}
