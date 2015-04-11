<?php

namespace NetPhp\Core;

/**
 * PHP Com Exceptions give little to no information of what
 * is really going on. We are going to wrap over those expections
 * and give some real and useful information.
 */
class COMExceptionManager {
  /**
   * Wrap over the exception if we have some additional
   * information otherwise rethrow.
   *
   * @param \Exception $e 
   * @throws \Exception 
   */
  public static function Manage(\Exception $e) {
    if (strpos($e->getMessage(), '[0x80004002]') !== FALSE) {
      throw new \Exception('Could not instantiate .Net class, make sure it is decorated with the COMVisible(true) attribute and that it is marked as public.', $e->getCode(), $e);
    }
    else {
      throw $e;
    }
  }
}
