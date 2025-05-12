<?php

namespace Core;

/**
 * Module interface.
 */
interface ModuleInterface {

  /**
   * Register the module routes, templates, etc.
   * This method is called when the application starts.
   */
  public static function register();
  
  /**
   * Install the module (create tables, initial data, etc.)
   * This method is called when the module is being installed.
   */
  public static function install();
  
  /**
   * Uninstall the module (remove tables, data, etc.)
   * This method is called when the module is being uninstalled.
   */
  public static function uninstall();

}
