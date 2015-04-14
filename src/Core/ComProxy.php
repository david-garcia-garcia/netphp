<?php

namespace NetPhp\Core;

use NetPhp\Core\MagicWrapper;

/**
 * There are some limitations the framework version.
 * @see https://bugs.php.net/bug.php?id=55847
 * @see https://bugs.php.net/bug.php?id=29800
 */
abstract class ComProxy {

  protected function __construct() {}

  // @var variant $hots
  //   The native COM object
  protected $host;
  
  /**
   * Use this to instantiate an assembly by using the full
   * qualified name + placing the binary in a discoverable place.
   *
   * The problem is that it will only load assemblies that
   * are compiled for .NET 3.5 or below.
   *
   * @param string $assembly
   *  Assembly Full Qualified Name
   *
   * @param string $class 
   *  Class Full Qualified Name
   */
  protected function _InstantiateDOTNET($assembly, $class) {
    try {
      $this->host = new \DOTNET($assembly, $class);
    }
    catch (\Exception $e) {
      COMExceptionManager::Manage($e);
    }
  }
  
  /**
   * Use this to instantiate a COM registered object
   * you can use this trick to expose .NET 4 and above
   * libraries to PHP.
   *
   * To register de COM object use regasm. Then place the .dll file
   * in a discoverable place such as with the php.exe or you can
   * register it into the GAC.
   *
   * Common mistakes when registering with regasm:
   * @see http://stackoverflow.com/questions/5136638/regasm-just-doesnt-work
   *
   *  - Forgetting to use the /codebase option. Required if you don't deploy the assembly to the GAC, 
   *    something you should not do on your dev machine.
   *
   *  - Using the wrong version of Regasm.exe. There are two on a 64-bit machine, the 
   *    Framework64 directory contains the one you have to use if the client code is 64-bit.
   *
   *  - Running it from a command prompt that is not elevated. Regasm.exe writes to the HKLM hive 
   *    of the registry, something that UAC actively prevents. That's an issue on Vista and Win7.
   *
   * @param string $name 
   */
  protected function _InstantiateCOM($name) {
    try {
      $this->host = new \COM($name);
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
