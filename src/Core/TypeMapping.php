<?php

namespace NetPhp\Core;

/**
 * Native Type mappings between .Net and PHP
 */
class TypeMapping {

  // @var array $mappings
  //   PHP types to .Net types
  private static $mappings = array(
    'string' => 'System.String',
    'integer' => 'System.Int32',
    'double' => 'System.Double',
    'boolean' => 'System.Boolean',
    'NULL' => 'System.DBNull',
    'array' => 'System.Object[]',
    'object' => 'System.__ComObject',
  );
  
  // @var array $mapping_flipped
  private static $mapping_flipped;
  
  /**
   * Get the .Net type from the PHP type
   * @param mixed $php_type 
   * @return mixed
   */
  public static function GetNetType($php_type) {
    return static::$mappings[$php_type];
  }
  
  /**
   * Gets the PHP type form the .Net type
   * @param mixed $net_type 
   * @return mixed
   */
  public static function GetPHPType($net_type) {
    if (!isset(static::$mapping_flipped)) {
      static::$mapping_flipped = array_flip(static::$mappings);
    }
    return static::$mapping_flipped[$net_type];
  }
}
