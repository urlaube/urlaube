<?php

  /**
    This is the Config class of the urlau.be CMS core.

    The main configuration as well as the addon configuration is handled through this.

    @package urlaube/urlaube
    @version 0.2a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a7
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class Config extends BaseSingleton {

    // FIELDS

    protected static $config = [];

    // GETTER FUNCTIONS

    public static function clone($name = null) {
      if (is_string($name)) {
        // $name should be trimmed lowercase
        $name = strtolower(trim($name));

        // handle empty $name like null
        if (0 >= strlen($name)) {
          $name = null;
        }
      }

      if (!array_key_exists($name, static::$config)) {
        static::$config[$name] = new Content();
      }

      return static::$config[$name]->clone();
    }

    public static function get($key, $name = null) {
      if (is_string($name)) {
        // $name should be trimmed lowercase
        $name = strtolower(trim($name));

        // handle empty $name like null
        if (0 >= strlen($name)) {
          $name = null;
        }
      }

      if (!array_key_exists($name, static::$config)) {
        static::$config[$name] = new Content();
      }

      return static::$config[$name]->get($key);
    }

    public static function indices($name = null) {
      if (is_string($name)) {
        // $name should be trimmed lowercase
        $name = strtolower(trim($name));

        // handle empty $name like null
        if (0 >= strlen($name)) {
          $name = null;
        }
      }

      if (!array_key_exists($name, static::$config)) {
        static::$config[$name] = new Content();
      }

      return static::$config[$name]->indices();
    }

    public static function isset($key, $name = null) {
      if (is_string($name)) {
        // $name should be trimmed lowercase
        $name = strtolower(trim($name));

        // handle empty $name like null
        if (0 >= strlen($name)) {
          $name = null;
        }
      }

      if (!array_key_exists($name, static::$config)) {
        static::$config[$name] = new Content();
      }

      return static::$config[$name]->isset($key);
    }

    // SETTER FUNCTIONS

    public static function merge($content, $overwrite = true, $name = null) {
      if (is_string($name)) {
        // $name should be trimmed lowercase
        $name = strtolower(trim($name));

        // handle empty $name like null
        if (0 >= strlen($name)) {
          $name = null;
        }
      }

      if (!array_key_exists($name, static::$config)) {
        static::$config[$name] = new Content();
      }

      return static::$config[$name]->merge($content, $overwrite);
    }

    public static function preset($key, $value, $name = null) {
      if (is_string($name)) {
        // $name should be trimmed lowercase
        $name = strtolower(trim($name));

        // handle empty $name like null
        if (0 >= strlen($name)) {
          $name = null;
        }
      }

      if (!array_key_exists($name, static::$config)) {
        static::$config[$name] = new Content();
      }

      return static::$config[$name]->preset($key, $value);
    }

    public static function set($key, $value, $name = null) {
      if (is_string($name)) {
        // $name should be trimmed lowercase
        $name = strtolower(trim($name));

        // handle empty $name like null
        if (0 >= strlen($name)) {
          $name = null;
        }
      }

      if (!array_key_exists($name, static::$config)) {
        static::$config[$name] = new Content();
      }

      return static::$config[$name]->set($key, $value);
    }

    public static function unset($key, $name = null) {
      if (is_string($name)) {
        // $name should be trimmed lowercase
        $name = strtolower(trim($name));

        // handle empty $name like null
        if (0 >= strlen($name)) {
          $name = null;
        }
      }

      if (!array_key_exists($name, static::$config)) {
        static::$config[$name] = new Content();
      }

      return static::$config[$name]->unset($key);
    }

  }
