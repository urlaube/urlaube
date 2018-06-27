<?php

  /**
    This is the Content class of the urlau.be CMS core.

    This file contains the Content class of the urlau.be CMS core. Each instance of this class contains the contents
    of one CMS entry and is used by a theme to render an entry.

    @package urlaube\urlaube
    @version 0.1a5
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists("Content")) {
    class Content {

      // FIELDS

      protected $fields = array();

      // GETTER FUNCTIONS

      public function get($name) {
        $result = null;

        if ($this->isset($name)) {
          $result = $this->fields[$name];
        } else {
          Debug::log("unset value $name requested", DEBUG_INFO);
        }

        return $result;
      }

      public function isset($name) {
        return array_key_exists($name, $this->fields);
      }

      // SETTER FUNCTIONS

      public function preset($name, $value) {
        if (!$this->isset($name)) {
          $this->fields[$name] = $value;
        } else {
          Debug::log("value $name has already been set", DEBUG_DEBUG);
        }

        return ($value === $this->get($name));
      }

      public function set($name, $value) {
        $this->fields[$name] = $value;

        return ($value === $this->get($name));
      }

      public function unset($name) {
        $result = false;

        if ($this->isset($name)) {
          unset($this->fields[$name]);

          $result = true;
        }

        return $result;
      }

    }
  }

