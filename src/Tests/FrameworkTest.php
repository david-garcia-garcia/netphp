<?php


/**
 * Testea la navegación principal, login y main page.
 */
class FrameworkTest extends \PHPUnit_Framework_TestCase { 

  /**
   *  Make sure that the NetPhp is installed and can be instantiated
   */
  public function testBasicFrameworkUsage() {

    // Use COM so that we can use newer versiones of the .Net framework.
    \NetPhp\Core\Configuration::GetConfiguration()->setLoadMode("COM");

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
    foreach ($versions->AsIterator() as  $version) {
      $net_versions[] = $version->Val();
    }
    $this->assertEquals(count($net_versions), count($versions));

    $manager = new \NetPhp\Core\NetManager();

    // Test dealing with an ArrayList
    $manager->RegisterAssembly('mscorlib, Version=2.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089', 'mscorlib');
    $manager->RegisterClass('mscorlib', 'System.Collections.ArrayList', 'ArrayList');
    $list = $manager->Create('mscorlib', 'ArrayList')->Instantiate();

    // Wrap over a collection proxy
    $list = $list->AsIterator();  

    // Check the .Net type.
    $net_type = $list->GetType();
    $php_list = array();
    $this->assertEquals('System.Collections.ArrayList', $net_type);
    for ($x = 0; $x < 200; $x++) {
      $list->Add("Object {$x}");
      $php_list[] = "Object {$x}";
    }

    $this->assertEquals(200, count($list));
    $manager->RegisterAssembly('System.Web, Version=2.0.0.0, Culture=neutral, PublicKeyToken=b03f5f7f11d50a3a', 'system.web2');
    $manager->RegisterClass('system.web2', 'System.Web.HttpRequest', 'HttpRequest');
    $request = $manager->Create('system.web2', 'HttpRequest')->Instantiate("d:\mmyfile.txt", "http://www.google.com", "");
    

    //// Testing dealing with another PHP runtime (and passing objects between them).
    $manager->RegisterAssembly('System.Web.Extensions, Version=4.0.0.0, Culture=neutral, PublicKeyToken=31bf3856ad364e35', 'system.web4');
    $manager->RegisterClass('system.web4', 'System.Web.Script.Serialization.JavaScriptSerializer', 'JavaScriptSerializer');
    $serializer = $manager->Create('system.web4', 'JavaScriptSerializer')->Instantiate();
    $result = $serializer->Serialize($list)->Val();

    $this->assertEquals($result, json_encode($php_list));

  }
}