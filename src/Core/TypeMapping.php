<?php

namespace NetPhp\Core;

/**
 * Not in use yet, but here we are keeping track of
 * how COM transforms between native types.
 */
class TypeMapping {

  /**
   * PHP Types equivalentes in .Net
   * 
   * @var string[]
   */
  private static $mappings = array(
    'string' => 'System.String',
    'integer' => 'System.Int32',
    'double' => 'System.Double',
    'boolean' => 'System.Boolean',
    'NULL' => 'System.DBNull',
    'array' => 'System.Object[]',
    'object' => 'System.__ComObject',
  );

  /**
   * .Net types to PHP types.
   * 
   * @var string[]
   */
  private static $mapping_flipped;

  /**
   * Get the .Net type from the PHP type. This expects
   * a basic type (from the gettype function).
   *
   * @param string $php_type 
   *
   * @return string
   */
  public static function GetNetType($php_type) {
    return static::$mappings[$php_type];
  }

  /**
   * Gets the PHP type form the .Net type
   * @param string $net_type 
   * @return string
   */
  public static function GetPHPType($net_type) {
    if (!isset(static::$mapping_flipped)) {
      static::$mapping_flipped = array_flip(static::$mappings);
    }
    return static::$mapping_flipped[$net_type];
  }
}
