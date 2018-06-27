<?php

  /**
    This is the Main class of the urlau.be CMS core.

    This file contains the Main class of the urlau.be CMS core. The main class handles the actual workflow of the
    urlau.be CMS.

    @package urlaube\urlaube
    @version 0.1a5
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists("Main")) {
    class Main extends Base {

      // FIELDS

      // configuration
      protected static $charset    = DEFAULT_CHARSET;
      protected static $pageinfo   = DEFAULT_PAGEINFO;
      protected static $pagesize   = DEFAULT_PAGESIZE;
      protected static $sitename   = DEFAULT_SITENAME;
      protected static $siteslogan = DEFAULT_SITESLOGAN;
      protected static $rooturi    = DEFAULT_ROOTURI;
      protected static $timezone   = DEFAULT_TIMEZONE;

      // call information
      protected static $hostname = DEFAULT_HOSTNAME;
      protected static $method   = DEFAULT_METHOD;
      protected static $port     = DEFAULT_PORT;
      protected static $protocol = DEFAULT_PROTOCOL;
      protected static $uri      = DEFAULT_URI;

      // pagination information
      protected static $pagemax    = null;
      protected static $pagemin    = null;
      protected static $pagenumber = null;

      // content information
      protected static $content = null;

      // runtime status
      protected static $oblevel = null;
      protected static $running = false;

      // GETTER FUNCTIONS

      public static function getCharset() {
        return static::$charset;
      }

      public static function getContent() {
        return static::$content;
      }

      public static function getHostname() {
        return static::$hostname;
      }

      public static function getMethod() {
        return static::$method;
      }

      public static function getPageinfo() {
        return static::$pageinfo;
      }

      public static function getPagemax() {
        return static::$pagemax;
      }

      public static function getPagemin() {
        return static::$pagemin;
      }

      public static function getPagenumber() {
        return static::$pagenumber;
      }

      public static function getOblevel() {
        return static::$oblevel;
      }

      public static function getPagesize() {
        return static::$pagesize;
      }

      public static function getPort() {
        return static::$port;
      }

      public static function getProtocol() {
        return static::$protocol;
      }

      public static function getRelativeuri() {
        $result = static::$uri;

        if (0 === strpos($result, static::$rooturi)) {
          if (0 === strcmp($result, static::$rooturi)) {
            $result = US;
          } else {
            $result = lead(substr($result, strlen(static::$rooturi)), US);
          }
        }

        return $result;
      }

      public static function getRooturi() {
        return static::$rooturi;
      }

      public static function getRunning() {
        return static::$running;
      }

      public static function getSitename() {
        return static::$sitename;
      }

      public static function getSiteslogan() {
        return static::$siteslogan;
      }

      public static function getTimezone() {
        return static::$timezone;
      }

      public static function getUri() {
        return static::$uri;
      }

      // SETTER FUNCTIONS

      public static function setCharset($charset) {
        if (is_string($charset)) {
          static::$charset = $charset;

          // activate the multibyte support
          _useMultiByte(static::$charset);
        } else {
          Debug::log("given value has wrong format", DEBUG_WARN);
        }

        return ($charset === static::$charset);
      }

      public static function setContent($content) {
        // check the content type
        $proceed = ($content instanceof Content);
        if (!$proceed) {
          // check if $content is an array of Content objects
          if (is_array($content)) {
            $proceed = true;
            foreach ($content as $content_item) {
              $proceed = ($content_item instanceof Content);
              if (!$proceed) {
                break;
              }
            }
          }
        }

        if ($proceed) {
          static::$content = $content;
        } else {
          Debug::log("given value has wrong format", DEBUG_WARN);
        }

        return ($content === static::$content);
      }

      public static function setHostname($hostname) {
        if (is_string($hostname)) {
          static::$hostname = $hostname;
        } else {
          Debug::log("given value has wrong format", DEBUG_WARN);
        }

        return ($hostname === static::$hostname);
      }

      public static function setMethod($method) {
        if (is_string($method)) {
          static::$method = strtoupper($method);
        } else {
          Debug::log("given value has wrong format", DEBUG_WARN);
        }

        return ($method === static::$method);
      }

      public static function setPageinfo($pageinfo) {
        if (is_array($pageinfo)) {
          static::$pageinfo = $pageinfo;
        } else {
          Debug::log("given value has wrong format", DEBUG_WARN);
        }

        return ($pageinfo === static::$pageinfo);
      }

      public static function setPagemax($pagemax) {
        if (is_numeric($pagemax)) {
          static::$pagemax = $pagemax;
        } else {
          Debug::log("given value has wrong format", DEBUG_WARN);
        }

        return ($pagemax === static::$pagemax);
      }

      public static function setPagemin($pagemin) {
        if (is_numeric($pagemin)) {
          static::$pagemin = $pagemin;
        } else {
          Debug::log("given value has wrong format", DEBUG_WARN);
        }

        return ($pagemin === static::$pagemin);
      }

      public static function setPagenumber($pagenumber) {
        if (is_numeric($pagenumber)) {
          static::$pagenumber = $pagenumber;
        } else {
          Debug::log("given value has wrong format", DEBUG_WARN);
        }

        return ($pagenumber === static::$pagenumber);
      }

      public static function setPagesize($pagesize) {
        if (is_numeric($pagesize)) {
          static::$pagesize = $pagesize;
        } else {
          Debug::log("given value has wrong format", DEBUG_WARN);
        }

        return ($pagesize === static::$pagesize);
      }

      public static function setPort($port) {
        if (is_string($port)) {
          static::$port = $port;
        } else {
          Debug::log("given value has wrong format", DEBUG_WARN);
        }

        return ($port === static::$port);
      }

      public static function setProtocol($protocol) {
        if (is_string($protocol)) {
          static::$protocol = $protocol;
        } else {
          Debug::log("given value has wrong format", DEBUG_WARN);
        }

        return ($protocol === static::$protocol);
      }

      public static function setRooturi($uri) {
        if (is_string($uri)) {
          $uri = lead(trail($uri, US), US);

          static::$rooturi = $uri;
        } else {
          Debug::log("given value has wrong format", DEBUG_WARN);
        }

        return ($uri === static::$rooturi);
      }

      public static function setSitename($sitename) {
        if (is_string($sitename)) {
          static::$sitename = $sitename;
        } else {
          Debug::log("given value has wrong format", DEBUG_WARN);
        }

        return ($sitename === static::$sitename);
      }

      public static function setSiteslogan($siteslogan) {
        if (is_string($siteslogan)) {
          static::$siteslogan = $siteslogan;
        } else {
          Debug::log("given value has wrong format", DEBUG_WARN);
        }

        return ($siteslogan === static::$siteslogan);
      }

      public static function setTimezone($timezone) {
        if (is_string($timezone)) {
          static::$timezone = $timezone;

          // set the timezone
          date_default_timezone_set(static::$timezone);
        } else {
          Debug::log("given value has wrong format", DEBUG_WARN);
        }

        return ($timezone === static::$timezone);
      }

      public static function setUri($uri) {
        if (is_string($uri)) {
          $uri = lead($uri, US);

          static::$uri = $uri;
        } else {
          Debug::log("given value has wrong format", DEBUG_WARN);
        }

        return ($uri === static::$uri);
      }

      // RUNTIME FUNCTIONS

      public static function run() {
        $result = null;

        // prevent multiple calls
        if (!static::$running) {
          try {
            // we're currently running
            static::$running = true;

            // activate the multibyte support
            _useMultiByte(static::$charset);

            // set the timezone
            date_default_timezone_set(static::$timezone);

            // set the response code
            http_response_code(200);

            // start buffering the generated output
            static::$oblevel = _startBuffer();

            // load the extensions
            // we load Themes and Plugins first because
            // they may bring their own handlers
            Themes::load();
            Plugins::load();
            Handlers::load();

            // call the before-main plugins
            Plugins::run(BEFORE_MAIN);

            // transfer the handling to the Handlers class
            Handlers::run();

            // stop buffering and gather the generated output
            if (null !== static::$oblevel) {
              $result = _stopBuffer(static::$oblevel);

              // filter the output
              $result = Plugins::run(FILTER_OUTPUT, true, $result);
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
  }

