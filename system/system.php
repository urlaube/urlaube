<?php

  /**
    These are the system functions of the urlau.be CMS core.

    This file contains the system functions of the urlau.be CMS core. Handler, plugin and them developers shall not
    rely on these functions as they may change without prior notice.

    @package urlaube\urlaube
    @version 0.1a2
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  // turn on all error reporting
  function _activateDebugMode() {
    error_reporting(E_ALL | E_STRICT | E_NOTICE);

    ini_set("display_errors",         1);
    ini_set("display_startup_errors", 1);
    ini_set("html_errors",            1);
    ini_set("track_errors",           1);
  }

  // turn off all error reporting
  function _deactivateDebugMode() {
    error_reporting(0);

    ini_set("display_errors",         0);
    ini_set("display_startup_errors", 0);
    ini_set("html_errors",            0);
    ini_set("track_errors",           0);
  }

  // get name of calling function or method
  function _getCallerName($caller = 1) {
    $result = null;

    $backtrace = debug_backtrace();

    // extract the caller from the debug backtrace
    if ((is_array($backtrace)) &&
        (0 <= $caller) && (count($backtrace) > $caller)) {
      // retrieve the class name and action type
      if ((isset($backtrace[$caller]["class"])) &&
          (isset($backtrace[$caller]["type"]))) {
        $result .= $backtrace[$caller]["class"].$backtrace[$caller]["type"];
      }

      // retrieve the function or method name
      if (isset($backtrace[$caller]["function"])) {
        $result .= $backtrace[$caller]["function"]."()";
      } else {
        // reset $result to null if there's no function name
        $result = null;
      } 
    }

    return $result;
  }

  // return the hostname if it is set or the IP address of the server
  // if nothing is available, "localhost" is default return value
  function _getDefaultHostname() {
    $result = "localhost";

    if (isset($_SERVER["SERVER_NAME"])) {
      // take server-provided ServerName as default
      $result = $_SERVER["SERVER_NAME"];
    } else {
      if (isset($_SERVER["HTTP_HOST"])) {
        // take provided "Host:" header next
        // remove port number if it's attached to the hostname
        $result = explode(":", $_SERVER["HTTP_HOST"])[0];
      } else {
        if (isset($_SERVER["SERVER_ADDR"])) {
          // or the IP address if the "Host:" header is not present
          $result = $_SERVER["SERVER_ADDR"];
        }
      }
    }

    return $result;
  }

  // return the HTTP method
  function _getDefaultMethod() {
    $result = null;

    if (isset($_SERVER["REQUEST_METHOD"])) {
      $result = strtoupper($_SERVER["REQUEST_METHOD"]);
    }

    return $result;
  }

  // returns the port number
  function _getDefaultPort() {
    $result = null;

    if (isset($_SERVER["SERVER_PORT"])) {
      $result = $_SERVER["SERVER_PORT"];
    }

    return $result;
  }

  // return the protocol string
  function _getDefaultProtocol() {
    $result = "http://";

    if (isset($_SERVER["HTTPS"])) {
      if (0 !== strcasecmp($_SERVER["HTTPS"], "off")) {
        $result = "https://";
      }
    }

    return $result;
  }

  // return the default root URI
  function _getDefaultRootUri() {
    $result = "/";

    if (isset($_SERVER["SCRIPT_NAME"])) {
      $result = lead(trail(dirname($_SERVER["SCRIPT_NAME"]), US), US);
    }

    return $result;
  }

  // return the URI
  function _getDefaultUri() {
    $result = "/";

    if (isset($_SERVER["REQUEST_URI"])) {
      $result = lead(urldecode($_SERVER["REQUEST_URI"]), US);
    }

    return $result;
  }

  // scan all subdirs of $path and find those that contain a $file
  // include the $file of each subdir that has been found
  function _loadExtensions($path, $file) {
    $result = 0;

    // handle system handlers first
    if (is_dir($path)) {
      $path = trail($path, DS);

      // get entries in alphabetical order
      $dirs = scandir($path);
      if (false !== $dirs) {
        // find folders in $dirs that contains a handler file
        foreach ($dirs as $dirs_item) {
          if ((0 !== strcasecmp($dirs_item, ".")) &&
              (0 !== strcasecmp($dirs_item, "..")) &&
              (is_dir($path.$dirs_item)) &&
              (is_file($path.$dirs_item.DS.$file))) {               
            // include the extension file
            require_once($path.$dirs_item.DS.$file);

            $result++;
          }
        }
      }
    }

    return $result;
  }

  // log information about resource usage
  function _logResourceUsage() {
    Debug::log("Current execution time: ".(microtime(true)-START_TIME)." sec",  DEBUG_INFO);
    Debug::log("Current memory usage: ".(memory_get_usage()/1024/1024)." MB",   DEBUG_INFO);
    Debug::log("Peak memory usage: ".(memory_get_peak_usage()/1024/1024)." MB", DEBUG_INFO);
  }

  function _startBuffer() {
    $result = null;

    // initialize output buffering and store buffering level
    if (ob_start(null, 0, PHP_OUTPUT_HANDLER_STDFLAGS)) {
      $result = ob_get_level();
      if (0 === $result) {
        $result = null;
      }
    }

    return $result;
  }

  function _stopBuffer($oblevel) {
    $result = null;

    // finalize output buffering based on buffering level
    if (null !== $oblevel) {
      // flush all buffer level above our own
      while (ob_get_level() > $oblevel) {
        ob_end_flush();
      }
      if (ob_get_level() === $oblevel) {
        // get the content of our own buffer level
        $result = ob_get_clean();
        if (false === $result) {
          $result = null;
        }
      }
    }

    return $result;
  }

  function _useMultiByte($encoding) {
    $result = false;

    // use mbstring extension, if available
    if (extension_loaded(MBSTRING)) {
      // set internal encoding
      mb_internal_encoding($encoding);

      // set HTTP output encoding
      mb_http_output($encoding);

      $result = true;
    }

    return $result;
  }

