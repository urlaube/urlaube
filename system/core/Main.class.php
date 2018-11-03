<?php

  /**
    This is the Main class of the urlau.be CMS core.

    This file contains the Main class of the urlau.be CMS core. The main class
    handles the actual workflow of the urlau.be CMS.

    @package urlaube\urlaube
    @version 0.1a10
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class Main extends BaseConfig {

    // FIELDS

    protected static $config  = null;
    protected static $oblevel = null;
    protected static $running = false;

    public static function configure() {
      // these are static configuration values used to generate the output
      static::preset(CHARSET,      "UTF-8");
      static::preset(CONTENTTYPE,  "text/html");
      static::preset(DEBUGMODE,    false);
      static::preset(RESPONSECODE, "200");
      static::preset(TIMEZONE,     "Europe/Berlin");

      // these are URL parsing results
      static::preset(HOSTNAME, _getDefaultHostname());
      static::preset(METHOD,   _getDefaultMethod());
      static::preset(PORT,     _getDefaultPort());
      static::preset(PROTOCOL, _getDefaultProtocol());
      static::preset(ROOTURI,  _getDefaultRootUri());
      static::preset(URI,      _getDefaultUri());

      // these are logging configuration values (used for Logging::log())
      static::preset(LOGLEVEL,   Logging::NONE);
      static::preset(LOGTARGET,  Logging::OUTPUT);
      static::preset(TIMEFORMAT, "c");

      // this is the chosen theme (used for Themes::run())
      static::preset(THEMENAME, null);

      // this is the chosen language (used for Translate::get() and Translate::format())
      static::preset(LANGUAGE, "de_DE");

      // deactivate caching by default
      static::preset(CACHE,    false);
      static::preset(CACHEAGE, 60*60);

      // these are pagination values
      static::preset(PAGE,      1); // set by the handler
      static::preset(PAGECOUNT, 1); // set by the handler
      static::preset(PAGESIZE,  5); // use this to increase the number of entries per page

      // these are used for storage by the handler
      static::preset(CONTENT,  null); // set by the handler, contains the actual items
      static::preset(METADATA, null); // set by the handler, contains data required by the active handler
    }

    // GETTER FUNCTIONS

    public static function getOblevel() {
      return static::$oblevel;
    }

    public static function isRunning() {
      return static::$running;
    }

    // RUNTIME FUNCTIONS

    public static function run() {
      $result = null;

      // prevent multiple calls
      if (!static::$running) {
        // we're currently running
        static::$running = true;

        try {
          // load the extensions
          Handlers::load();
          Plugins::load();
          Themes::load();

          // filter the extensions
          Handlers::filter();
          Plugins::filter();
          Themes::filter();

          // call the before-main plugins
          Plugins::run(BEFORE_MAIN);

          // start buffering the generated output
          static::$oblevel = _startBuffer();
          if (null !== static::$oblevel) {
            try {
              // prepare the execution
              _setDebugMode(value(static::class, DEBUGMODE));
              _useMultiByte(value(static::class, CHARSET));
              clearstatcache(true);
              date_default_timezone_set(value(static::class, TIMEZONE));
              header("Content-Type: ".value(static::class, CONTENTTYPE));
              http_response_code(value(static::class, RESPONSECODE));

              // transfer the handling to the Handlers class
              Handlers::run();
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
