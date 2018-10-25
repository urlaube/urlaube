<?php

  /**
    This is the CachePlugin class of the urlau.be CMS.

    This file contains the CachePlugin class of the urlau.be CMS. This plugin
    provides a file-based cache that relies on PHP (de)serialization.

    @package urlaube\urlaube
    @version 0.1a8
    @author  Yahe <hello@yahe.sh>
    @since   0.1a8
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class CachePlugin extends BaseSingleton implements Plugin {

    // RUNTIME FUNCTIONS

    public static function get($key, &$value, $name = null) {
      // we couldn't handle this request
      $result = false;

      // prepare key
      $key = hash("sha256", $key);

      // prepare name
      if (null !== $name) {
        $name = hash("sha256", $name).DOT;
      }

      // if the file exists
      if (is_file(USER_CACHE_PATH.$name.$key)) {
        // try to read the content from file
        $content = file_get_contents(USER_CACHE_PATH.$name.$key);
        if (false !== $content) {
          // try to unserialize the content
          $content = unserialize($content);
          if (false !== $content) {
            // set the value
            $value = $content;

            // we succeeded
            $result = true;
          }
        }
      }

      return $result;
    }

    public static function set($key, $value, $name = null) {
      // prepare key
      $key = hash("sha256", $key);

      // prepare name
      if (null !== $name) {
        $name = hash("sha256", $name).DOT;
      }

      // try to write the serialized value to file
      return file_put_contents(USER_CACHE_PATH.$name.$key, serialize($value), LOCK_EX);
    }

  }

  // register plugin
  Plugins::register(CachePlugin::class, "get", GET_CACHE);
  Plugins::register(CachePlugin::class, "set", SET_CACHE);
