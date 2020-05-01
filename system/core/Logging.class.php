<?php

  /**
    This is the Logging class of the urlau.be CMS core.

    This class provides a simple logging feature.

    @package urlaube/urlaube
    @version 0.2a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a7
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class Logging extends BaseSingleton {

    // RUNTIME FUNCTIONS

    public static function log($string, $level = LOGGING_ERROR, $time = null) {
      $result = false;

      if (is_string($string) && is_numeric($level)) {
        if ((LOGGING_NONE < Config::get(LOGLEVEL)) && ($level >= Config::get(LOGLEVEL))) {
          // get the name of the function or method that called us
          $callerName = _getCallerName(2);
          if (null === $callerName) {
            // set default string
            $callerName = "(script)";
          }
          // prepend the caller name to the log message
          $string = $callerName.": ".$string;

          // prepend the time
          if (null !== Config::get(TIMEFORMAT)) {
            // use the current time if $time is not set
            if (null === $time) {
              $time = time();
            }

            // $prepend the time to the log message
            $string = "[".date(Config::get(TIMEFORMAT), $time)."] ".$string;
          }

          // make sure the log message ends with a line break
          $string = trail($string, NL);

          if (null === Config::get(LOGTARGET)) {
            print($string);
          } else {
            file_put_contents(Config::get(LOGTARGET), $string, FILE_APPEND | LOCK_EX);
          }

          $result = true;
        }
      }

      return $result;
    }

  }
