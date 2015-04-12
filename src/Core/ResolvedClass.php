<?php

namespace NetPhp\Core;

/**
 * We want to give the user as many options as possible
 * to specifiy the origin of an assembly and a class.
 * Use this as a container for that info.
 *
 * TODO:// Completely revise this and it's usage, it's quite
 * an improvised mess.
 */
class ResolvedClass {
  public $assemblyName;
  public $className;
  
  public $assemblyFullQualifiedName;
  public $assemblyPath;
  
  public $classFullQualifiedName;
}
