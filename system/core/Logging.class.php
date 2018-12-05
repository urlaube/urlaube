<?php

  /**
    This is the Logging class of the urlau.be CMS core.

    This file contains the Logging class of the urlau.be CMS core. This class
    provides a simple logging feature.

    @package urlaube\urlaube
    @version 0.1a11
    @author  Yahe <hello@yahe.sh>
    @since   0.1a7
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class Logging extends BaseSingleton {

    // CONSTANTS

    const NONE  = -1; // do not log
    const DEBUG =  0; // something might help when debugging
    const INFO  =  1; // something might be interesting
    const WARN  =  2; // something shouldn't be done
    const ERROR =  3; // something went wrong

    const OUTPUT = null; // write log to output

    // RUNTIME FUNCTIONS

    public static function log($string, $level = Logging::ERROR, $time = null) {
      $result = false;

      if (is_string($string) && is_numeric($level)) {
        if ((Logging::NONE < value(Main::class, LOGLEVEL)) && ($level >= value(Main::class, LOGLEVEL))) {
          // get the name of the function or method that called us
          $callerName = _getCallerName(2);
          if (null === $callerName) {
            // set default string
            $callerName = "(script)";
          }
          // prepend the caller name to the log message
          $string = $callerName.": ".$string;

          // prepend the time
          if (null !== value(Main::class, TIMEFORMAT)) {
            // use the current time if $time is not set
            if (null === $time) {
              $time = time();
            }

            // $prepend the time to the log message
            $string = "[".date(value(Main::class, TIMEFORMAT), $time)."] ".$string;
          }

          // make sure the log message ends with a line break
          $string = trail($string, NL);

          if (Logging::OUTPUT === value(Main::class, LOGTARGET)) {
            print($string);
          } else {
            file_put_contents(value(Main::class, LOGTARGET), $string, FILE_APPEND | LOCK_EX);
          }

          $result = true;
        }
      }

      return $result;
    }

  }
