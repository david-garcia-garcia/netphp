<?php

namespace NetPhp\Core;

use NetPhp\Core\MagicWrapper;

class MagicWrapperUtilities extends ComProxy {

  public static function GetInstance() {
    $instance = new MagicWrapperUtilities();
    $instance->_Instantiate(Constants::ASSEMBLY, Constants::MWU_CLASS);
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
    $instance = MagicWrapper::Get()->Wrap($this->host->GetIteratorSample());
    return $instance;
  }
}
