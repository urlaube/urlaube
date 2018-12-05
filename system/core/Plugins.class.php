<?php

  /**
    This is the Plugins class of the urlau.be CMS core.

    This file contains the Plugins class of the urlau.be CMS core. The system
    and user plugins are managed through this class. It loads the plugins and
    activates them depending on the currently required actions.

    @package urlaube\urlaube
    @version 0.1a11
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class Plugins extends BaseConfig {

    // CONSTANTS

    const ENTITY   = "entity";
    const EVENT    = "event";
    const FILENAME = "plugin.php";
    const FUNCTION = "function";

    // FIELDS

    protected static $active  = null;
    protected static $config  = null;
    protected static $plugins = [];

    // GETTER FUNCTIONS

    public static function getActive() {
      return static::$active;
    }

    // RUNTIME FUNCTIONS

    public static function filter() {
      static::$plugins = preparecontent(Plugins::run(FILTER_PLUGINS, true, static::$plugins),
                                        null,
                                        [static::ENTITY, static::FUNCTION, static::EVENT]);

      // make sure that we have an array
      if (!is_array(static::$plugins)) {
        if (static::$plugins instanceof Content) {
          static::$plugins = [static::$plugins];
        } else {
          static::$plugins = [];
        }
      }
    }

    public static function load() {
      // load the user plugins to give them a higher priority
      _loadExtensions(USER_PLUGINS_PATH, static::FILENAME);

      // load the system plugins
      _loadExtensions(SYSTEM_PLUGINS_PATH, static::FILENAME);
    }

    public static function register($entity, $function, $event) {
      $result = false;

      if (_checkMethod($entity, $function)) {
        if (is_string($event)) {
          $plugin = new Content();
          $plugin->set(static::ENTITY,   $entity);
          $plugin->set(static::FUNCTION, $function);
          $plugin->set(static::EVENT,    $event);
          static::$plugins[] = $plugin;

          // we're done
          $result = true;
        } else {
          Logging::log("given event has wrong format", Logging::WARN);
        }
      } else {
        Logging::log("given entity or function does not exist", Logging::WARN);
      }

      return $result;
    }

    // search for all plugins that match the event
    // call the plugins' methods if there's a match
    public static function run($event, $filter = false, $value = null, $arguments = []) {
      if ($filter) {
        $result = $value;
      } else {
        $result = null;
      }

      // make sure that $arguments is an array
      if (!is_array($arguments)) {
        $arguments = [$arguments];
      }

      foreach (static::$plugins as $plugins_item) {
        if (0 === strcasecmp(value($plugins_item, static::EVENT), $event)) {
          // store the last active plugin
          $lastActive = static::$active;

          try {
            // set the active plugin
            static::$active = value($plugins_item, static::ENTITY);

            // call the plugin
            if ($filter) {
              // if this is a filter call then reiterate the $result
              $result = _callMethod(value($plugins_item, static::ENTITY),
                                    value($plugins_item, static::FUNCTION),
                                    array_merge([$result], $arguments));
            } else {
              // preset the result when at least one plugin is called
              if (null === $result) {
                $result = [];
              }

              // if this isn't a filter call then collect the return values
              $temp = _callMethod(value($plugins_item, static::ENTITY),
                                  value($plugins_item, static::FUNCTION),
                                  $arguments);

              // check if an array has been returned that has to be flattened
              if (is_array($temp)) {
                // flatten the array
                foreach ($temp as $temp_item) {
                  $result[] = $temp_item;
                }
              } else {
                $result[] = $temp;
              }
            }
          } finally {
            // restore the last active plugin
            static::$active = $lastActive;
          }
        }
      }

      return $result;
    }

  }
