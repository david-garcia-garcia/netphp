<?php


/**
 * Testea la navegación principal, login y main page.
 */
class FrameworkTest extends \PHPUnit_Framework_TestCase { 

  /**
   *  Make sure that the NetPhp is installed and can be instantiated
   */
  public function testDifferentFrameworkVersions() {

    // Get a list of available .Net framework version in this machine.
    $utilities = \NetPhp\Core\MagicWrapperUtilities::GetInstance();

    $netPhpVersion = $utilities->GetStringVersion();

    // For performance reasons type metadata is only loaded on demand
    // or when a specific operation needs it. Use this to see detailed information
    // about the unerlying .Net type of a MagicWrapper.
    $netPhpBinaryVersion = $utilities->GetVersion();
    $metadata = $netPhpBinaryVersion->GetMetadata();
    $this->assertEquals('System.Reflection.AssemblyName', $metadata['name']);

    // Available framwork versions is a List<string> .Net type.
    $versions = $utilities->GetAvailableFrameworkVersions();
    $metadata = $versions->GetMetadata();
    $this->assertEquals('System.Collections.Generic', $metadata['namespace']);

    // Make sure we can iterate through this!
    $net_versions = array();
    foreach ($versions as  $version) {
      $net_versions[] = $version->Val();
    }
    $this->assertEquals(count($net_versions), count($versions));


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