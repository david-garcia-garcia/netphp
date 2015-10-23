<?php

namespace NetPhp\Core;

use NetPhp\Core\MagicWrapper;


/**
 * Used to dump the PHP static class model
 * of .Net types.
 * 
 *  * COM CLASS ID FOR THE 2.X VERSION OF THE BINARY: {2D1896A6-6528-438C-9890-55778147D5BD}
 */
class TypeDumper extends ComProxy {

  /**
   * {@inheritdoc} 
   */
  public function __construct($loadMode = 'COM', $className = 'netutilities.TypeGenerator.TypeDumper', $assemblyName = NULL) {
    parent::__construct($loadMode, $className, $assemblyName);
  }

  /**
   * By default only publicly visible Types are considered during
   * the dumping process. Set to TRUE to export all types defined in the assembly
   * including internal, private and others.
   *
   * @param bool $dump
   */
  public function SetDumpInternal($dump) {
    $this->host->SetDumpInternal($dump);
  }

  /**
   * When the dumper applies user filters to limit
   * the dumped types, it will inspect properties, methods, etc.
   * to determine what other types might be useful to dump.
   *
   * This inspection is recursive and can pontentially generate
   * very big PHP class models. Use this parameter to limit the
   * inspecto recursion depth.
   *
   * Defaults to -1, that is, recurse without limit. Recommended
   * values are between 1 and 2.
   *
   * @param int $depth
   */
  public function SetDumpDepth($depth = -1) {
    $this->host->SetDumpInternal($depth);
  }

  /**
   * Add a regular expression to the list of types allowed
   * for dumping.
   *
   * @param string $regex
   */
  public function AddDumpFilter($regex) {
    $this->host->AddDumpFilter($regex);
  }

  /**
   * Set the destination path where the PHP class
   * model will be dumped to.
   *
   * @param string $path
   */
  public function SetDestination($path) {
    $this->host->SetDestination($path);
  }

  /**
   * You can dump the PHP model over an existing PHP
   * namespace (or a new one). Use this option to specify
   * the base namespace to be used for the PHP class model.
   *
   * @param string $name
   */
  public function SetBaseNamespace($name) {
    $this->host->SetBaseNamespace($name);
  }

  /**
   * Register an assembly using the .dll location.
   *
   * Make sure this location is accesible for the PHP process.
   *
   * @param string $name
   */
  public function RegisterAssemblyFromFileName($name) {
    // Just a check to make sure we are not messing up.
    if (!file_exists($name)) {
      throw new \Exception("$name not reachable.");
    }
    $this->host->RegisterAssemblyFromFileName($name);
  }

  /**
   * Register an assembly using it's full qualified Name.
   *
   * Make sure that the assembly is properly registered in the GAC
   * or deployed in such a way that is is discoverable by the neutilities.dll
   * binary.
   *
   * @param string $name
   */
  public function RegisterAssemblyFromFullQualifiedName($name) {
    $this->host->RegisterAssemblyFromFullQualifiedName($name);
  }

  /**
   * Generate the PHP classes model. This might take a while.
   */
  public function GenerateModel() {
    $this->host->GenerateModel();
  }

  /**
   * Just a security measure to prevent the type dumper
   * from clearing a wrong folder and the generate
   * a model over an existing one.
   */
  public function AllowDestinationDirectoryClear() {
    $this->host->AllowDestinationDirectoryClear();
  }
}
