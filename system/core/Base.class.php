<?php

  /**
    This is the Base class of the urlau.be CMS core.

    This file contains the Base class of the urlau.be CMS core. The core classes use a singleton pattern by means of
    static-only properties and methods. The Base class tries to prevent the instantiation of core classes.

    @package urlaube\urlaube
    @version 0.1a3
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists("Base")) {
    class Base {

      // CONSTRUCTORS / DESTRUCTORS

      protected function __clone() {}

      protected function __construct() {}

      protected function __destruct() {}

      // MAGIC FUNCTIONS

      public static function __callStatic($name, $arguments) {
        $result = null;

        // get current class name
        $class = get_called_class();

        if (1 === preg_match("/^[A-Z]+$/", $name)) {
          if (0 === count($arguments)) {
            $name = GETTER_PREFIX.ucfirst(strtolower($name));
          } else {
            $name = SETTER_PREFIX.ucfirst(strtolower($name));
          }

          if (checkMethod($class, $name)) {
            $result = callMethod($class, $name, ...$arguments);
          } else {
            Debug::log("call to undefined magic method $class::$name()", DEBUG_WARN);
          }
        } else {
          Debug::log("call to unknown static method $class::$name()", DEBUG_WARN);
        }

        return $result;
      }

    }
  }

