<?php

include_once ('vendor/autoload.php');

// We need to run tests on a fast-cgi instance to allow the Visual Studio debugger to Attach
// to the running instance.
$test = \NetPhp\Tests\FrameworkTest::testABCPdf();
?>