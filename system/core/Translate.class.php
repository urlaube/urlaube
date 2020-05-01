<?php

  /**
    This is the Translate class of the urlau.be CMS core.

    This class provides a translation feature based on JSON translation files.

    @package urlaube/urlaube
    @version 0.2a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a4
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class Translate extends BaseSingleton {

    // FIELDS

    protected static $translations = [];

    // HELPER FUNCTIONS

    protected static function load($name = null) {
      $result = false;

      $language = Config::get(LANGUAGE);
      if (is_string($language)) {
        // $language is trimmed lowercase
        $language = strtolower(trim($language));
      }

      if (is_string($name)) {
        // $name should be trimmed lowercase
        $name = strtolower(trim($name));

        // handle empty $name like null
        if (0 >= strlen($name)) {
          $name = null;
        }
      }

      // check if the named translation is registered
      if (array_key_exists($name, static::$translations)) {
        if (array_key_exists(null, static::$translations[$name])) {
          // check if the translation has already been loaded
          $result = array_key_exists($language, static::$translations[$name]);

          // only proceed if the translation has not been loaded
          if (!$result) {
            // iterate through the folder entry
            $translation = [];
            foreach (static::$translations[$name][null] as $translations_item) {
              if (is_file($translations_item.$language)) {
                // try to read the file
                $content = file_get_contents($translations_item.$language);
                if (false !== $content) {
                  // try to json-decode the read content
                  $temp = json_decode($content, true);
                  if (is_array($temp)) {
                    // merge the translation entries
                    $translation = array_merge($translation, $temp);
                  }
                }
              }
            }

            // set the translation
            if (0 < count($translation)) {
              static::$translations[$name][$language] = $translation;
            } else {
              static::$translations[$name][$language] = null;
            }

            $result = true;
          }
        }
      }

      return $result;
    }

    // RUNTIME FUNCTIONS

    public static function format($string, $name = null, ...$values) {
      $result = $string;

      $language = Config::get(LANGUAGE);
      if (is_string($language)) {
        // $language is trimmed lowercase
        $language = strtolower(trim($language));
      }

      if (is_string($name)) {
        // $name should be trimmed lowercase
        $name = strtolower(trim($name));

        // handle empty $name like null
        if (0 >= strlen($name)) {
          $name = null;
        }
      }

      if (static::load($name)) {
        // check if the translation is loaded
        if (is_array(static::$translations[$name][$language])) {
          // check if there is a translation
          if (array_key_exists($string, static::$translations[$name][$language])) {
            $result = vsprintf(static::$translations[$name][$language][$string], $values);
          }
        }
      }

      return $result;
    }

    public static function get($string, $name = null) {
      $result = $string;

      $language = Config::get(LANGUAGE);
      if (is_string($language)) {
        // $language is trimmed lowercase
        $language = strtolower(trim($language));
      }

      if (is_string($name)) {
        // $name should be trimmed lowercase
        $name = strtolower(trim($name));

        // handle empty $name like null
        if (0 >= strlen($name)) {
          $name = null;
        }
      }

      if (static::load($name)) {
        // check if the translation is loaded
        if (is_array(static::$translations[$name][$language])) {
          // check if there is a translation
          if (array_key_exists($string, static::$translations[$name][$language])) {
            $result = static::$translations[$name][$language][$string];
          }
        }
      }

      return $result;
    }

    public static function register($folder, $name = null) {
      $result = false;

      if (is_string($name)) {
        // $name should be trimmed lowercase
        $name = strtolower(trim($name));

        // handle empty $name like null
        if (0 >= strlen($name)) {
          $name = null;
        }
      }

      if (is_dir($folder)) {
        // create the named translation
        if (!array_key_exists($name, static::$translations)) {
          static::$translations[$name] = [];
        }

        // create the folder entry
        if (!array_key_exists(null, static::$translations[$name])) {
          static::$translations[$name][null] = [];
        }

        // set the folder
        static::$translations[$name][null][] = trail($folder, DS);

        $result = true;
      }

      return $result;
    }

  }
