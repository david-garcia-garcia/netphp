<?php

namespace NetPhp\Core;

use NetPhp\Core\MagicWrapper;

/**
 * Runtime for NetPhp.
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
   * Summary of GetTypeAsString
   *
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
   * Get the AssemblyName instance.
   */
  public function GetExecutingAssembly() {
    $instance = MagicWrapper::Get($this->host->GetExecutingAssembly());
    return NetProxy::Get($instance);
  }

  /**
   * Get a Version object of the current
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
    $instance = MagicWrapper::Get($this->host->getAvailableFrameworkVersions());
    return NetProxyCollection::Get($instance);
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
  }

  /**
   * Register types for the .Net framework 4.0 and beyond.
   */
  public function RegisterNetFramework4() {
    $this->RegisterAssemblyFromFullQualifiedName("mscorlib, Version=4.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089", "mscorlib");
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
    $this->RegisterAssemblyFromFullQualifiedName("System.Deployment, Version4.0.0.0, Culture=neutral, PublicKeyToken=b03f5f7f11d50a3a", "System.Deployment");
    $this->RegisterAssemblyFromFullQualifiedName("System.Web, Version=4.0.0.0, Culture=neutral, PublicKeyToken=b03f5f7f11d50a3a", "System.Web");
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
}
