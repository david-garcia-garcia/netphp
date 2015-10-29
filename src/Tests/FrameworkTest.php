<?php

namespace NetPhp\Tests;

use NetPhp\ms\Typer;
use NetPhp\ms\System\netString;
use NetPhp\Core\Utilities;

/**
 * Testea la navegación principal, login y main page.
 */
class FrameworkTest extends \PHPUnit_Framework_TestCase {

  protected function GetABCPdfLocation() {
    return 'D:\REPOSITORIOS_SABENTIS\drupal7\sites\all\modules\sabentis\fdf\net\bin\ABCpdf.dll';
  }

  /**
   * Get a runtime to be used during tests.
   *
   * @return \NetPhp\Core\NetPhpRuntime
   */
  protected function GetTestRuntime() {

    // Generate a runtime.
    $runtime = new \NetPhp\Core\NetPhpRuntime('COM', '{2BF990C8-2680-474D-BDB4-65EEAEE0015F}');

    // Initialize the runtime.
    $runtime->Initialize();

    // Register the .Net framework 2 (2 through 3.5)
    $runtime->RegisterNetFramework2();
    $runtime->RegisterAssemblyFromFile($this->GetABCPdfLocation(), "ABCpdf");

    return $runtime;
  }

  /**
   * Test licensing.
   */
  public function testLicense() {

    // Of course this key is only valid in the machine where it was generated.
    $good_key = 'ewAiAEMASABFAEMASwBTAFUATQAiADoAIgAkADIAYQAkADAANAAkAGQASQBxAFgAagBCADEAWQBwAFEAUgBvADUAWABTAGoAaQBPAFcAWQAyAHUASwBjADUARgByAHgAYwBoAHQAMQBrAGoAMABUAHkASwB2AEcAYgBkAEIAagBGAFEARwBDAHMAbwBKAGIAeQAiACwAIgBHAFUASQBEACIAOgB7ACIAYwBwAHUASQBkACIAOgAiAEIARgBFAEIARgBCAEYARgAwADAAMAAzADAANgBDADMAIgAsACIAVwBpAG4AMwAyAF8AQgBJAE8AUwBfAE0AYQBuAHUAZgBhAGMAdAB1AHIAZQByACIAOgAiAEwARQBOAE8AVgBPACIALAAiAFcAaQBuADMAMgBfAEIASQBPAFMAXwBTAE0AQgBJAE8AUwBCAEkATwBTAFYAZQByAHMAaQBvAG4AIgA6ACIASgA5AEUAVAA5ADkAVwBXACAAKAAyAC4AMQA5ACAAKQAiACwAIgBXAGkAbgAzADIAXwBCAEkATwBTAF8ASQBkAGUAbgB0AGkAZgBpAGMAYQB0AGkAbwBuAEMAbwBkAGUAIgA6ACIAIgAsACIAVwBpAG4AMwAyAF8AQgBJAE8AUwBfAFMAZQByAGkAYQBsAE4AdQBtAGIAZQByACIAOgAiAFAARgAwADQAUwBKAFMARQAiACwAIgBXAGkAbgAzADIAXwBCAEkATwBTAF8AUgBlAGwAZQBhAHMAZQBEAGEAdABlACIAOgAiADIAMAAxADUAMAA1ADAANQAwADAAMAAwADAAMAAuADAAMAAwADAAMAAwACsAMAAwADAAIgAsACIAVwBpAG4AMwAyAF8AQgBJAE8AUwBfAFYAZQByAHMAaQBvAG4AIgA6ACIATABFAE4ATwBWAE8AIAAtACAAMgAxADkAMAAiACwAIgBXAGkAbgAzADIAXwBEAGkAcwBrAEQAcgBpAHYAZQBfAE0AbwBkAGUAbAAiADoAIgBUAFMAMgA1ADYARwBNAFQAUwA0ADAAMAAiACwAIgBXAGkAbgAzADIAXwBEAGkAcwBrAEQAcgBpAHYAZQBfAE0AYQBuAHUAZgBhAGMAdAB1AHIAZQByACIAOgAiACgAUwB0AGEAbgBkAGEAcgBkACAAZABpAHMAawAgAGQAcgBpAHYAZQBzACkAIgAsACIAVwBpAG4AMwAyAF8ARABpAHMAawBEAHIAaQB2AGUAXwBTAGkAZwBuAGEAdAB1AHIAZQAiADoAIgAxADAAOQA2ADAANQA2ADIANQAwACIALAAiAFcAaQBuADMAMgBfAEQAaQBzAGsARAByAGkAdgBlAF8AVABvAHQAYQBsAEgAZQBhAGQAcwAiADoAIgAyADUANQAiACwAIgBXAGkAbgAzADIAXwBCAGEAcwBlAEIAbwBhAHIAZABfAE0AbwBkAGUAbAAiADoAIgAiACwAIgBXAGkAbgAzADIAXwBCAGEAcwBlAEIAbwBhAHIAZABfAE0AYQBuAHUAZgBhAGMAdAB1AHIAZQByACIAOgAiAEwARQBOAE8AVgBPACIALAAiAFcAaQBuADMAMgBfAEIAYQBzAGUAQgBvAGEAcgBkAF8ATgBhAG0AZQAiADoAIgBCAGEAcwBlACAAQgBvAGEAcgBkACIALAAiAFcAaQBuADMAMgBfAEIAYQBzAGUAQgBvAGEAcgBkAF8AUwBlAHIAaQBhAGwATgB1AG0AYgBlAHIAIgA6ACIAMQBaAFMAVQBCADQAQQBOADEANABEACIALAAiAFcAaQBuADMAMgBfAFYAaQBkAGUAbwBDAG8AbgB0AHIAbwBsAGwAZQByAF8ARAByAGkAdgBlAHIAVgBlAHIAcwBpAG8AbgAiADoAIgAxADAALgAxADgALgAxADQALgA0ADIANgA0ACIALAAiAFcAaQBuADMAMgBfAFYAaQBkAGUAbwBDAG8AbgB0AHIAbwBsAGwAZQByAF8ATgBhAG0AZQAiADoAIgBJAG4AdABlAGwAKABSACkAIABIAEQAIABHAHIAYQBwAGgAaQBjAHMAIAA0ADYAMAAwACIALAAiAFcAaQBuADMAMgBfAE4AZQB0AHcAbwByAGsAQQBkAGEAcAB0AGUAcgBDAG8AbgBmAGkAZwB1AHIAYQB0AGkAbwBuACIAOgAiADYAOAA6AEYANwA6ADIAOAA6ADEAMwA6AEMAMAA6AEMANwAiAH0AfQA=';

    $path = sys_get_temp_dir() . '\\netphplicense.txt';

    $runtime = $this->GetTestRuntime();
    $runtime->ActivationLicenseInitialize($path, TRUE);

    $sample_key = $runtime->ActivationKeyGetSample();
    $runtime->ActivationSetKey($sample_key);

    $this->assertFalse($runtime->ActivationValid());

    // Make sure we throw an exception, this is aggresive mode.
    $message = '';
    try {
      $runtime->RegisterAssemblyFromFile(__FILE__, "alias");
    }
    catch (\Exception $e) {
      $message = $e->getMessage();
    }

    $this->AssertTrue(stripos($message, 'Aggresive mode') !== FALSE);

    $runtime = new \NetPhp\Core\NetPhpRuntime();
    $runtime->Initialize();
    $runtime->ActivationLicenseInitialize($path, TRUE);
    $runtime->ActivationSetKey($good_key);
    echo $runtime->ActivationValid();

    $runtime = new \NetPhp\Core\NetPhpRuntime();
    $runtime->Initialize();
    $runtime->ActivationLicenseInitialize($path, TRUE);
    echo $runtime->ActivationGetCode();

    $runtime->ActivationSetKey($sample_key);
    $this->AssertFalse($runtime->ActivationValid());

    $runtime->ActivationSetKey($good_key);
    $this->AssertTrue($runtime->ActivationValid());

    $runtime->ActivationClearCaches();
    $this->AssertTrue($runtime->ActivationValid());

    $runtime->ActivationSetKey($sample_key);
    $this->AssertFalse($runtime->ActivationValid());

    $runtime->ActivationSetKey($good_key);
    $this->AssertTrue($runtime->ActivationValid());

  }

  /**
   * Dump a static model to be used during the tests.
   */
  public function testDumpModel() {

    $runtime = $this->GetTestRuntime();

    #region Design Time Type Dumping

    // You only need to run this code at design time
    // to generate the PHP class model to interact with PHP

    $dumper = new \NetPhp\Core\TypeDumper();

    $dumper->Initialize();

    $dumper->SetDestination(Utilities::GetClassLocation(\NetPhp\RootDetector::class) . '/ms');

    // Make the namespace coherent!
    $dumper->SetBaseNamespace('NetPhp\ms');

    // Re-register all assemblies into the dumper.
    $runtime->RegisterAssembliesInDumper($dumper);

    // Dumping ALL the types in the assemblies can generate extremely
    // big class models (the whole .Net framework is about 125Mb).

    // Only types that match the following regular expressions
    // will be considered during dumping. These expressions
    // are run against the type full name.
    $dumper->AddDumpFilter('^WebSupergoo\.ABCpdf8.\Doc$');
    $dumper->AddDumpFilter('^System\.Convert$');
    $dumper->AddDumpFilter('^System\.IO\.File$');
    $dumper->AddDumpFilter('^System\.Diagnostics\.Process$');

    // Allow the destination directory to be cleared.
    $dumper->AllowDestinationDirectoryClear();

    // The dumper will recursively scan for types that
    // participate in method calls, return types, etc...
    // from the base type list that results from applying
    // the filters.
    $dumper->SetDumpDepth(1);

    // Now generate the static model.
    $dumper->GenerateModel();

    #endregion
  }

  public function testBasicFrameworkTests() {

    // Generate a runtime.
    $runtime = $this->GetTestRuntime();

    // Do some on the fly work on an ArrayList.
    $arrayList = $runtime->TypeFromName("System.Collections.ArrayList")->Instantiate();

    $arrayList->Add(TRUE);
    $arrayList->Add(52);
    $arrayList->Add(45454);

    // Because we are on the fly, we need to re-cast
    // the NetProxy into a NetProxyCollection.
    $arrayList = $arrayList->AsIterator();

    $this->assertTrue($arrayList[0]->Val());
    $this->assertEquals($arrayList[1]->Val(), 52);
    $this->assertEquals($arrayList[2]->Val(), 45454);

    // Remove the first one so we only have numbers here.
    $arrayList->RemoveAt(0);

    // Let's operate a little bit on the contents.
    $sum = 0;
    $target_sum = 52 + 45454;
    foreach ($arrayList as $item) {
      $sum += $item->Val();
    }
    $this->assertEquals($sum, $target_sum);

    // Let's inspect the Type.
    $nettype = $arrayList->GetType();
    $this->assertEquals($nettype, "System.Collections.ArrayList");

    // Let's see what the NetPhpRuntime assembly.
    // This is the CLR runtime (1,2,4..) and may vary depending
    // on the NetPhp binary deployed and load method.
    $version = $runtime->GetRuntimeVersion()->ToString()->Val();
  }

  /**
   *  Test using the ABCPdf library.
   */
  public function testABCPdf() {

    // Generate a runtime.
    $runtime = $this->GetTestRuntime();

    // Very important tell our PHP class map
    // to use the runtime we have constructed!
    \NetPhp\ms\TypeMap::SetRuntime($runtime);

    $vertical = TRUE;

    $doc = \NetPhp\ms\WebSupergoo\ABCpdf8\netDoc::Doc_Constructor();

    $doc->HtmlOptions()->Engine(\NetPhp\ms\WebSupergoo\ABCpdf8\netEngineType::Gecko());
    $doc->HtmlOptions()->Media(\NetPhp\ms\WebSupergoo\ABCpdf8\netMediaType::Screen());
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
      $theID = $doc->addImageUrl($url, Typer::cBoolean(FALSE), \NetPhp\ms\System\netConvert::_ToInt32($h), Typer::cBoolean(TRUE));
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
      $this->assertEquals(TRUE, FALSE);
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
    $b64 = \NetPhp\ms\System\netConvert::_ToBase64String($bytes)->Val();
    $pdf = base64_decode($b64);

    $path = Typer::cString("d:\\caca.pdf");

    //
    \NetPhp\ms\System\IO\netFile::_WriteAllBytes($path, $bytes);

    // Open windows explorer to that directory.
    \NetPhp\ms\System\Diagnostics\netProcess::_Start(Typer::cString("explorer.exe"), netString::_Format(Typer::cString("/select,\"{0}\""), $path));
  }

  /**
   * Test Native Type management.
   */
  public function testNativeTypes() {

    // Generate a runtime.
    $runtime = $this->GetTestRuntime();

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
      array('type' => 'System.__ComObject' , 'sample' => new \stdClass()),
      );

    foreach ($mappings as $map) {
      $this->assertEquals($runtime->GetTypeAsString($map['sample']), $map['type']);
    }

    // Now let's give this a shot the other way round, ask for primitive .Net types
    // and se what we get.
    $types = $runtime->GetSampleTypes();
    $samples = $runtime->GetSamples();

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

      $p1 = (object) array('php_type' => gettype($sample) , 'net_type' => $type, 'error' => $error, 'php_converted' => $php_converted);
      $p2 = (object) $results[$x];

      if (!in_array($p1->net_type, array('System.Double', 'System.Int64', 'System.Single', 'System.UInt64'))) {
        //$this->assertEquals($p1, $p2);
      }
      else {
        //$equals = abs($p1->php_converted - $p2->php_converted) < 0.0000001;
        //$this->assertTrue($equals);
      }

    }

  }

  /**
   * Test the usage of ArrayList
   */
  public function testArrayList() {

    // Register .Net framework assemblies.
    $runtime = $this->GetTestRuntime();

    // Very important tell our PHP class map
    // to use the runtime we have constructed!
    \NetPhp\ms\TypeMap::SetRuntime($runtime);

    // See what are the 4 php native types being convert to on the .Net side
    $arrayList = \NetPhp\ms\System\Collections\netArrayList::ArrayList_Constructor();

    $arrayList->Add(Typer::cBoolean(TRUE));
    $arrayList->Add(Typer::cInt32(52));
    $arrayList->Add(Typer::cDouble(45454));

    $arrayList = $arrayList->AsIterator();

    $this->assertTrue($arrayList[0]->Val());
    $this->assertEquals($arrayList[1]->Val(), 52);
    $this->assertEquals($arrayList[2]->Val(), 45454);
  }

  public function testDumper() {



  }

}