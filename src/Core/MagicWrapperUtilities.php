<?php

namespace NetPhp\Core;

use NetPhp\Core\MagicWrapper;

class MagicWrapperUtilities extends ComProxy {


  /**
   * Get an instance of MagicWrapperUtilities
   * 
   * @return MagicWrapperUtilities
   */
  public static function GetInstance() {
    $instance = new MagicWrapperUtilities();

    $configuration = Configuration::GetConfiguration();
    if ($configuration->getLoadMode() == "DOTNET") {
      $instance->_InstantiateDOTNET($configuration->getAssemblyFullQualifiedName(), $configuration->GetMagicWrapperUtilitiesClassName());
    }
    else if ($configuration->getLoadMode() == "COM") {
      $instance->_InstantiateCOM($configuration->GetMagicWrapperUtilitiesClassName());
    }

    return $instance;
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
}
