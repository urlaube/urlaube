<?php

  /**
    This is the Handlers class of the urlau.be CMS core.

    This file contains the Handlers class of the urlau.be CMS core. The system
    and user handlers are managed through this class. It loads the handlers and
    activates them depending on the requested URI.

    @package urlaube\urlaube
    @version 0.1a11
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class Handlers extends BaseConfig {

    // CONSTANTS

    const ENTITY   = "entity";
    const FILENAME = "handler.php";
    const FUNCTION = "function";
    const METHODS  = "methods";
    const PRIORITY = "priority";
    const REGEX    = "regex";

    // FIELDS

    protected static $active   = null;
    protected static $config   = null;
    protected static $handlers = [];

    // GETTER FUNCTIONS

    public static function getActive() {
      return static::$active;
    }

    // RUNTIME FUNCTIONS

    public static function filter() {
      static::$handlers = preparecontent(Plugins::run(FILTER_HANDLERS, true, static::$handlers),
                                         null,
                                         [static::ENTITY, static::FUNCTION, static::METHODS, static::PRIORITY,
                                          static::REGEX]);

      // make sure that we have an array
      if (!is_array(static::$handlers)) {
        if (static::$handlers instanceof Content) {
          static::$handlers = [static::$handlers];
        } else {
          static::$handlers = [];
        }
      }
    }

    public static function load() {
      // load the user handlers to give them a higher priority
      _loadExtensions(USER_HANDLERS_PATH, static::FILENAME);

      // load the system handlers
      _loadExtensions(SYSTEM_HANDLERS_PATH, static::FILENAME);
    }

    public static function register($entity, $function, $regex, $methods = [GET], $priority = 0) {
      $result = false;

      if (_checkMethod($entity, $function)) {
        if (is_string($regex)) {
          if (is_array($methods) || is_string($methods)) {
            if (is_numeric($priority)) {
              // convert string to array
              if (is_string($methods)) {
                $methods = [$methods];
              }

              $handler = new Content();
              $handler->set(static::ENTITY,   $entity);
              $handler->set(static::FUNCTION, $function);
              $handler->set(static::METHODS,  $methods);
              $handler->set(static::PRIORITY, $priority);
              $handler->set(static::REGEX,    $regex);
              static::$handlers[] = $handler;

              // we're done
              $result = true;
            } else {
              Logging::log("given priority has wrong format", Logging::WARN);
            }
          } else {
            Logging::log("given methods have wrong format", Logging::WARN);
          }
        } else {
          Logging::log("given regex has wrong format", Logging::WARN);
        }
      } else {
        Logging::log("given entity or function does not exist", Logging::WARN);
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
              return (intval(value($left, static::PRIORITY))-intval(value($right, static::PRIORITY)));
            });

      foreach (static::$handlers as $handlers_item) {
        if (1 === preg_match(value($handlers_item, static::REGEX), relativeuri())) {
          if (in_array(value(Main::class, METHOD), value($handlers_item, static::METHODS))) {
            // store the last active handler
            $lastActive = static::$active;

            try {
              // set the active handler
              static::$active = value($handlers_item, static::ENTITY);

              // call the handler
              $result = _callMethod(value($handlers_item, static::ENTITY),
                                    value($handlers_item, static::FUNCTION));
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
        Logging::log("no handler found matching the relative URI", Logging::DEBUG);
      }

      // call the after-handler plugins
      Plugins::run(AFTER_HANDLER);

      return $result;
    }

  }
