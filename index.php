<?php

include_once ('vendor/autoload.php');

$test = new \DOTNET('ClassLibrary1, Version=1.0.0.0, Culture=neutral, PublicKeyToken=null', 'ClassLibrary1.Class1');

// Try loading a .Net4  framework library
$v20 = new \COM('ClassLibrary7.Class1');
print $v20->Hello();
print "</BR>";

// Try loading a .Net4  framework library
$v40 = new \COM('ClassLibrary8.Class1');
print $v40->Hello();
print "</BR>";

NetPhp\Tests\PHPTypes::Run();

NetPhp\Samples\AjaxMinSample::MinifyJavascript();
NetPhp\Samples\AjaxMinSample::MinifyJavascriptObfuscate();

?>