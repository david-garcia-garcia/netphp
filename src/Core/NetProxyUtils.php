<?php

namespace NetPhp\Core;

use \NetPhp\Core\NetProxy;
use \NetPhp\Core\MagicWrapper;

/**
 * Utility methods.
 */
class NetProxyUtils {

  /**
   * Iterates a an array of objects and unpacks them as much
   * as possible.
   * 
   * From either NetProxy or MagicWrapper wrappers
   * to the COM instance, so that they can be sent
   * to .Net.
   *
   * @param array $params 
   */
  public static function UnpackParameters(array &$params) {
    foreach ($params as &$param) {
      if (method_exists($param, 'UnPack')) {
        $param = $param->UnPack();
      }
    }
  }
  
  /**
   * Unpacks a NetProxy or MagicWrapper to the COM instance.
   *
   * @param mixed $param 
   */
  public static function UnpackParameter(&$param) {
    if (method_exists($param, 'UnPack')) {
      $param = $param->UnPack();
    }
  }
}
