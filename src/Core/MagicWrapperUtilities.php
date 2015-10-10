<?php

namespace NetPhp\Core;

use NetPhp\Core\MagicWrapper;

class MagicWrapperUtilities extends ComProxy {

  public static function GetInstance() {
    $instance = new MagicWrapperUtilities();
    $configuration = Configuration::GetConfiguration();
    $instance->_InstantiateDOTNET($configuration->getAssemblyFullQualifiedName(), $configuration->GetMagicWrapperUtilitiesClassName());
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
   * Returns a MagicWrapped instance of List<string>
   */
  public function GetIteratorSample() {
    $instance = MagicWrapper::Get($this->host->GetIteratorSample());
    return $instance;
  }
  
  /**
   * Returns a MagicWrapped instance of Dictionary<string, string>
   */
  public function GetDictionaryIteratorSample() {
    $instance = MagicWrapper::Get($this->host->GetIteratorSample());
    return $instance;
  }
  
  /**
   * Throw an Exception from .Net
   */
  public function TestException() {
    $this->host->TestException();
  }
  
  /**
   * Version number and license type.
   */
  public function GetStringVersion() {
    return $this->host->GetStringVersion();
  }
  
  /**
   * Get the AssemblyName instance.
   */
  public function GetVersion() {
    $instance = MagicWrapper::Get($this->host->GetVersion());
    return $instance;
  }

  /**
   * The the list of installed .Net framwork versions.
   */
  public function GetAvailableFrameworkVersions() {
    $instance = MagicWrapper::Get($this->host->getAvailableFrameworkVersions());
    return NetProxyCollection::Get($instance);
  }
}
