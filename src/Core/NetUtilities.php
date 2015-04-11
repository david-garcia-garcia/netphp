<?php

namespace NetPhp\Core;

use NetPhp\Core\MagicWrapperUtilities;

class NetUtilities {
  
  // @var MagicWrapperUtilities $utils;
  private static $utils = NULL;
  
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
  
  public static function GetTypeAsString($object) {
    static::EnsureLoaded();
    $result = static::$utils->GetTypeAsString($object);
    return $result;
  }
}
