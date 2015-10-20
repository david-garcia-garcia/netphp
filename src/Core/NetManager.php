<?php

namespace NetPhp\Core;

use \NetPhp\Core\NetProxy;

/**
 * Manager used to create Magically Wrapped instances
 * of .Net objects.
 *
 * This is currently a mess, but it's decoupled from the proxies
 * so can be easy replace with something better. In the end the aim
 * of this manager is to give us an instance of MagicWrapper loaded with an
 * internal .Net type.
 */
class NetManager {

  private static $instance = null;

  /**
   * Retrieve the current Runtime Manager instance.
   *
   * @return NetManager
   */
  public static function GetInstance() {
    if (static::$instance == NULL) {
      static::$instance = new NetManager();
    }
    return static::$instance;
  }

  private function __construct() {}

  private $assemblies = array();

  /**
   * Register an assembly from a .dll file.
   *
   * @param string $assemblyPath
   *   The assembly path.
   *
   * @param string $alias
   */
  public function RegisterAssemblyFromFile($assemblyPath, $alias) {
    if (!file_exists($assemblyPath)) {
      throw new \Exception("Could not acces file: $assemblyPath. Make sure that the file exists and is accessible by the PHP process.");
    }
    $this->assemblies[$alias] = array('path' => $assemblyPath, 'classes' => array());
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
    $this->assemblies[$alias] = array('fqdn' => $assembly, 'classes' => array());
  }

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
   * Use class aliases to ease usage
   * of the library.
   *
   * @param string $assemblyAlias
   *   The alias of the assembly this type belongs to.
   *
   * @param string $class
   *   The full name of this class.
   *
   * @param mixed $alias
   *   The alias of this class. If null it will use the last part of the full name.
   */
  public function RegisterClass($assemblyAlias, $class, $alias = NULL) {

    if (!isset($this->assemblies[$assemblyAlias])) {
      throw new \Exception("Assembly alias not registered: $assemblyAlias");
    }

    // Automatic alias generation.
    if (empty($alias)) {
      $parts = explode('.', $class);
      $alias = end($parts);
    }

    $this->assemblies[$assemblyAlias]['classes'][$alias] = $class;
  }

  /**
   * Summary of ResolveClass
   * 
   * @param string $assemblyName 
   * 
   * @param string $className 
   * 
   * @throws \Exception 
   * 
   * @return ResolvedClass
   */
  private function ResolveClass($assemblyName, $className) {
    $resolved = new ResolvedClass();

    if (isset($this->assemblies[$assemblyName]['path'])) {
      $resolved->assemblyPath = $this->assemblies[$assemblyName]['path'];
    }
    else if (isset($this->assemblies[$assemblyName]['fqdn'])) {
      $resolved->assemblyFullQualifiedName = $this->assemblies[$assemblyName]['fqdn'];
    }
    else {
      throw new \Exception("Unexpected...");
    }

    $resolved->classFullQualifiedName = isset($this->assemblies[$assemblyName]['classes'][$className]) ? $this->assemblies[$assemblyName]['classes'][$className] : $className;
    
    return $resolved;
  }

  /**
   * Summary of Create
   *
   * @param string $assembly
   * @param string $class
   *
   * @return mixed
   */
  public function Create($assembly, $class) {
    $resolved = self::ResolveClass($assembly, $class);
    $native = MagicWrapper::GetFromType($resolved);
    return NetProxy::Get($native);
  }

  /**
   * Summary of CreateStatic
   *
   * @param string $assembly
   *   Assembly where the type is declared.
   *
   * @param string $class
   *   Full qualified name of the class.
   *
   * @param mixed $data
   *   Data to wrap over.
   *
   * @return mixed
   */
  public static function CreateStatic($assembly, $class, $proxy_class = NULL, $data = NULL) {
    $resolved = new ResolvedClass();
    $resolved->assemblyFullQualifiedName = $assembly;
    $resolved->classFullQualifiedName = $class;
    $native = MagicWrapper::GetFromType($resolved);
    if ($data !== NULL) {
      $native->Wrap($data);
    }
    if (!empty($proxy_class)) {
      return $proxy_class::Get($native);
    }

    /** @var mixed */
    $res = NetProxy::Get($native);
    return $res;
  }

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
}