<?php

  /**
    This is the Plugins class of the urlau.be CMS core.

    This file contains the Plugins class of the urlau.be CMS core. The system and user plugins are managed through
    this class. It loads the plugins and activates them depending on the currently required actions.

    @package urlaube\urlaube
    @version 0.1a5
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists("Plugins")) {
    class Plugins extends Base {

      // FIELDS

      protected static $config = null;

      // runtime status
      protected static $active  = null;
      protected static $plugins = null;

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
        // load the system plugins
        _loadExtensions(SYSTEM_PLUGINS_PATH, PLUGIN_FILE);

        // load the user plugins
        // user plugins must come last to react on system plugins
        _loadExtensions(USER_PLUGINS_PATH, PLUGIN_FILE);
      }

      public static function register($entity, $function, $event) {
        $result = false;

        if (checkMethod($entity, $function)) {
          if (is_string($event)) {
            // prepare plugins array
            if (null === static::$plugins) {
              static::$plugins = array();
            }

            static::$plugins[] = array(PLUGIN_ENTITY   => $entity,
                                       PLUGIN_FUNCTION => $function,
                                       PLUGIN_EVENT    => $event);

            // we're done
            $result = true;
          } else {
            Debug::log("given event has wrong format", DEBUG_WARN);
          }
        } else {
          Debug::log("given entity or function does not exist", DEBUG_WARN);
        }

        return $result;
      }

      // search for all plugins that match the event
      // call the plugins' methods if there's a match
      public static function run($event, $filter = false, $value = null, ...$arguments) {
        if ($filter) {
          $result = $value;
        } else {
          $result[] = array();
        }

        foreach (static::$plugins as $plugins_item) {
          if (0 === strcasecmp($plugins_item[PLUGIN_EVENT], $event)) {
            // set the active plugin
            static::$active = $plugins_item[PLUGIN_ENTITY];

            // call the plugin
            if ($filter) {
              // if this is a filter call then reiterate the $result
              $result = callMethod($plugins_item[PLUGIN_ENTITY],
                                   $plugins_item[PLUGIN_FUNCTION],
                                   $result, ...$arguments);
            } else {
              // if this isn't a filter call then collect the return values
              $result[] = callMethod($plugins_item[PLUGIN_ENTITY],
                                     $plugins_item[PLUGIN_FUNCTION],
                                     $value, ...$arguments);
            }

            // unset the active plugin
            static::$active = null;
          }
        }

        return $result;
      }

    }
  }

