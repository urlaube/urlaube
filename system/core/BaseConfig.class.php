<?php

  /**
    This is the BaseConfig class of the urlau.be CMS core.

    This file contains the BaseConfig class of the urlau.be CMS core. Some of
    the core classes contain a list of configuration values. This class provides
    a generalized interface that is consistent throughout all of these classes.

    @package urlaube\urlaube
    @version 0.1a10
    @author  Yahe <hello@yahe.sh>
    @since   0.1a7
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  abstract class BaseConfig extends BaseSingleton {

    // HELPER FUNCTIONS

    protected static function init($reset = false) {
      if ((null === static::$config) || $reset) {
        static::$config = new Content();
      }

      return (null !== static::$config);
    }

    // GETTER FUNCTIONS

    public static function clone() {
      static::init();

      return static::$config->clone();
    }

    public static function get($name) {
      static::init();

      return static::$config->get($name);
    }

    public static function indices() {
      static::init();

      return static::$config->indices();
    }

    public static function isset($name) {
      static::init();

      return static::$config->isset($name);
    }

    // SETTER FUNCTIONS

    public static function merge($content, $overwrite = true) {
      static::init();

      return static::$config->merge($content, $overwrite);
    }

    public static function preset($name, $value) {
      static::init();

      return static::$config->preset($name, $value);
    }

    public static function set($name, $value) {
      static::init();

      return static::$config->set($name, $value);
    }

    public static function unset($name) {
      static::init();

      return static::$config->unset($name);
    }

  }
