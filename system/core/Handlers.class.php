<?php

  /**
    This is the Handlers class of the urlau.be CMS core.

    The system and user handlers are managed through this class. It loads the handlers and activates them depending on
    the requested URI.

    @package urlaube/urlaube
    @version 0.2a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class Handlers extends BaseSingleton {

    // FIELDS

    protected static $active   = null;
    protected static $handlers = [];

    // HELPER FUNCTIONS

    protected static function cleanup(&$entity, &$member, &$regex, &$method, &$priority) {
      $result = false;

      if (_checkFunction($entity, $member)) {
        if (is_string($regex)) {
          // convert string to array
          if (is_string($method)) {
            $method = [$method];
          }

          // cleanup array
          if (is_array($method)) {
            foreach ($method as $key => $value) {
              $value = strtoupper($value);
              if (0 < strlen($value)) {
                $method[$key] = $value;
              } else {
                unset($method[$key]);
              }
            }

            // reindex and check if empty
            $method = array_values($method);
            if (0 >= count($method)) {
              $method = null;
            }
          }

          if ((null === $method) || is_array($method)) {
            if (is_numeric($priority)) {
              // all checks and cleanups have passed
              $result = true;
            } else {
              Logging::log("given priority has wrong format", Logging::WARN);
            }
          } else {
            Logging::log("given method has wrong format", Logging::WARN);
          }
        } else {
          Logging::log("given regex has wrong format", Logging::WARN);
        }
      } else {
        Logging::log("given entity or member does not exist", Logging::WARN);
      }

      return $result;
    }

    // GETTER FUNCTIONS

    public static function active() {
      return static::$active;
    }

    // RUNTIME FUNCTIONS

    public static function filter() {
      static::$handlers = preparecontent(Plugins::run(FILTER_HANDLERS, true, static::$handlers),
                                         null, [ENTITY, MEMBER, METHOD, PRIORITY, REGEX]);

      // make sure that we have an array
      if (!is_array(static::$handlers)) {
        if (static::$handlers instanceof Content) {
          static::$handlers = [static::$handlers];
        } else {
          static::$handlers = [];
        }
      }

      // cleanup the entries
      foreach (static::$handlers as $key => $value) {
        // get values from entry
        $entity   = $value->get(ENTITY);
        $member   = $value->get(MEMBER);
        $method   = $value->get(METHOD);
        $priority = $value->get(PRIORITY);
        $regex    = $value->get(REGEX);

        if (static::cleanup($entity, $member, $regex, $method, $priority)) {
          $value->set(ENTITY,   $entity);
          $value->set(MEMBER,   $member);
          $value->set(METHOD,   $method);
          $value->set(PRIORITY, $priority);
          $value->set(REGEX,    $regex);
        } else {
          unset(static::$handlers[$key]);
        }
      }
      static::$handlers = array_values(static::$handlers);
    }

    public static function register($entity, $member, $regex, $method = [GET], $priority = 0) {
      $result = false;

      if (static::cleanup($entity, $member, $regex, $method, $priority)) {
        $handler = new Content();
        $handler->set(ENTITY,   $entity);
        $handler->set(MEMBER,   $member);
        $handler->set(METHOD,   $method);
        $handler->set(PRIORITY, $priority);
        $handler->set(REGEX,    $regex);
        static::$handlers[] = $handler;

        // we're done
        $result = true;
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
              return (intval($left->get(PRIORITY))-intval($right->get(PRIORITY)));
            });

      foreach (static::$handlers as $handlers_item) {
        if (1 === preg_match($handlers_item->get(REGEX), relativeuri())) {
          if ((null === $handlers_item->get(METHOD)) ||
              in_array(Config::get(METHOD), $handlers_item->get(METHOD))) {
            // store the last active handler
            $lastActive = static::$active;

            try {
              // set the active handler
              static::$active = [ENTITY => $handlers_item->get(ENTITY),
                                 MEMBER => $handlers_item->get(MEMBER)];

              // call the handler
              $result = _callFunction(static::$active[ENTITY],
                                      static::$active[MEMBER]);

              // store the successful handler
              if ($result) {
                Config::set(HANDLER, static::$active);
              }
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
        Logging::log("no handler found matching the relative URI", Logging::WARN);
      }

      // call the after-handler plugins
      Plugins::run(AFTER_HANDLER);

      return $result;
    }

  }
