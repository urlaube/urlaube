<?php

  /**
    This is the Translations class of the urlau.be CMS core.

    This file contains the Translations class of the urlau.be CMS core. This class provides helper methods for the
    handling and management of translations.

    @package urlaube\urlaube
    @version 0.1a3
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists("Translations")) {
    class Translations extends Base {

      // FIELDS

      protected static $language = DEFAULT_LANGUAGE;

      // GETTER FUNCTIONS

      public static function getLanguage() {
        return static::$language;
      }

      public static function getTranslation($string) {
        // return the untranslated string by default
        $result = $string;

        // set the sources for potential translations
        $sources = array(Handlers::ACTIVE(),
                         PLUGINS::ACTIVE(),
                         THEMES::ACTIVE());

        // try to get a translation from each source
        foreach ($sources as $sources_item) {
          // check if we've got a translatable object
          if ($sources_item instanceof Translatable) {
            // retrieve the translation
            $result = callMethod($sources_item, GETTRANSLATION, $string);

            // exit when the string has changed
            if (0 !== strcmp($result, $string)) {
              break;
            }
          }
        }

        return $result;
      }

      // SETTER FUNCTIONS

      public static function setLanguage($language) {
        if (is_string($language)) {
          static::$language = $language;
        } else {
          Debug::log("given value has wrong format", DEBUG_WARN);
        }

        return ($language === static::$language);
      }

      // RUNTIME FUNCTIONS

      public static function loadTranslation($file) {
        $result = null;

        // try to read the file
        $content = file_get_contents($file);
        if (false !== $content) {
          // try to json-decode the read content
          $translation = json_decode($content, true);
          if (is_array($translation)) {
            $result = $translation;
          }
        }

        return $result;
      }

    }
  }

