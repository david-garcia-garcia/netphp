<?php

namespace NetPhp\Core;

/**
 * This class is used by the PHP class
 * model generator to interact with the runtime
 * and to expose a list of dumped types.
 */
abstract class TypeMapBase {

  /**
   * Resolves a .Net type into a PHP class
   * using the dumped class map.
   * 
   * @param string $nettype 
   */
  public static function ResolveNetType($nettype) {

    foreach (static::GetTypes() as $type) {
      if (isset($type['types'][$nettype])) {
        return $type['types'][$nettype];
      }
    }

    // If we could not map, return a net proxy.
    return NetProxy::class;
  }

  protected static function GetTypes() {
    throw new \Exception("This method must be implemented by child classes.");
  }

  /**
   * @var NetPhpRuntime
   */
  protected static $runtime;

  /**
   * Set the runtime that will be used by this
   * PHP class model.
   *
   * @param NetPhpRuntime $runtime
   */
  public static function SetRuntime(NetPhpRuntime $runtime) {
    static::$runtime = $runtime;
  }

  /**
   * Get the current runtime.
   *
   * @return NetPhpRuntime
   */
  public static function GetRuntime() {
    return static::$runtime;
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
  public static function TypeFromAssembly($className, $assemblyName, $proxy_class = NULL, $data = NULL) {
    return static::$runtime->TypeFromAssembly($className, $assemblyName, $proxy_class, $data);
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
    return static::$runtime->TypeFromFile($className, $assemblyPath, $proxy_class, $data);
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
    return static::$runtime->TypeFromName($className, $proxy_class, $data);
  }

}
