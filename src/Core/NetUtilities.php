<?php

namespace NetPhp\Core;

use NetPhp\Core\MagicWrapperUtilities;


/**
 * Wrapper around the .Net MagicWrapperUtilities
 * class.
 */
class NetUtilities {
  
  /**
   * The actual COM instance of the MagicWrapperUtilities
   *  defined inside the NetPHP binary.
   *  
   * @var mixed
   */
  private static $utils = NULL;
  
  /**
   * Make sure that the internal instance to the
   * MagicWrapperUtilities is loaded.
   *
   * @return void
   */
  private static function EnsureLoaded() {
    if (static::$utils !== NULL) {
      return;
    }
    
    try {
      static::$utils = MagicWrapperUtilities::GetInstance();
    }
    catch(\Exception $e) {
      COMExceptionManager::Manage($e);
    }
  }
  
  /**
   * Returns the .Net type of the passed object.
   *
   * @param mixed $object 
   * @return string
   */
  public static function GetTypeAsString($object) {
    static::EnsureLoaded();
    NetProxyUtils::UnpackParameter($object);
    $result = static::$utils->GetTypeAsString($object);
    return $result;
  }
  
  /**
   * Summary of GetStringVersion
   * 
   * @return mixed
   */
  public static function GetStringVersion() {
    static::EnsureLoaded();
    return static::$utils->GetStringVersion();
  }
}
