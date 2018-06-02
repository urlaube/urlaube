<?php

  /**
    This is the Translate class of the urlau.be CMS core.

    This file contains the Translate class of the urlau.be CMS core. This class provides a translation feature based
    on JSON translation files.

    @package urlaube\urlaube
    @version 0.1a4
    @author  Yahe <hello@yahe.sh>
    @since   0.1a4
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists("Translate")) {
    class Translate extends Base {

      // FIELDS

      protected static $language = DEFAULT_LANGUAGE;

      // runtime status
      protected static $translations = array();

      // GETTER FUNCTIONS

      public static function getLanguage() {
        return static::$language;
      }

      // SETTER FUNCTIONS

      public static function setLanguage($language) {
        if (is_string($language) && (0 < strlen($language))) {
          static::$language = $language;
        } else {
          Debug::log("given value has wrong format", DEBUG_WARN);
        }

        return ($language === static::$language);
      }

      // HELPER FUNCTIONS

      protected static function load($name = null) {
        $result = false;

        // check if the named translation is register
        if (array_key_exists(strtolower($name), static::$translations)) {
          if (array_key_exists(null, static::$translations[strtolower($name)])) {
            // check if the translation has already been loaded
            $result = array_key_exists(strtolower(static::$language), static::$translations[strtolower($name)]);

            // only proceed if the translation has not been loaded
            if (!$result) {
              // iterate through the folder entry
              $translation = array();
              foreach (static::$translations[strtolower($name)][null] as $translations_item) {
                if (is_file($translations_item.strtolower(static::$language))) {
                  // try to read the file
                  $content = file_get_contents($translations_item.strtolower(static::$language));
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
                static::$translations[strtolower($name)][strtolower(static::$language)] = $translation;
              } else {
                static::$translations[strtolower($name)][strtolower(static::$language)] = null;
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

        if (static::load(strtolower($name))) {
          // check if the translation is loaded
          if (is_array(static::$translations[strtolower($name)][strtolower(static::$language)])) {
            // check if there is a translation
            if (array_key_exists($string, static::$translations[strtolower($name)][strtolower(static::$language)])) {
              $result = sprintf(static::$translations[strtolower($name)][strtolower(static::$language)][$result],
                                ...$values);
            }
          }
        }

        return $result;
      }

      public static function get($string, $name = null) {
        $result = $string;

        if (static::load(strtolower($name))) {
          // check if the translation is loaded
          if (is_array(static::$translations[strtolower($name)][strtolower(static::$language)])) {
            // check if there is a translation
            if (array_key_exists($string, static::$translations[strtolower($name)][strtolower(static::$language)])) {
              $result = static::$translations[strtolower($name)][strtolower(static::$language)][$result];
            }
          }
        }

        return $result;
      }

      public static function register($folder, $name = null) {
        $result = false;

        if (is_dir($folder)) {
          // create the named translation
          if (!array_key_exists(strtolower($name), static::$translations)) {
            static::$translations[strtolower($name)] = array();
          }

          // create the folder entry
          if (!array_key_exists(null, static::$translations[strtolower($name)])) {
            static::$translations[strtolower($name)][null] = array();
          }

          // set the folder
          static::$translations[strtolower($name)][null][] = trail($folder, DS);

          $result = true;
        }

        return $result;
      }

    }
  }

