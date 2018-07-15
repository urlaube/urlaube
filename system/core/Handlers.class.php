<?php

  /**
    This is the Handlers class of the urlau.be CMS core.

    This file contains the Handlers class of the urlau.be CMS core. The system and user handlers are managed through
    this class. It loads the handlers and activates them depending on the requested URI.

    @package urlaube\urlaube
    @version 0.1a6
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists("Handlers")) {
    class Handlers extends Base {

      // FIELDS

      protected static $config = null;

      // runtime status
      protected static $active   = null;
      protected static $handlers = null;

      // GETTER FUNCTIONS

      public static function get($name) {
        if (null === static::$config) {
          static::$config = new Content();
        }

        return static::$config->get($name);
      }

      public static function getActive() {
        return static::$active;
      }

      public static function isset($name) {
        if (null === static::$config) {
          static::$config = new Content();
        }

        return static::$config->isset($name);
      }

      // SETTER FUNCTIONS

      public static function preset($name, $value) {
        if (null === static::$config) {
          static::$config = new Content();
        }

        return static::$config->preset($name, $value);
      }

      public static function set($name, $value) {
        if (null === static::$config) {
          static::$config = new Content();
        }

        return static::$config->set($name, $value);
      }

      public static function unset($name) {
        if (null === static::$config) {
          static::$config = new Content();
        }

        return static::$config->unset($name);
      }

      // RUNTIME FUNCTIONS

      public static function load() {
        // load the system handlers
        _loadExtensions(SYSTEM_HANDLERS_PATH, HANDLER_FILE);

        // load the user handlers
        // user handlers must come last to react on system handlers
        _loadExtensions(USER_HANDLERS_PATH, HANDLER_FILE);
      }

      public static function register($entity, $function, $regex, $methods = array(GET), $priority = 0) {
        $result = false;

        if (_checkMethod($entity, $function)) {
          if (is_string($regex)) {
            if (is_array($methods) || is_string($methods)) {
              if (is_numeric($priority)) {
                // convert string to array
                if (is_string($methods)) {
                  $methods = array($methods);
                }

                // prepare handers array
                if (null === static::$handlers) {
                  static::$handlers = array();
                }

                static::$handlers[] = array(HANDLER_ENTITY   => $entity,
                                            HANDLER_FUNCTION => $function,
                                            HANDLER_METHODS  => $methods,
                                            HANDLER_PRIORITY => $priority,
                                            HANDLER_REGEX    => $regex);

                // we're done
                $result = true;
              } else {
                Debug::log("given priority has wrong format", DEBUG_WARN);
              }
            } else {
              Debug::log("given methods have wrong format", DEBUG_WARN);
            }
          } else {
            Debug::log("given regex has wrong format", DEBUG_WARN);
          }
        } else {
          Debug::log("given entity or function does not exist", DEBUG_WARN);
        }

        return $result;
      }

      // search for the first handler that matches the URI
      // call the handler's method if there's a match
      public static function run() {
        $result = false;

        // call the before-handler plugins
        Plugins::run(BEFORE_HANDLER);

        // sort handlers by priority
        usort(static::$handlers,
              function ($left, $right) {
                return ($left[HANDLER_PRIORITY]-$right[HANDLER_PRIORITY]);
              });

        foreach (static::$handlers as $handlers_item) {
          if (1 === preg_match($handlers_item[HANDLER_REGEX], Main::RELATIVEURI())) {
            if (in_array(Main::METHOD(), $handlers_item[HANDLER_METHODS])) {
              // store the last active handler
              $lastActive = static::$active;

              try {
                // set the active handler
                static::$active = $handlers_item[HANDLER_ENTITY];

                // call the handler
                $result = _callMethod($handlers_item[HANDLER_ENTITY],
                                      $handlers_item[HANDLER_FUNCTION]);
              } finally {
                // restore the last active handler
                static::$active = $lastActive;
              }

              // break if the handler said to abort the procession
              if ($result) {
                break;
              }
            }
          }
        }

        // warn if no handler has been found
        if (false === $result) {
          Debug::log("no handler found matching the relative URI", DEBUG_WARN);
        }

        // call the after-handler plugins
        Plugins::run(AFTER_HANDLER);

        return $result;
      }

    }
  }

