<?php

/**
 * Testea la navegación principal, login y main page.
 */
class BasicTest extends PHPUnit_Framework_TestCase { 

  /**
   *  Make sure that the NetPhp is installed and can be instantiated
   */
  public function testArrayList() {
    $manager = new \NetPhp\Core\NetManager();
    // Usaremos cualquier cosa del .Net Framework
    $manager->RegisterAssembly('mscorlib, Version=2.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089', 'mscorlib');
    $manager->RegisterClass('mscorlib', 'System.Collections.ArrayList', 'ArrayList');
    $list = $manager->Create('mscorlib', 'ArrayList')->Instantiate();
    // Wrap over a collection proxy
    $list = $list->AsIterator();  
    // Check the .Net type.
    $net_type = $list->GetType();
    $this->assertEquals('System.Collections.ArrayList', $net_type);
    for ($x = 0; $x < 200; $x++) {
      $list->Add("Object {$x}");
    }
    $this->assertEquals(200, count($list));
  }
}