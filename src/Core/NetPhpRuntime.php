<?php

namespace NetPhp\Core;

use NetPhp\Core\MagicWrapper;

/**
 * Runtime for NetPhp.
 *
 * COM CLASS ID FOR THE 2.X VERSION OF THE BINARY: {2BF990C8-2680-474D-BDB4-65EEAEE0015F}
 */
class NetPhpRuntime extends ComProxy {

  /**
   * {@inheritdoc}
   */
  public function __construct($loadMode = 'COM', $className = 'netutilities.NetPhpRuntime', $assemblyName = NULL) {
    parent::__construct($loadMode, $className, $assemblyName);
  }

  /**
   * Wrap over an existing COM object
   *
   * @param mixed $source
   */
  public static function Wrap($source) {
    $instance = new NetPhpRuntime();
    $instance->_Wrap($source);
    return $instance;
  }

  /**
   * Pass in any object and get it's .Net type. If you
   * pass in a MagicWrapper, it will get the type of the
   * internal object. You can use this to check out what
   * types do PHP natives get converted into by COM interop.
   *
   * @param mixed $object
   *
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
   * Get the version and license type for the NetPhp binary library.
   *
   * @return string
   */
  public function GetStringVersion() {
    return $this->host->GetStringVersion();
  }

  /**
   * Get a list of Types that match the objects returned in GetSamples().
   */
  public function GetSampleTypes() {
    $instance = MagicWrapper::Get($this->host->GetSampleTypes());
    return NetProxyCollection::Get($instance);
  }

  /**
   * Get a list of sample objects from .Net.
   */
  public function GetSamples() {
    $instance = MagicWrapper::Get($this->host->GetSamples());
    return NetProxyCollection::Get($instance);
  }

  /**
   * Returns a wrapped Assembly.GetExecutingAssembly()
   */
  public function GetExecutingAssembly() {
    $instance = MagicWrapper::Get($this->host->GetExecutingAssembly());
    return NetProxy::Get($instance);
  }

  /**
   * Get the Environment.Version object.
   *
   * @return NetProxy
   */
  public function GetRuntimeVersion() {
    $instance = MagicWrapper::Get($this->host->GetRuntimeVersion());
    return NetProxy::Get($instance);
  }

  /**
   * The the list of installed .Net framwork versions.
   */
  public function GetAvailableFrameworkVersions() {
    return MagicWrapper::Get($this->host->getAvailableFrameworkVersions())->GetPhpFromJson();
  }

  /**
   * Get a type by providing the assembly name.
   *
   * @param string $className
   *
   * @param string $assemblyName
   *
   * @param string $proxy_class
   *
   * @param mixed $data
   *
   * @return mixed
   */
  public function TypeFromAssembly($className, $assemblyName, $proxy_class = NULL, $data = NULL) {
    return $this->GetType($className, NULL, $assemblyName, $proxy_class, $data);
  }

  /**
   * Get a type by providing the assembly file name.
   *
   * @param string $className
   *
   * @param string $assemblyPath
   *
   * @param string $proxy_class
   *
   * @param mixed $data
   *
   * @return mixed
   */
  public function TypeFromFile($className, $assemblyPath, $proxy_class = NULL, $data = NULL) {
    return $this->GetType($className, $assemblyPath, NULL, $proxy_class, $data);
  }

  /**
   * Get a type without specifing an assembly. The assembly
   * must already be registered in the runtime.
   *
   * @param string $className
   *
   * @param string $proxy_class
   *
   * @param mixed $data
   *
   * @return mixed
   */
  public function TypeFromName($className, $proxy_class = NULL, $data = NULL) {
    return $this->GetType($className, NULL, NULL, $proxy_class, $data);
  }

  /**
   * Return a VARIANT MagicWrapper instance over the specified type.
   *
   * @param string $className
   *
   * @param string $assemblyPath
   *
   * @return NetProxy
   */
  protected function GetType($className, $assemblyPath = NULL, $assemblyName = NULL, $proxy_class = NULL, $data = NULL, $typeMap = NULL) {

    $instance = NULL;

    if (!empty($assemblyPath)) {

      // This file exists here is very useful to diagnose
      // wrong paths or lack of permissions.
      if (!file_exists($assemblyPath)) {
        throw new \Exception("Could not find assembly file: $assemblyPath");
      }

      $instance = $this->host->TypeFromFile($className, $assemblyPath);
    }
    else if (!empty($assemblyName)) {
      $instance = $this->host->TypeFromName($className, $assemblyName);
    }
    else {
      $instance = $this->host->TypeFromClassOnly($className);
    }

    $mw = MagicWrapper::Get($instance);

    if ($data !== NULL) {
      $mw->Wrap($data);
    }

    /** @var mixed */
    $proxy = NULL;

    if (!empty($proxy_class)) {
      $proxy = $proxy_class::Get($mw, $typeMap);
    }
    else {
      $proxy = NetProxy::Get($mw, $typeMap);
    }

    return $proxy;
  }

  /**
   * Inspect all the assemblies in a directory to get their FQDN.
   *
   * @param string $path
   */
  public function InspectDirectoryAssemblies($path) {
    return MagicWrapper::Get($this->host->InspectDirectoryAssemblies($path))->GetPhpFromJson();
  }

  /**
   * Register an assembly from a .dll file.
   *
   * @param string $assemblyPath
   *   The assembly path.
   *
   * @param string $alias
   */
  public function RegisterAssemblyFromFile($assemblyPath, $alias) {

    // Make sure the file exists and is accesible.
    if (!file_exists($assemblyPath)) {
      throw new \Exception("Could not acces file: $assemblyPath. Make sure that the file exists and is accessible by the PHP process.");
    }

    // Store a copy in our internal MAP.
    $this->assemblies[$alias] = array('path' => $assemblyPath, 'classes' => array());

    // Preload this in the host.
    $this->host->RegisterAssemblyFromFile($assemblyPath);
  }

  /**
   * If you know the progId of a COM type and what to include all the assembly
   * it belongs to, use this.
   *
   * For example, you can bring in the Whole Word interop assembly by using
   * "word.application" as the progId.
   *
   * @param string $progId
   */
  public function RegisterAssemblyFromProgId($progId) {
    // Preload this in the host.
    $this->host->RegisterAssemblyFromProgId($progId);
  }

  /**
   * Register an assembly from the FQDN.
   *
   * @param string $assembly
   *   The assembly FQDN.
   *
   * @param string $alias
   */
  public function RegisterAssemblyFromFullQualifiedName($assembly, $alias) {

    // Store an internal copy.
    $this->assemblies[$alias] = array('fqdn' => $assembly, 'classes' => array());

    // Preload this in the host.
    $this->host->RegisterAssemblyFromName($assembly);
  }

  #region Preconfigured Set of Binaries for the .Net framework

  /**
   * Register types for the .Net framework between 2.0 and 3.5
   */
  public function RegisterNetFramework2() {
    $this->RegisterAssemblyFromFullQualifiedName("mscorlib, Version=2.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089", "mscorlib");
    $this->RegisterAssemblyFromFullQualifiedName("System.Core, Version=2.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089", "System.Core");
    $this->RegisterAssemblyFromFullQualifiedName("System.Transactions, Version=2.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089", "System.Transactions");
    $this->RegisterAssemblyFromFullQualifiedName("System.Data, Version=2.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089", "System.Data");
    $this->RegisterAssemblyFromFullQualifiedName("System.Security, Version=2.0.0.0, Culture=neutral, PublicKeyToken=b03f5f7f11d50a3a", "System.Security");
    $this->RegisterAssemblyFromFullQualifiedName("System.Xml, Version=2.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089", "System.Xml");
    $this->RegisterAssemblyFromFullQualifiedName("System, Version=2.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089", "System");
    $this->RegisterAssemblyFromFullQualifiedName("System.Web.RegularExpressions, Version=2.0.0.0, Culture=neutral, PublicKeyToken=b03f5f7f11d50a3a", "System.Web.RegularExpressions");
    $this->RegisterAssemblyFromFullQualifiedName("System.Windows.Forms, Version=2.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089", "System.Windows.Forms");
    $this->RegisterAssemblyFromFullQualifiedName("System.Management, Version=2.0.0.0, Culture=neutral, PublicKeyToken=b03f5f7f11d50a3a", "System.Management");
    $this->RegisterAssemblyFromFullQualifiedName("System.Drawing, Version=2.0.0.0, Culture=neutral, PublicKeyToken=b03f5f7f11d50a3a", "System.Drawing");
    $this->RegisterAssemblyFromFullQualifiedName("System.EnterpriseServices, Version=2.0.0.0, Culture=neutral, PublicKeyToken=b03f5f7f11d50a3a", "System.EnterpriseServices");
    $this->RegisterAssemblyFromFullQualifiedName("System.Deployment, Version=2.0.0.0, Culture=neutral, PublicKeyToken=b03f5f7f11d50a3a", "System.Deployment");
    $this->RegisterAssemblyFromFullQualifiedName("System.Web, Version=2.0.0.0, Culture=neutral, PublicKeyToken=b03f5f7f11d50a3a", "System.Web");
    $this->RegisterAssemblyFromFullQualifiedName("System.Configuration, Version = 2.0.0.0, Culture = neutral, PublicKeyToken = b03f5f7f11d50a3a", "System.Configuration");
  }

  /**
   * Register types for the .Net framework 4.0 and beyond.
   */
  public function RegisterNetFramework4() {
    $this->RegisterAssemblyFromFullQualifiedName("mscorlib, Version=4.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089", "mscorlib");
    $this->RegisterAssemblyFromFullQualifiedName("System.Core, Version=4.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089", "System.Core");
    $this->RegisterAssemblyFromFullQualifiedName("System.Transactions, Version=4.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089", "System.Transactions");
    $this->RegisterAssemblyFromFullQualifiedName("System.Data, Version=4.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089", "System.Data");
    $this->RegisterAssemblyFromFullQualifiedName("System.Security, Version=4.0.0.0, Culture=neutral, PublicKeyToken=b03f5f7f11d50a3a", "System.Security");
    $this->RegisterAssemblyFromFullQualifiedName("System.Xml, Version=4.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089", "System.Xml");
    $this->RegisterAssemblyFromFullQualifiedName("System, Version=4.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089", "System");
    $this->RegisterAssemblyFromFullQualifiedName("System.Web.RegularExpressions, Version=4.0.0.0, Culture=neutral, PublicKeyToken=b03f5f7f11d50a3a", "System.Web.RegularExpressions");
    $this->RegisterAssemblyFromFullQualifiedName("System.Windows.Forms, Version=4.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089", "System.Windows.Forms");
    $this->RegisterAssemblyFromFullQualifiedName("System.Management, Version=4.0.0.0, Culture=neutral, PublicKeyToken=b03f5f7f11d50a3a", "System.Management");
    $this->RegisterAssemblyFromFullQualifiedName("System.Drawing, Version=4.0.0.0, Culture=neutral, PublicKeyToken=b03f5f7f11d50a3a", "System.Drawing");
    $this->RegisterAssemblyFromFullQualifiedName("System.EnterpriseServices, Version=4.0.0.0, Culture=neutral, PublicKeyToken=b03f5f7f11d50a3a", "System.EnterpriseServices");
    $this->RegisterAssemblyFromFullQualifiedName("System.Deployment, Version=4.0.0.0, Culture=neutral, PublicKeyToken=b03f5f7f11d50a3a", "System.Deployment");
    $this->RegisterAssemblyFromFullQualifiedName("System.Web, Version=4.0.0.0, Culture=neutral, PublicKeyToken=b03f5f7f11d50a3a", "System.Web");
    $this->RegisterAssemblyFromFullQualifiedName("System.Configuration, Version = 4.0.0.0, Culture = neutral, PublicKeyToken = b03f5f7f11d50a3a", "System.Configuration");
  }

  #endregion

  /**
   * Reregister all of our assemblies in the dumper.
   *
   * @param TypeDumper $dumper
   *
   * @throws \Exception
   */
  public function RegisterAssembliesInDumper(TypeDumper $dumper) {
    foreach ($this->assemblies as $assembly) {
      if (isset($assembly['path'])) {
        $dumper->RegisterAssemblyFromFileName($assembly['path']);
      }
      else if (isset($assembly['fqdn'])){
        $dumper->RegisterAssemblyFromFullQualifiedName($assembly['fqdn']);
      }
      else {
        throw new \Exception("Unexpected...");
      }
    }
  }

  /**
   * Get a report of the .Net registered assemblies and it's dependencies.
   *
   * Use this to detect dependencies between libraries that cannot be resolved.
   */
  public function GetAssemblyReport() {
    return MagicWrapper::Get($this->host->GetAssemblyReport())->GetPhpFromJson();
  }

  /**
   * The runtime has to do cleanup tasks to get rid of old dll files
   * that were locked due to concurrency issues.
   */
  public function Cron() {
    $this->host->Cron();
  }




  #region License Related Methods

  /**
   * Get the current activation key.
   */
  public function ActivationCurrentKey() {
    return $this->host->ActivationCurrentKey();
  }

  /**
   * Get this machine code used for activation.
   */
  public function ActivationGetCode() {
    return $this->host->ActivationGetCode();
  }

  /**
   * Set the activation key.
   *
   * @param string $key
   */
  public function ActivationSetKey($key) {
    $this->host->ActivationSetKey($key);
  }

  /**
   * Trigger the activation process to check
   * if current key is valid.
   */
  public function ActivationValid() {
    return $this->host->ActivationValid();
  }

  /**
   * Clear out internal caches to make sure
   * that persistent key storage is working.
   */
  public function ActivationClearCaches() {
    $this->host->ActivationClearCaches();
  }

  /**
   * Get location of the Activation File
   */
  public function ActivationLicenseLocation() {
    return $this->host->ActivationLicenseLocation();
  }

  /**
   * Initialize the license system and license path. You cannot call
   * any license related methods until this has been called.
   *
   * @param string $file
   *   Path to the license file. License files are automatically
   *   generated and modified during runtime, so the process
   *   must have read/write permissions on the file.
   *
   * @param boolean $aggresive
   *   Only for debug purposes, used to ALWAYS throw exception if
   *   license is invalid.
   */
  public function ActivationLicenseInitialize($file, $aggresive = FALSE) {
    $this->host->ActivationLicenseInitialize($file, $aggresive);
  }

  /**
   * Get a demo KEY with a valid format used for testing purposes.
   *
   * @return string
   */
  public function ActivationKeyGetSample() {
    return $this->host->ActivationKeyGetSample();
  }

  /**
   * Helper method to completely remove a COM component
   * from the registry using its COM ID. To be able to run
   * this method you should give the caller process enough
   * permissions to be able to modify the system registry.
   * 
   * If you are having permission issues and are unable
   * to run this method, setup a desktop project and
   * copy the C# implementation from the source code.
   *
   * @param string[] $classes
   *   An array of COM class identifiers such as:
   *     "{8F952AD2-63A8-3D2C-BC15-C78FB902C616}",
   *     "{8B2C194A-8C2A-324F-8CFD-73AD8E1E30ED}",
   *     "{2D1896A6-6528-438C-9890-55778147D5BD}",
   *     "{CC968291-7A4E-3A2F-8667-F67CE8F3BD36}",
   *     "{129CCB19-A796-319A-926F-B57211846186}",
   */
  public function RegistryHellCleanup($classes) {
    $this->host->RegistryHellCleanup();
  }

  /**
   * Configure the temporary path that the runtime
   * should use when it has issues to instantiate al library
   * due to locked binary problems. If not set, the runtime
   * will use the system default temporary path.
   * 
   * @param string $directory 
   */
  public function SetTempDirectory($directory) {
    $this->host->SetTempDirectory($directory);
  }

  #endregion
}
