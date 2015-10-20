//<?php

//namespace NetPhp\Core;

//use \NetPhp\Core\NetProxy;

///**
// * Manager used to create Magically Wrapped instances
// * of .Net objects.
// *
// * This is currently a mess, but it's decoupled from the proxies
// * so can be easy replace with something better. In the end the aim
// * of this manager is to give us an instance of MagicWrapper loaded with an
// * internal .Net type.
// */
//class NetManager {

//  private static $instance = null;

//  /**
//   * Retrieve the current Runtime Manager instance.
//   *
//   * @return NetManager
//   */
//  public static function GetInstance() {
//    if (static::$instance == NULL) {
//      static::$instance = new NetManager();
//    }
//    return static::$instance;
//  }

//  private function __construct() {}

//  private $assemblies = array();

//  /**
//   * Reregister all of our assemblies in the dumper.
//   *
//   * @param TypeDumper $dumper
//   *
//   * @throws \Exception
//   */
//  public function RegisterAssembliesInDumper(TypeDumper $dumper) {
//    foreach ($this->assemblies as $assembly) {
//      if (isset($assembly['path'])) {
//        $dumper->RegisterAssemblyFromFileName($assembly['path']);
//      }
//      else if (isset($assembly['fqdn'])){
//        $dumper->RegisterAssemblyFromFullQualifiedName($assembly['fqdn']);
//      }
//      else {
//        throw new \Exception("Unexpected...");
//      }
//    }
//  }

//  /**
//   * Use class aliases to ease usage
//   * of the library.
//   *
//   * @param string $assemblyAlias
//   *   The alias of the assembly this type belongs to.
//   *
//   * @param string $class
//   *   The full name of this class.
//   *
//   * @param mixed $alias
//   *   The alias of this class. If null it will use the last part of the full name.
//   */
//  public function RegisterClass($assemblyAlias, $class, $alias = NULL) {

//    if (!isset($this->assemblies[$assemblyAlias])) {
//      throw new \Exception("Assembly alias not registered: $assemblyAlias");
//    }

//    // Automatic alias generation.
//    if (empty($alias)) {
//      $parts = explode('.', $class);
//      $alias = end($parts);
//    }

//    $this->assemblies[$assemblyAlias]['classes'][$alias] = $class;
//  }

//  /**
//   * Summary of ResolveClass
//   * 
//   * @param string $assemblyName 
//   * 
//   * @param string $className 
//   * 
//   * @throws \Exception 
//   * 
//   * @return ResolvedClass
//   */
//  private function ResolveClass($assemblyName, $className) {
//    $resolved = new ResolvedClass();

//    if (isset($this->assemblies[$assemblyName]['path'])) {
//      $resolved->assemblyPath = $this->assemblies[$assemblyName]['path'];
//    }
//    else if (isset($this->assemblies[$assemblyName]['fqdn'])) {
//      $resolved->assemblyFullQualifiedName = $this->assemblies[$assemblyName]['fqdn'];
//    }
//    else {
//      throw new \Exception("Unexpected...");
//    }

//    $resolved->classFullQualifiedName = isset($this->assemblies[$assemblyName]['classes'][$className]) ? $this->assemblies[$assemblyName]['classes'][$className] : $className;
    
//    return $resolved;
//  }

//}