<?php

namespace NetPhp\Tests;

use NetPhp\ms\Typer;
use NetPhp\ms\System\String_;

/**
 * Testea la navegación principal, login y main page.
 */
class FrameworkTest extends \PHPUnit_Framework_TestCase {

  /**
   *  Rnadom test using the ABCPdf library.
   */
  public static function testABCPdf() {

    // Set loading type.
    \NetPhp\Core\Configuration::GetConfiguration()->setLoadMode("COM");


    // Retrieve an instance of the NetPhp Runtime.
    $manager = \NetPhp\Core\NetManager::GetInstance();

    // Register the .Net framework 2 (2 through 3.5)
    $manager->RegisterNetFramework2();
    $manager->RegisterAssemblyFromFile('D:\REPOSITORIOS_SABENTIS\drupal7\sites\all\modules\sabentis\fdf\net\bin\ABCpdf.dll', "ABCpdf");

    #region Design Time Type Dumping

    // You only need to run this code at design time
    // to generate the PHP class model to interact with PHP

    $dumper = \NetPhp\Core\TypeDumper::GetInstance();

    $dumper->SetDestination('D:\Repositories\netphp\src\ms');
    $dumper->SetBaseNamespace('NetPhp\ms');

    // Re-register all assemblies into the dumper.
    $manager->RegisterAssembliesInDumper($dumper);

    // Remember that these are regular expressions.
    $dumper->AddDumpFilter('^WebSupergoo\.ABCpdf8.\Doc');
    $dumper->AddDumpFilter('^System\.Convert');
    $dumper->AddDumpFilter('^System\.Collections');
    $dumper->AddDumpFilter('^System\.IO');
    $dumper->AddDumpFilter('^System\.Diagnostics\.Process');

    $dumper->SetDumpDepth(0);
    $dumper->GenerateModel();

    #endregion

    // Register .Net framework assemblies, the TypeMap is generated at design time
    // by the NetPhp dumper.
    \NetPhp\Core\Configuration::RegisterTypes(\NetPhp\ms\TypeMap::GetTypes());

    $vertical = TRUE;

    $doc = \NetPhp\ms\WebSupergoo\ABCpdf8\Doc::Doc_Constructor();

    $doc->HtmlOptions()->Engine(\NetPhp\ms\WebSupergoo\ABCpdf8\EngineType::Gecko());
    $doc->HtmlOptions()->Media(\NetPhp\ms\WebSupergoo\ABCpdf8\MediaType::Screen());
    $doc->HtmlOptions()->GeckoSubset()->UseScript(Typer::cBoolean(TRUE));

    $w = $doc->MediaBox()->Width();
    $h = $doc->MediaBox()->Height();

    if (!$vertical) {
      $l = $doc->MediaBox()->Left();
      $b = $doc->MediaBox()->Right();

      $doc->Transform()->Rotate(Typer::cDouble(90), $l, $b);
      $doc->Transform()->Translate($w, Typer::cDouble(0));

      $doc->Rect()->Width($h);
      $doc->Rect()->Height($w);
    }
    else {
      // rotate our rectangle
      $doc->Rect()->Width($w);
      $doc->Rect()->Height($h);
    }

    $url = Typer::cString('http://www.google.com');

    // Añadimos el HTML
    $theID = 0;
    try {
      $theID = $doc->addImageUrl($url, Typer::cBoolean(FALSE), \NetPhp\ms\System\Convert::_ToInt32($h), Typer::cBoolean(TRUE));
      while (TRUE) {
        $doc->FrameRect();
        if (!$doc->Chainable($theID)->Val()) {
          break;
        }

        $doc->Page($doc->AddPage());
        $theID = $doc->AddImageToChain($theID);
      }
    }
    catch (\Exception $ex) {
      // Bad news.
      //$this->assertEquals(TRUE, FALSE);
    }

    // Adjust the default rotation and save
    if (!$vertical) {
      $theID = $doc->GetInfoInt($doc->Root(), Typer::cString("Pages"));
      $doc->SetInfo($theID, Typer::cString("/Rotate"), Typer::cString("90"));
    }

    $bytes = $doc->GetData();

    // There is no way to deal with a byte[] in PHP
    // so use System.Convert to deal with that
    // using base64 as a bridge.
    $b64 = \NetPhp\ms\System\Convert::_ToBase64String($bytes)->Val();
    $pdf = base64_decode($b64);

    $path = Typer::cString("d:\\caca.pdf");

    //
    \NetPhp\ms\System\IO\File::_WriteAllBytes($path, $bytes);

    // Open windows explorer to that directory.
    \NetPhp\ms\System\Diagnostics\Process::_Start(Typer::cString("explorer.exe"), String_::_Format(Typer::cString("/select,\"{0}\""), $path));
  }

  /**
   * Test Native Type management.
   */
  public function testNativeTypes() {

    // Set loading type.
    \NetPhp\Core\Configuration::GetConfiguration()->setLoadMode("COM");

    // Register .Net framework assemblies.
    \NetPhp\Core\Configuration::RegisterTypes(\NetPhp\ms\TypeMap::GetTypes());

    // See what are the 4 php native types being convert to on the .Net side

    $utilities = \NetPhp\Core\MagicWrapperUtilities::GetInstance();

    $mappings = array(
      array('type' => 'System.String' , 'sample' => 'This is a string'),
      array('type' => 'System.String' , 'sample' => 'a'),
      array('type' => 'System.Int32' , 'sample' => 12),
      array('type' => 'System.Int32' , 'sample' => 0),
      array('type' => 'System.Double' , 'sample' => 1351844949494112151),
      array('type' => 'System.Double' , 'sample' => 1.3556989),
      array('type' => 'System.Boolean' , 'sample' => TRUE),
      array('type' => 'System.Boolean' , 'sample' => FALSE),
      array('type' => 'System.DBNull' , 'sample' => NULL),
      array('type' => 'System.Object[]' , 'sample' => array()),
      array('type' => 'System.__ComObject' , 'sample' => new MyClass()),
      );

    foreach ($mappings as $map) {
      $this->assertEquals($utilities->GetTypeAsString($map['sample']), $map['type']);
    }

    // Now let's give this a shot the other way round, ask for primitive .Net types
    // and se what we get.
    $types = $utilities->GetSampleTypes();
    $samples = $utilities->GetSamples();

    $count = count($types);

    $mappings = array();

    $encoded_results = <<<EOT
[
{"php_type":"boolean","net_type":"System.Boolean","error":false,"php_converted":true},
{"php_type":"integer","net_type":"System.Byte","error":false,"php_converted":97},
{"php_type":"integer","net_type":"System.Char","error":false,"php_converted":"a"},
{"php_type":"double","net_type":"System.Double","error":false,"php_converted":1.2354848484545e+16},
{"php_type":"integer","net_type":"System.Int16","error":false,"php_converted":1254},
{"php_type":"integer","net_type":"System.Int32","error":false,"php_converted":1258584564},
{"php_type":"object","net_type":"System.Int64","error":false,"php_converted":1.2758686845646e+17},
{"php_type":"integer","net_type":"System.SByte","error":false,"php_converted":1},
{"php_type":"double","net_type":"System.Single","error":false,"php_converted":1.27586865e+17},
{"php_type":"integer","net_type":"System.UInt16","error":false,"php_converted":12654},
{"php_type":"integer","net_type":"System.UInt32","error":false,"php_converted":1276654},
{"php_type":"object","net_type":"System.UInt64","error":false,"php_converted":1.2758686845646e+17},
{"php_type":"NULL","net_type":"System.Collections.Generic.Dictionary`2[[System.String, mscorlib, Version=4.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089],[System.String, mscorlib, Version=4.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089]]","error":true,"php_converted":{"a":"1","b":"2","c":"3"}},
{"php_type":"NULL","net_type":"System.Collections.Generic.List`1[[System.String, mscorlib, Version=4.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089]]","error":true,"php_converted":["a","b","c","d","e","f","g"]},
{"php_type":"object","net_type":"System.Byte[]","error":false,"php_converted":[69,0,115,0,116,0,111,0,115,0,32,0,115,0,111,0,110,0,32,0,108,0,111,0,115,0,32,0,98,0,121,0,116,0,101,0,115,0,32,0,100,0,101,0,32,0,117,0,110,0,32,0,116,0,101,0,120,0,116,0,46,0]}
]
EOT;

    $results = json_decode($encoded_results);

    for ($x = 0; $x < $count; $x++) {
      $type = $types[$x]->Val();
      $sample = NULL;
      $php_converted = NULL;
      $error = FALSE;

      // Some times won't even conver to 'variant' or 'com'
      // PHP will simply choke.
      try {
        // Sometimes getting a direct value from .Net won't work
        // but bridging over JSON gives some nices results!
        $php_converted = $samples[$x]->GetPhpFromJson();
        // Try to directly get the .Net value this might throw
        // exceptions due to COM not able to convert types, even to variant!
        $sample = $samples[$x]->Val();
      }
      catch (\Exception $e) {
        $error = TRUE;
      }

      $mappings[] = array('php_type' => gettype($sample) , 'net_type' => $type, 'error' => $error, 'php_converted' => $php_converted);
    }

    $this->assertEquals(json_encode($results), json_encode($mappings));

  }

  /**
   * Test the usage of ArrayList
   */
  public function testArrayList() {

    // Set loading type.
    \NetPhp\Core\Configuration::GetConfiguration()->setLoadMode("COM");

    // Register .Net framework assemblies.
    \NetPhp\Core\Configuration::RegisterTypes(\NetPhp\ms\TypeMap::GetTypes());

    // See what are the 4 php native types being convert to on the .Net side
    $arrayList = \NetPhp\ms\System\Collections\ArrayList::ArrayList_Constructor();

    $arrayList->Add(Typer::cBoolean(TRUE));
    $arrayList->Add(Typer::cInt32(52));
    $arrayList->Add(Typer::cDouble(45454));

    $arrayList = $arrayList->AsIterator();

    $this->assertTrue($arrayList[0]->Val());
    $this->assertEquals($arrayList[1]->Val(), 52);
    $this->assertEquals($arrayList[2]->Val(), 45454);
  }

  public function testDumper() {

    // Set loading type.
    \NetPhp\Core\Configuration::GetConfiguration()->setLoadMode("COM");

    // Tell the runtime What assemblies we are going to be using.
    $manager = \NetPhp\Core\NetManager::GetInstance();

    // Make sure that we bring in at least the main assembly for the .Net framework. This assembly
    // contains many native types.
    $manager->RegisterAssembly("mscorlib, Version=2.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089", "mscorlib");

    // Now let's bring in an external assembly, such as AjaxMin.
    $manager->RegisterAssembly("D:\Repositories\netutilities\Tests\resources\XFinium.PDF.dll", "XFinium");

    // We could actually use these assemblies on the fly, but it is easier and more robust if we generate a
    // static class model.
    //\NetPhp\ms\System\Collections\IDictionary_trait

  }

}