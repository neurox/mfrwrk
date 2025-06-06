<?php

namespace Neurox\Mfrwrk\Core;

/**
 * Base module class.
 */
abstract class BaseModule implements ModuleInterface {

  /**
   * Register the module routes, templates, etc.
   *
   * This method is called when the application starts.
   */
  protected static function registerRoutes() {
    $calledClass = get_called_class();
    $moduleNamespace = substr($calledClass, 0, strrpos($calledClass, '\\'));
    $routesClass = $moduleNamespace . '\\Routes';

    // If routes class exists, register it.
    if (class_exists($routesClass)) {
      $routesClass::register();
    }
  }

}
