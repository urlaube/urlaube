<?php

  /**
    This is the Debug class of the urlau.be CMS core.

    This file contains the Debug class of the urlau.be CMS core. This class provides a simple logging feature.

    @package urlaube\urlaube
    @version 0.1a4
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists("Debug")) {
    class Debug extends Base {

      // FIELDS

      protected static $debugmode = DEFAULT_DEBUGMODE;

      protected static $loglevel   = DEFAULT_DEBUG_LOGLEVEL;
      protected static $logtarget  = DEFAULT_DEBUG_LOGTARGET;
      protected static $timeformat = DEFAULT_DEBUG_TIMEFORMAT;

      // GETTER FUNCTIONS

      public static function getDebugmode() {
        return static::$debugmode;
      }

      public static function getLoglevel() {
        return static::$loglevel;
      }

      public static function getLogtarget() {
        return static::$logtarget;
      }

      public static function getTimeformat() {
        return static::$timeformat;
      }

      // SETTER FUNCTIONS

      public static function setDebugmode($mode) {
        if (is_bool($mode)) {
          static::$debugmode = $mode;

          // activate/deactivate the debug mode
          if (static::$debugmode) {
            _activateDebugMode();
          } else {
            _deactivateDebugMode();
          }
        } else {
          Debug::log("given value has wrong format", DEBUG_WARN);
        }

        return ($mode === static::$debugmode);
      }

      public static function setLoglevel($level) {
        if (is_numeric($level)) {
          static::$loglevel = $level;
        } else {
          Debug::log("given value has wrong format", DEBUG_WARN);
        }

        return ($level === static::$loglevel);
      }

      public static function setLogtarget($target) {
        if ((null === $target) || (is_string($target))) {
          static::$logtarget = $target;
        } else {
          Debug::log("given value has wrong format", DEBUG_WARN);
        }

        return ($target === static::$logtarget);
      }

      public static function setTimeformat($timeformat) {
        if ((null === $timeformat) || (is_string($target))) {
          static::$timeformat = $timeformat;
        } else {
          Debug::log("given value has wrong format", DEBUG_WARN);
        }

        return ($timeformat === static::$timeformat);
      }

      // RUNTIME FUNCTIONS

      public static function log($string, $level = DEBUG_ERROR, $time = null) {
        $result = false;

        if (is_string($string) && is_numeric($level)) {
          if ((DEBUG_NONE < static::$loglevel) && ($level >= static::$loglevel)) {
            // get the name of the function or method that called us
            $callerName = _getCallerName(2);
            if (null === $callerName) {
              // set default string
              $callerName = "(script)";
            }
            // prepend the caller name to the log message
            $string = $callerName.": ".$string;

            // prepend the time
            if (null !== static::$timeformat) {
              // use the current time if $time is not set
              if (null === $time) {
                $time = time();
              }

              // $prepend the time to the log message
              $string = "[".date(static::$timeformat, $time)."] ".$string;
            }

            // make sure the log message ends with a line break
            $string = trail($string, NL);

            if (DEBUG_OUTPUT === static::$logtarget) {
              print($string);
            } else {
              file_put_contents(static::$logtarget, $string, FILE_APPEND);
            }

            $result = true;
          }
        }

        return $result;
      }

    }
  }

