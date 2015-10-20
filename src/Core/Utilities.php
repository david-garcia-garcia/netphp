<?php

namespace NetPhp\Core;

/**
 * Utilities short summary.
 *
 * Utilities description.
 *
 * @version 1.0
 * @author David
 */
class Utilities {

  /**
   * Emulates ...args
   *
   * @param string $method
   *
   * @param string $class
   *
   * @param mixed[] $num
   */
  public static function GetArgs($args, $method, $class = NULL) {
    if ($args === FALSE) {
      return array();
    }
    $count = static::NumberOfParameters($method, $class);
    return array_splice($args, $count);
  }

  /**
   * Summary of NumberOfParameters
   *
   * @param string $method
   * 
   * @param string $class
   * 
   * @return mixed
   */
  public static function NumberOfParameters($method, $class) {
    static $cache = array();
    $key = $method . '::' . $class;
    if (!isset($cache[$key])) {
      if (!empty($class)) {
        $c = new \ReflectionClass($class);
        $m = $c->getMethod($method);
      }
      else {
        $m = new \ReflectionFunction($method);
      }

      $cache[$key] = $m->getNumberOfParameters();
    }
    return $cache[$key];
  }

  /**
   * Get the directory where the file that defines
   * a class is found.
   *
   * @param mixed|string $class
   *   Either the name of the class or an instance of it.
   *
   * @return string
   */
  public static function GetClassLocation($class) {
    $reflector = new \ReflectionClass($class);
    return dirname($reflector->getFileName());
  }

}
