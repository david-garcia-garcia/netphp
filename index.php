<?php

include_once ('vendor/autoload.php');

NetPhp\Tests\PHPTypes::Run();

NetPhp\Samples\AjaxMinSample::MinifyJavascript();
NetPhp\Samples\AjaxMinSample::MinifyJavascriptObfuscate();

?>