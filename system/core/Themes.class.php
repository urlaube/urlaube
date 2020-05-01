<?php

  /**
    This is the Themes class of the urlau.be CMS core.

    The user themes are managed through this class. It loads the themes and activates the selected theme when requested
    by the active handler.

    @package urlaube/urlaube
    @version 0.2a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class Themes extends BaseSingleton {

    // FIELDS

    protected static $active = null;
    protected static $themes = [];

    protected static function cleanup(&$entity, &$member, &$themename, $current = null) {
      $result = false;

      if (_checkFunction($entity, $member)) {
        if (is_string($themename)) {
          // theme name is trimmed lowercase
          $themename = strtolower(trim($themename));

          // do not allow empty theme name
          if (0 >= strlen($themename)) {
            $themename = null;
          }
        }

        if (is_string($themename)) {
          // check if the theme name is not taken by another entry
          if ($current === findcontent(static::$themes, THEMENAME, $themename)) {
            // all checks and cleanups have passed
            $result = true;
          } else {
            Logging::log("given theme name is already registered", LOGGING_WARN);
          }
        } else {
          Logging::log("given theme name has wrong format", LOGGING_WARN);
        }
      } else {
        Logging::log("given entity or function does not exist", LOGGING_WARN);
      }

      return $result;
    }

    // GETTER FUNCTIONS

    public static function active() {
      return static::$active;
    }

    // RUNTIME FUNCTIONS

    public static function filter() {
      static::$themes = preparecontent(Plugins::run(FILTER_THEMES, true, static::$themes),
                                       null, [ENTITY, MEMBER, THEMENAME]);

      // make sure that we have an array
      if (!is_array(static::$themes)) {
        if (static::$themes instanceof Content) {
          static::$themes = [static::$themes];
        } else {
          static::$themes = [];
        }
      }

      // cleanup the entries
      foreach (static::$themes as $key => $value) {
        // get values from entry
        $entity    = $value->get(ENTITY);
        $member    = $value->get(MEMBER);
        $themename = $value->get(THEMENAME);

        if (static::cleanup($entity, $member, $themename, $value)) {
          $value->set(ENTITY,    $entity);
          $value->set(MEMBER,    $member);
          $value->set(THEMENAME, $themename);
        } else {
          unset(static::$themes[$key]);
        }
      }
      static::$themes = array_values(static::$themes);
    }

    public static function register($entity, $member, $themename) {
      $result = false;

      if (static::cleanup($entity, $member, $themename, null)) {
        $theme = new Content();
        $theme->set(ENTITY,    $entity);
        $theme->set(MEMBER,    $member);
        $theme->set(THEMENAME, $themename);
        static::$themes[] = $theme;

        // we're done
        $result = true;
      }

      return $result;
    }

    public static function run() {
      $result = false;

      // call the before-theme plugins
      Plugins::run(BEFORE_THEME);

      $theme = null;
      // if no theme name is set we take the first theme
      if (null === Config::get(THEMENAME)) {
        if (0 < count(static::$themes)) {
          $theme = static::$themes[0];
        }
      } else {
        $theme = findcontent(static::$themes, THEMENAME, strtolower(trim(Config::get(THEMENAME))));
      }

      // proceed with the retrieved theme item
      if (null !== $theme) {
        // store the last active theme
        $lastActive = static::$active;

        try {
          // set the active theme
          static::$active = [ENTITY => $theme->get(ENTITY),
                             MEMBER => $theme->get(MEMBER)];

          // call the theme
          $result = _callFunction(static::$active[ENTITY],
                                  static::$active[MEMBER]);

          // store the successful theme
          if ($result) {
            Config::set(THEME, static::$active);
          }
        } finally {
          // restore the last active theme
          static::$active = $lastActive;
        }
      }

      // warn if no theme has been found
      if (false === $result) {
        Logging::log("no theme found matching the given name", LOGGING_WARN);
      }

      // call the after-theme plugins
      Plugins::run(AFTER_THEME);

      return $result;
    }

  }
