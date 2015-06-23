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
   * @param mixed $method
   * @param mixed $class
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
   * @param mixed $method 
   * @param mixed $class 
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
  
}
