<?php

  /**
    This is the Content class of the urlau.be CMS core.

    This file contains the Content class of the urlau.be CMS core. Each instance
    of this class contains the contents of one CMS entry and is used by a theme
    to render an entry.

    @package urlaube\urlaube
    @version 0.1a11
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class Content implements Serializable {

    // FIELDS

    protected $fields = [];

    // INTERFACE FUNCTIONS

    public function serialize() {
      return serialize($this->fields);
    }

    public function unserialize($serialized) {
      // unserialize and check data
      $temp = unserialize($serialized);
      if (is_array($temp)) {
        foreach ($temp as $key => $value) {
          $this->set($key, $value);
        }
      }
    }

    // GETTER FUNCTIONS

    public function clone() {
      $result = new Content();

      // copy the contents
      foreach ($this->fields as $key => $value) {
        $result->set($key, $value);
      }

      return $result;
    }

    public function get($name) {
      $result = null;

      // $name should be case-insensitive
      if (is_string($name)) {
        $name = strtolower($name);
      }

      if ($this->isset($name)) {
        $result = $this->fields[$name];
      } else {
        Logging::log("unset value $name requested", Logging::WARN);
      }

      return $result;
    }

    public function indices() {
      return array_keys($this->fields);
    }

    public function isset($name) {
      // $name should be case-insensitive
      if (is_string($name)) {
        $name = strtolower($name);
      }

      return array_key_exists($name, $this->fields);
    }

    // SETTER FUNCTIONS

    public function merge($content, $overwrite = true) {
      $result = false;

      if ($content instanceof Content) {
        // the function returns the number of merged fields
        $result = 0;

        $indices = $content->indices();
        foreach ($indices as $indices_item) {
          if (($overwrite) || (!$this->isset($indices_item))) {
            $this->set($indices_item, $content->get($indices_item));

            // increment the number of merged fields
            $result++;
          }
        }
      } else {
        Logging::log("given entity is not a content object", Logging::WARN);
      }

      return $result;
    }

    public function preset($name, $value) {
      if (!$this->isset($name)) {
        $this->set($name, $value);
      } else {
        Logging::log("value $name has already been set", Logging::DEBUG);
      }

      return ($value === $this->get($name));
    }

    public function set($name, $value) {
      // $name should be case-insensitive
      if (is_string($name)) {
        $name = strtolower($name);
      }

      $this->fields[$name] = $value;

      return ($value === $this->get($name));
    }

    public function unset($name) {
      // $name should be case-insensitive
      if (is_string($name)) {
        $name = strtolower($name);
      }

      if ($this->isset($name)) {
        unset($this->fields[$name]);
      } else {
        Logging::log("value $name is already unset", Logging::DEBUG);
      }

      return (!$this->isset($name));
    }

  }
