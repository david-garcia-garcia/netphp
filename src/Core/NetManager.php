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
  private $assemblies = array();
  
  /**
   * Summary of RegisterAssembly
   * @param mixed $assemblyPath 
   * @param mixed $alias 
   */
  public function RegisterAssembly($assemblyPath, $alias) {
    $this->assemblies[$alias] = array('path' => $assemblyPath, 'classes' => array());
  }
  
  public function RegisterClass($assemblyName, $class, $alias) {
    $this->assemblies[$assemblyName]['classes'][$alias] = $class;
  }
  
  private function ResolveClass($assemblyName, $className) {
    $resolved = new ResolvedClass();
    $resolved->assemblyFullQualifiedName = $this->assemblies[$assemblyName]['path'];
    $resolved->classFullQualifiedName = isset($this->assemblies[$assemblyName]['classes'][$className]) ? $this->assemblies[$assemblyName]['classes'][$className] : $className;
    return $resolved;
  }
  
  public function Create($assembly, $class) {
    $resolved = self::ResolveClass($assembly, $class);
    $native = MagicWrapper::GetFromType($resolved);
    return NetProxy::Get($native);
  }

  public static function CreateStatic($assembly, $class) {
    $resolved = new ResolvedClass();
    $resolved->assemblyFullQualifiedName = $assembly;
    $resolved->classFullQualifiedName = $class;
    $native = MagicWrapper::GetFromType($resolved);
    return NetProxy::Get($native);
  }

}