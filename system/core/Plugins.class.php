<?php

  /**
    This is the Plugins class of the urlau.be CMS core.

    The system and user plugins are managed through this class. It loads the plugins and activates them depending on
    the currently required actions.

    @package urlaube/urlaube
    @version 0.2a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class Plugins extends BaseSingleton {

    // FIELDS

    protected static $active  = null;
    protected static $plugins = [];

    // HELPER FUNCTIONS

    protected static function cleanup(&$entity, &$member, &$event) {
      $result = false;

      if (_checkFunction($entity, $member)) {
        if (is_string($event)) {
          // event is trimmed lowercase
          $event = strtolower(trim($event));

          // do not allow empty event
          if (0 >= strlen($event)) {
            $event = null;
          }
        }

        if (is_string($event)) {
          // all checks and cleanups have passed
          $result = true;
        } else {
          Logging::log("given event has wrong format", Logging::WARN);
        }
      } else {
        Logging::log("given entity or function does not exist", Logging::WARN);
      }

      return $result;
    }


    // GETTER FUNCTIONS

    public static function active() {
      return static::$active;
    }

    // RUNTIME FUNCTIONS

    public static function filter() {
      static::$plugins = preparecontent(Plugins::run(FILTER_PLUGINS, true, static::$plugins),
                                        null, [ENTITY, MEMBER, EVENT]);

      // make sure that we have an array
      if (!is_array(static::$plugins)) {
        if (static::$plugins instanceof Content) {
          static::$plugins = [static::$plugins];
        } else {
          static::$plugins = [];
        }
      }

      // cleanup the entries
      foreach (static::$plugins as $key => $value) {
        // get values from entry
        $entity = $value->get(ENTITY);
        $event  = $value->get(EVENT);
        $member = $value->get(MEMBER);

        if (static::cleanup($entity, $member, $event)) {
          $value->set(ENTITY, $entity);
          $value->set(EVENT,  $event);
          $value->set(MEMBER, $member);
        } else {
          unset(static::$plugins[$key]);
        }
      }
      static::$plugins = array_values(static::$plugins);
    }

    public static function register($entity, $member, $event) {
      $result = false;

      if (static::cleanup($entity, $member, $event)) {
        $plugin = new Content();
        $plugin->set(ENTITY, $entity);
        $plugin->set(EVENT,  $event);
        $plugin->set(MEMBER, $member);
        static::$plugins[] = $plugin;

        // we're done
        $result = true;
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

      if (is_string($event)) {
        // event is trimmed lowercase
        $event = strtolower(trim($event));

        // do not allow empty event
        if (0 >= strlen($event)) {
          $event = null;
        }
      }

      if (is_string($event)) {
        foreach (static::$plugins as $plugins_item) {
          if (0 === strcasecmp($plugins_item->get(EVENT), $event)) {
            // store the last active plugin
            $lastActive = static::$active;

            try {
              // set the active plugin
              static::$active = [ENTITY => $plugins_item->get(ENTITY),
                                 MEMBER => $plugins_item->get(MEMBER)];


              // call the plugin
              if ($filter) {
                // if this is a filter call then reiterate the $result
                $result = _callFunction(static::$active[ENTITY],
                                        static::$active[MEMBER],
                                        array_merge([$result], $arguments));
              } else {
                // preset the result when at least one plugin is called
                if (null === $result) {
                  $result = [];
                }

                // if this isn't a filter call then collect the return values
                $temp = _callFunction(static::$active[ENTITY],
                                      static::$active[MEMBER],
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
      }

      return $result;
    }

  }
