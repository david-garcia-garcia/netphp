<?php
namespace NetPhp\Tests;

use NetPhp\Core\NetManager;
use NetPhp\Core\MagicWrapper;

class PHPTypes {

  /**
   * TODO: Convert this piece of code into a PHP unit test.
   */
  public static function Run() {
    
    // Net Should report it's native types becase 
    // COM+ does converion for these.
    $PHPType = array();
    $PHPType[] = array('data' => 'mystring', 'netType' => 'System.String');
    $PHPType[] = array('data' => 1234, 'netType' => 'System.Int32');
    $PHPType[] = array('data' => 1.234, 'netType' => 'System.Double');
    $PHPType[] = array('data' => 0, 'netType' => 'System.Int32');
    $PHPType[] = array('data' => FALSE, 'netType' => 'System.Boolean');
    $PHPType[] = array('data' => NULL, 'netType' => 'System.DBNull');
    $PHPType[] = array('data' => array(), 'netType' => 'System.Object[]');
    $PHPType[] = array('data' => function() { $abstract = 0; }, 'netType' => 'System.__ComObject');
    
    $mappings = array();
    
    foreach ($PHPType as &$item) {
      $php_type = gettype($item['data']);
      $item['netType'] = \NetPhp\Core\NetUtilities::GetTypeAsString($item['data']);
      $mappings[$php_type] = $item['netType'];
    }
  } 
}
