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

  private $instance = null;

  /**
   * Retrieve the current Runtime Manager instance.
   *
   * @return NetManager
   */
  public function GetInstance() {
    if ($this->instance == NULL) {
      $this->instance = new NetManager();
    }
    return $this->instance;
  }

  private function __construct() {}

  private $assemblies = array();

  /**
   * Summary of RegisterAssembly
   *
   * @param string $assemblyPath
   *   The assembly path or full qualified name.
   *
   * @param mixed $alias
   */
  public function RegisterAssembly($assemblyPath, $alias) {
    $this->assemblies[$alias] = array('path' => $assemblyPath, 'classes' => array());
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

  private function ResolveClass($assemblyName, $className) {
    $resolved = new ResolvedClass();
    $resolved->assemblyFullQualifiedName = $this->assemblies[$assemblyName]['path'];
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

}