<?php

  /**
    This is the Translatable class of the urlau.be CMS core.

    This file contains the Translatable class of the urlau.be CMS core. Extension classes like handlers, plugins or
    themes may provide translations. The Translatable class encapsulates certain features like autoloading of
    translation files, storing loaded translations and reading the translated strings.

    @package urlaube\urlaube
    @version 0.1a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists("Translatable")) {
    class Translatable implements Translation {

      // FIELDS

      protected $translations     = array();
      protected $translationsPath = null;

      // GETTER FUNCTIONS

      public function getTranslation($string) {
        // return the untranslated string by default
        $result = $string;

        // check if the translation for the active language has been loaded
        if (!array_key_exists(Translations::LANGUAGE(), $this->translations)) {
          // try to load the translation
          if (!$this->loadTranslation()) {
            // create an empty translation to prevent is from being loaded again
            $this->translations[Translations::LANGUAGE()] = null;
          }
        }

        // check if the translation for the requested string has been loaded
        if (null !== $this->translations[Translations::LANGUAGE()]) {
          if (array_key_exists($string, $this->translations[Translations::LANGUAGE()])) {
            // return the translated string
            $result = $this->translations[Translations::LANGUAGE()][$string];
          }
        }

        return $result;
      }

      public function getTranslationsPath() {
        return $this->translationsPath;
      }

      // SETTER FUNCTIONS

      public function setTranslationsPath($path) {
        if (is_dir($path)) {
          $path = trail($path, DS);

          $this->translationsPath = $path;
        } else {
          Debug::log("given value has wrong format", DEBUG_WARN);
        }

        return ($path === $this->translationsPath);
      }

      // RUNTIME FUNCTIONS

      protected function loadTranslation() {
        // check if the translation for the active language has been loaded
        if (!isset($this->translations[Translations::LANGUAGE()])) {
          // check if the translations path actually exists
          if (is_dir($this->translationsPath)) {
            // check if the language file actually exists
            if (is_file($this->translationsPath.Translations::LANGUAGE())) {
              // try to load the translation from the file
              $translation = Translations::loadTranslation($this->translationsPath.
                                                           Translations::LANGUAGE());
              if (null !== $translation) {
                // set the loaded translation
                $this->translations[Translations::LANGUAGE()] = $translation;
              }
            }
          }
        }

        return (isset($this->translations[Translations::LANGUAGE()]));
      }

    }
  }

