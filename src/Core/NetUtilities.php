<?php

namespace NetPhp\Core;

use NetPhp\Core\MagicWrapperUtilities;


/**
 * Wrapper around the .Net MagicWrapperUtilities
 * class.
 */
class NetUtilities {
  
  // @var MagicWrapperUtilities $utils;
  //  The actual COM instance of the MagicWrapperUtilities
  //  defined inside the NetPHP binary.
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
    NetManager::UnpackParameter($object);
    $result = static::$utils->GetTypeAsString($object);
    return $result;
  }
}
