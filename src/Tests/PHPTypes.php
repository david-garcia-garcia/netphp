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
    
    // The fact that NetProxyCollection supports the Countable interface
    // does not mean that the native type will allow to do so. Use this with care.
    $total = count($mylist);
    
    // Keys are brought back as native types.
    // Values are NetProxy instances.
    foreach($mylist as $key => $value) {
      $real_value = $value->Val();
    }
    
    // Get an instance of Dictionary<String, String>
    $mylist = \NetPhp\Core\NetProxyCollection::Get($utils->GetDictionaryIteratorSample());
    
    $total = count($mylist);
    
    foreach($mylist as $key => $value) {
      $real_value = $value->Val();
    }
    
    // Be brave and use .Net native lists on the fly.
    // ArrayList is the closes thing we have to a PHP array.
    $net1 = new NetManager();
    $net1->RegisterAssembly('mscorlib, Version=2.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089', 'mscorlib');
    $net1->RegisterClass('mscorlib', 'System.Collections.ArrayList', 'ArrayList');
    
    $list = $net1->Create('mscorlib', 'ArrayList')->Instantiate();
    
    // Wrap over a collection proxy
    $list = \NetPhp\Core\NetProxyCollection::Get($list->GetWrapper());
        
    // Check the .Net type.
    $net_type = $list->GetType();
    
    $start = microtime(TRUE);

    for ($x = 0; $x < 5000; $x++) {
      $list->Add("Object {$x}");
    }
    
    // Retrieve total count.
    $count = count($list);
    
    $total1 = microtime(TRUE) - $start;
    
    $start = microtime(TRUE);
    
    $list = array();
    
    for ($x = 0; $x < 5000; $x++) {
      $list[] = "Object {$x}";
    }
    
    // Retrieve total count.
    $count = count($list);
    
    $total2 = microtime(TRUE) - $start;
    
    $a  = 0;
    
  } 
}
