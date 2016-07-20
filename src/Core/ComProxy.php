<?php

namespace NetPhp\Core;

use NetPhp\Core\MagicWrapper;

/**
 * There are some limitations the framework version.
 * 
 * @see https://bugs.php.net/bug.php?id=55847
 * @see https://bugs.php.net/bug.php?id=29800
 */
abstract class ComProxy {

  protected $className = 'netutilities.MagicWrapperUtilities';
  protected $assemblyName = '';
  protected $loadMode;


  /**
   * Returns an instance of the NetPhpRuntime
   *
   * @param string $loadMode
   *   Can be DOTNET or COM.
   *
   * @param string $className
   *   NetPhp binary type Full Qualified Name.
   *
   * @param string $assemblyName
   *   NetPhp binary type assembly Full Qualified Name.
   *
   * @throws \Exception
   */
  public function __construct($loadMode = 'COM', $className = 'netutilities.NetPhpRuntime', $assemblyName = NULL) {
    if (!in_array($loadMode, array('DOTNET', 'COM'))) {
      throw new \Exception("Invalid load mode. Use COM or DOTNET.");
    }

    $this->_loadMode = $loadMode;
    $this->class_name = $className;
    $this->assembly_name = $assemblyName;
  }

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
   * Get an instance.
   *
   * @return null
   */
  public function Initialize() {

    // Do NOT allow users to reinitialize an instance.
    if ($this->host != null) {
      throw new \Exception("This instance has already been initialized.");
    }

    if ($this->getLoadMode() == "DOTNET") {
      $this->_InstantiateDOTNET($this->assembly_name, $this->class_name);
    }
    else if ($this->getLoadMode() == "COM") {
      $this->_InstantiateCOM($this->class_name);
    }
  }

  /**
   * The VARIANT .Net object
   * 
   * @var mixed
   */
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
      if (!class_exists('DOTNET')) {
        throw new \Exception("DOTNET class not found. Verify that you have the COM_DOTNET php extension properly enabled.");
      }
      $this->ManageComCreateError($e);
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
      $this->host = new \COM($name, NULL, \CP_UTF8);
    }
    catch (\Exception $e) {
      if (!class_exists('COM')) {
        throw new \Exception("COM class not found. Verify that you have the COM_DOTNET php extension properly enabled.");
      }
      $this->ManageComCreateError($e);
    }
  }
  
  /**
   * Wrap around an already created COM instance.
   */
  protected function _Wrap($source) {
    $this->host = $source;
  }

  /**
   * Wrap over the exception if we have some additional
   * information otherwise rethrow.
   *
   * @param \Exception $e 
   * 
   * @throws \Exception 
   */
  protected function ManageComCreateError(\Exception $e) {
    if (strpos($e->getMessage(), '[0x80004002]') !== FALSE) {
      throw new \Exception('Could not instantiate .Net class, make sure it is decorated with the COMVisible(true) attribute and that it is marked as public.', $e->getCode(), $e);
    }
    else if (stripos($e->getMessage(), 'Invalid syntax') !== FALSE) {
      throw new \Exception('Could not instantiate COM class. Invalid syntax might meen that the COM component is simply not registered.', $e->getCode(), $e);
    }
    else {
      throw new \Exception($e->getMessage() . ' Try changing the Load User Profile property of your Application Pool settings.', $e->getCode(), $e);
    }
  }
}
