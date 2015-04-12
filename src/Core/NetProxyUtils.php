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
    for ($x = 0; $x < count($params); $x++) {
      $item = &$params[$x];
      static::UnpackParameter($item);
    }
  }
  
  /**
   * Unpacks a NetProxy or MagicWrapper to the COM instance.
   *
   * @param mixed $param 
   */
  public static function UnpackParameter(&$param) {
    if (is_a($param, NetProxy::class)) {
      $param = $param->UnPack();
    } 
    elseif (is_a($param, MagicWrapper::class)) {
      $param = $param->UnPack();
    } 
  }
}
