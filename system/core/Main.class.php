<?php

  /**
    This is the Main class of the urlau.be CMS core.

    This file contains the Main class of the urlau.be CMS core. The main class
    handles the actual workflow of the urlau.be CMS.

    @package urlaube/urlaube
    @version 0.2a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class Main extends BaseSingleton {

    // FIELDS

    protected static $oblevel = null;
    protected static $running = false;

    // GETTER FUNCTIONS

    public static function oblevel() {
      return static::$oblevel;
    }

    public static function running() {
      return static::$running;
    }

    // RUNTIME FUNCTIONS

    // the main configuration is put into the global configuration space
    public static function configure() {
      // these are static configuration values used to generate the output
      Config::preset(CHARSET,      "UTF-8");
      Config::preset(CONTENTTYPE,  "text/html");
      Config::preset(DEBUGMODE,    false);
      Config::preset(RESPONSECODE, "200");
      Config::preset(TIMEZONE,     "Europe/Berlin");

      // these are URL parsing results
      Config::preset(HOSTNAME, _getDefaultHostname());
      Config::preset(METHOD,   _getDefaultMethod());
      Config::preset(PORT,     _getDefaultPort());
      Config::preset(PROTOCOL, _getDefaultProtocol());
      Config::preset(ROOTURI,  _getDefaultRootUri());
      Config::preset(URI,      _getDefaultUri());

      // these are logging configuration values
      Config::preset(LOGLEVEL,   LOGGING_NONE);
      Config::preset(LOGTARGET,  null);
      Config::preset(TIMEFORMAT, "c");

      // this is the chosen theme
      Config::preset(THEMENAME, null);

      // this is the chosen language
      Config::preset(LANGUAGE, "de_DE");

      // deactivate caching by default
      Config::preset(CACHE,    CACHE_NONE);
      Config::preset(CACHEAGE, 60*60);

      // these are pagination values
      Config::preset(PAGE,      1); // set by the handler
      Config::preset(PAGECOUNT, 1); // set by the handler
      Config::preset(PAGESIZE,  5); // use this to increase the number of entries per page

      // these are used for storage by the handler
      Config::preset(CONTENT,  null); // set by the handler, contains the actual items
      Config::preset(METADATA, null); // set by the handler, contains data required by the active handler

      // these hold the successful handler and theme information
      Config::preset(HANDLER, null);
      Config::preset(THEME,   null);
    }

    public static function load() {
      // load the system and user addons
      _loadAddons(SYSTEM_ADDONS_PATH, ADDONS_FILENAME);
      _loadAddons(USER_ADDONS_PATH,   ADDONS_FILENAME);

      // filter the system and user addons
      Handlers::filter();
      Plugins::filter();
      Themes::filter();
    }

    public static function run() {
      $result = null;

      // prevent multiple calls
      if (!static::$running) {
        // we're currently running
        static::$running = true;

        try {
          // call the before-main plugins
          Plugins::run(BEFORE_MAIN);

          // start buffering the generated output
          static::$oblevel = _startBuffer();
          if (null !== static::$oblevel) {
            try {
              // prepare the execution
              _setDebugMode(Config::get(DEBUGMODE));
              _useMultiByte(Config::get(CHARSET));
              clearstatcache(true);
              date_default_timezone_set(Config::get(TIMEZONE));
              header("Content-Type: ".Config::get(CONTENTTYPE));
              http_response_code(Config::get(RESPONSECODE));

              // transfer the handling to the addons
              Handlers::run();
              Themes::run();
            } finally {
              // stop buffering, gather the generated output and filter it
              $result = Plugins::run(FILTER_OUTPUT, true, _stopBuffer(static::$oblevel));
            }
          }

          // call the after-main plugins
          Plugins::run(AFTER_MAIN);
        } finally {
          // we've finished running
          static::$running = false;
        }
      }

      return $result;
    }

  }
