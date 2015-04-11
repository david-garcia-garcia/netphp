<?php

namespace NetPhp\Samples;

use \NetPhp\Core\NetManager;
use \NetPhp\Core\MagicWrapper;

class XFiniumSamples {

  /**
   * Registers the assembly and some aliases.
   *
   * @param NetManager $manager 
   */
  public static function LoadAssemblyAndAliases($manager) {
    // Register the source assembly, this can be a dll of a full qualified name.
    // Make sure the DLL is compiled for .Net framework 3.5 or lower.
    $manager->RegisterAssembly('D:\\xxxx\\xfinium.dll', 'xfinium');
    // Add some shortcuts.
    $manager->RegisterClass('xfinium', 'Xfinium.Pdf.PdfFixedDocument', 'PdfFixedDocument');
    $manager->RegisterClass('xfinium', 'Xfinium.Pdf.Graphics.PdfBrush', 'PdfBrush');
    $manager->RegisterClass('xfinium', 'Xfinium.Pdf.Graphics.PdfStandardFont', 'PdfStandardFont');
    $manager->RegisterClass('xfinium', 'Xfinium.Pdf.Graphics.PdfRgbColor', 'PdfRgbColor');
    $manager->RegisterClass('xfinium', 'Xfinium.Pdf.Graphics.PdfStandardFontFace', 'PdfStandardFontFace');
    $manager->RegisterClass('xfinium', 'Xfinium.Pdf.PdfPage', 'PdfPage');
  }
  
  /**
   * Generate a simple PDF file.
   */
  public static function GeneratePDF() {
    $m = new NetManager();
    static::LoadAssemblyAndAliases($m);
    
    $document = $m->Create('xfinium', 'PdfFixedDocument')->Instantiate();
    $page = $document->Pages->Add();

    $color = $m->Create('xfinium', 'PdfRgbColor')->Instantiate();
    $color = $color->Red;
    
    $face = $m->Create('xfinium', 'PdfStandardFontFace')->Enum("Helvetica");
    $font = $m->Create('xfinium', 'PdfStandardFont')->Instantiate($face, 24);
    
    $brush = $m->Create('xfinium', 'PdfBrush')->Instantiate($color);

    $page->Graphics->DrawString('Bomba', $font, $brush, 100, 100);
    $document->Save('d:\\bomba.pdf');
  }
}
