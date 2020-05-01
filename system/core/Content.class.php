<?php

  /**
    This is the Content class of the urlau.be CMS core.

    Each instance of this class contains the contents of one CMS entry and is used by a theme to render an entry.

    @package urlaube/urlaube
    @version 0.2a0
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

    public function get($key) {
      $result = null;

      if (is_string($key)) {
        // $key should be trimmed lowercase
        $key = strtolower(trim($key));

        // handle empty $key like null
        if (0 >= strlen($key)) {
          $key = null;
        }
      }

      if ($this->isset($key)) {
        $result = $this->fields[$key];
      }

      return $result;
    }

    public function indices() {
      return array_keys($this->fields);
    }

    public function isset($key) {
      if (is_string($key)) {
        // $key should be trimmed lowercase
        $key = strtolower(trim($key));

        // handle empty $key like null
        if (0 >= strlen($key)) {
          $key = null;
        }
      }

      return array_key_exists($key, $this->fields);
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
      }

      return $result;
    }

    public function preset($key, $value) {
      if (!$this->isset($key)) {
        $this->set($key, $value);
      }

      return ($value === $this->get($key));
    }

    public function set($key, $value) {
      if (is_string($key)) {
        // $key should be trimmed lowercase
        $key = strtolower(trim($key));

        // handle empty $key like null
        if (0 >= strlen($key)) {
          $key = null;
        }
      }

      $this->fields[$key] = $value;

      return ($value === $this->get($key));
    }

    public function unset($key) {
      if (is_string($key)) {
        // $key should be trimmed lowercase
        $key = strtolower(trim($key));

        // handle empty $key like null
        if (0 >= strlen($key)) {
          $key = null;
        }
      }

      if ($this->isset($key)) {
        unset($this->fields[$key]);
      }

      return (!$this->isset($key));
    }

  }
