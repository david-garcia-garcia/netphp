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
    
    // Try some .Net to PHP native COM conversions and see what they throw.
    $utils = \NetPhp\Core\MagicWrapperUtilities::GetInstance();
    
    $locals = array();
    for($x = 0; $x < 8; $x ++){
      $locals[] = $utils->GetTypeSample($x);
    }
    
    // Get an instance of List<String>
    $mylist = \NetPhp\Core\NetProxyCollection::Get($utils->GetIteratorSample());
    
    // Keys are brought back as native types.
    // Values are NetProxy's
    foreach($mylist as $key => $value) {
      $real_value = $value->Val();
    }
  } 
}
