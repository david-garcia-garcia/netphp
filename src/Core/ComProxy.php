<?php

namespace NetPhp\Core;

use NetPhp\Core\MagicWrapper;

/**
 * Helper Class to Wrap around a native COM object.
 */
abstract class ComProxy {

  /**
   * Manage internal .Net Exceptions
   * and convert them to PHP Exceptions
   */
  protected function ManageExceptions() {
    $str = $this->host->LastErrorDump();
    $error = $this->host->PopLastError();
    if ($error !== NULL) {
      throw new \Exception($str);
    }
    
  }
  
  
  protected function __construct() {}

  // @var variant $hots
  //   The native COM object
  protected $host;
  
  /**
   * Wrap around a new COM instance of the
   * provided assembly and class.
   */
  protected function _Instantiate($assembly, $class) {
    try {
      $this->host = new \DOTNET($assembly, $class);
    }
    catch (\Exception $e) {
      COMExceptionManager::Manage($e);
    }
  }
  
  /**
   * Wrap around an already created COM instance.
   */
  protected function _Wrap($source) {
    $this->host = $source;
  }
}
