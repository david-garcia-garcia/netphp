<?php

namespace NetPhp\Samples;

use \NetPhp\Core\NetManager;
use \NetPhp\Core\MagicWrapper;

class AjaxMinSample {

  /**
   * Registers the assembly and some aliases.
   *
   * @param NetManager $manager 
   */
  public static function LoadAssemblyAndAliases($manager) {
    // Register the source assembly, this can be a dll of a full qualified name.
    // Make sure the DLL is compiled for .Net framework 3.5 or lower.
    $manager->RegisterAssembly('C:\Users\David\Desktop\AjaxMinDll\bin\Debug\AjaxMin.dll', 'Ajaxmin');
    // Add some shortcuts.
    $manager->RegisterClass('Ajaxmin', 'Microsoft.Ajax.Utilities.Minifier', 'Minifier');
    $manager->RegisterClass('Ajaxmin', 'Microsoft.Ajax.Utilities.CodeSettings', 'CodeSettings');
    $manager->RegisterClass('Ajaxmin', 'Microsoft.Ajax.Utilities.OutputMode', 'OutputMode');
  }

  /**
   * Minify a javascript file to a single line.
   */
  public static function MinifyJavascript() {
    $manager = new NetManager();
    static::LoadAssemblyAndAliases($manager);

    $minifier = $manager->Create('Ajaxmin', 'Minifier')->Instantiate();
    $settings = $manager->Create('Ajaxmin', 'CodeSettings')->Instantiate();
    $settings->OutputMode = $manager->Create('Ajaxmin', 'OutputMode')->Enum('SingleLine');
    
    $script = <<<EOD
        function makearray(n){this.length=n;for(var i=1;i<=n;i++)
this[i]=0;return this;}
hexa=new makearray(16);for(var i=0;i<10;i++)
hexa[i]=i;hexa[10]="a";hexa[11]="b";hexa[12]="c";hexa[13]="d";hexa[14]="e";hexa[15]="f";function hex(i){if(i<0)
return"00";else if(i>255)
return"ff";else
return""+hexa[Math.floor(i/16)]+hexa[i%16];}
function setbgColor(r,g,b){var hr=hex(r);var hg=hex(g);var hb=hex(b);document.bgColor="#"+hr+hg+hb;}
function fade(sr,sg,sb,er,eg,eb,step){for(var i=0;i<=step;i++){setbgColor(Math.floor(sr*((step-i)/step)+er*(i/step)),Math.floor(sg*((step-i)/step)+eg*(i/step)),Math.floor(sb*((step-i)/step)+eb*(i/step)));}}
function fadein(){fade(0x00,0xff,0xff,0xff,0xff,0x00,400);}
function fadeout(){}
fadein();fadeout();
EOD;
    
    // The UnWrap will give us the underlying Native type, if this can be convertable
    // to PHP (such as a string) we will get it converted, otherwise you get
    // an unusable COM object.
    $result = $minifier->MinifyJavaScript($script, $settings);
  }
  
  /**
   * Minify Javascript with obfuscation.
   */
  public static function MinifyJavascriptObfuscate() {
    $manager = new NetManager();
    static::LoadAssemblyAndAliases($manager);

    $minifier = $manager->Create('Ajaxmin', 'Minifier')->Instantiate();
    $settings = $manager->Create('Ajaxmin', 'CodeSettings')->Instantiate();
    $settings->OutputMode = $manager->Create('Ajaxmin', 'OutputMode')->Enum('SingleLine');
    $settings->PreserveFunctionNames = FALSE;
    
    $script = <<<EOD
        function makearray(n){this.length=n;for(var i=1;i<=n;i++)
this[i]=0;return this;}
hexa=new makearray(16);for(var i=0;i<10;i++)
hexa[i]=i;hexa[10]="a";hexa[11]="b";hexa[12]="c";hexa[13]="d";hexa[14]="e";hexa[15]="f";function hex(i){if(i<0)
return"00";else if(i>255)
return"ff";else
return""+hexa[Math.floor(i/16)]+hexa[i%16];}
function setbgColor(r,g,b){var hr=hex(r);var hg=hex(g);var hb=hex(b);document.bgColor="#"+hr+hg+hb;}
function fade(sr,sg,sb,er,eg,eb,step){for(var i=0;i<=step;i++){setbgColor(Math.floor(sr*((step-i)/step)+er*(i/step)),Math.floor(sg*((step-i)/step)+eg*(i/step)),Math.floor(sb*((step-i)/step)+eb*(i/step)));}}
function fadein(){fade(0x00,0xff,0xff,0xff,0xff,0x00,400);}
function fadeout(){}
fadein();fadeout();
EOD;
    
    // The UnWrap will give us the underlying Native type, if this can be convertable
    // to PHP (such as a string) we will get it converted, otherwise you get
    // an unusable COM object.
    $result = $minifier->MinifyJavaScript($script, $settings);
  }
}
