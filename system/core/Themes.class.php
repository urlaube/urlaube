<?php

  /**
    This is the Themes class of the urlau.be CMS core.

    This file contains the Themes class of the urlau.be CMS core. The user
    themes are managed through this class. It loads the themes and activates the
    selected theme when requested by the active handler.

    @package urlaube\urlaube
    @version 0.1a9
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class Themes extends BaseConfig {

    // CONSTANTS

    const ENTITY   = "entity";
    const FILENAME = "theme.php";
    const FUNCTION = "function";
    const NAME     = "name";

    // FIELDS

    protected static $active = null;
    protected static $config = null;
    protected static $themes = [];

    // GETTER FUNCTIONS

    public static function getActive() {
      return static::$active;
    }

    // RUNTIME FUNCTIONS

    public static function filter() {
      static::$themes = preparecontent(Plugins::run(FILTER_THEMES, true, static::$themes),
                                       null,
                                       [static::ENTITY, static::FUNCTION, static::NAME]);

      // make sure that we have an array
      if (!is_array(static::$themes)) {
        if (static::$themes instanceof Content) {
          static::$themes = [static::$themes];
        } else {
          static::$themes = [];
        }
      }
    }

    public static function load() {
      // load the user themes
      _loadExtensions(USER_THEMES_PATH, static::FILENAME);
    }

    public static function register($entity, $function, $name) {
      $result = false;

      if (_checkMethod($entity, $function)) {
        if (is_string($name)) {
          // check if the theme name is not already taken
          if (null === findcontent(static::$themes, static::NAME, strtolower($name))) {
            // store the given method as a new theme
            $theme = new Content();
            $theme->set(static::ENTITY,   $entity);
            $theme->set(static::FUNCTION, $function);
            $theme->set(static::NAME,     strtolower($name));
            static::$themes[] = $theme;

            // we're done
            $result = true;
          } else {
            Logging::log("given name is already registered", Logging::WARN);
          }
        } else {
          Logging::log("given name has wrong format", Logging::WARN);
        }
      } else {
        Logging::log("given entity or function does not exist", Logging::WARN);
      }

      return $result;
    }

    public static function run() {
      $result = false;

      // call the before-theme plugins
      Plugins::run(BEFORE_THEME);

      $theme = null;
      // if no theme name is set we take the first theme
      if (null === value(Main::class, THEMENAME)) {
        if (0 < count(static::$themes)) {
          $theme = static::$themes[0];
        }
      } else {
        $theme = findcontent(static::$themes, static::NAME, strtolower(value(Main::class, THEMENAME)));
      }

      // proceed with the retrieved theme item
      if (null !== $theme) {
        // store the last active theme
        $lastActive = static::$active;

        try {
          // set the active theme
          static::$active = value($theme, static::ENTITY);

          // call the theme
          $result = _callMethod(value($theme, static::ENTITY),
                                value($theme, static::FUNCTION));
        } finally {
          // restore the last active theme
          static::$active = $lastActive;
        }
      }

      // warn if no theme has been found
      if (false === $result) {
        Logging::log("no theme found matching the given name", Logging::DEBUG);
      }

      // call the after-theme plugins
      Plugins::run(AFTER_THEME);

      return $result;
    }

  }
