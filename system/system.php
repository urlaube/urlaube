<?php

  /**
    These are the system functions of the urlau.be CMS core.

    This file contains the system functions of the urlau.be CMS core. Handler,
    plugin and them developers shall not rely on these functions as they may
    change without prior notice.

    @package urlaube/urlaube
    @version 0.2a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  // call the $member of $entity and return its result value
  function _callFunction($entity, $member, $arguments = []) {
    $result = false;

    // check if the method is callable
    if (_checkFunction($entity, $member)) {
      // retrieve target
      $target = $member;
      if (null !== $entity) {
        $target = [$entity, $member];
      }

      $result = call_user_func_array($target, $arguments);
    }

    return $result;
  }

  // check if $member exists in $entity or as a plain function if $entity is null
  function _checkFunction($entity, $member) {
    $result = false;

    // check if $entity is either an object or a class name
    if (is_object($entity) || (is_string($entity) && class_exists($entity))) {
      // check if $member is a member of $entity
      $result = method_exists($entity, $member);
    } else {
      // check if $entity is empty
      if (null === $entity) {
        // check if $member is a plain function
        $result = function_exists($member);
      }
    }

    return $result;
  }

  // get entity and member of calling function or method
  function _getCaller($caller = 1) {
    $result = null;

    // extract the caller from the debug backtrace
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $caller+1);
    if (is_array($backtrace) && array_key_exists($caller, $backtrace) && is_array($backtrace[$caller])) {
      if (array_key_exists("function", $backtrace[$caller])) {
        $member = $backtrace[$caller]["function"];

        $entity = null;
        if (array_key_exists("class", $backtrace[$caller])) {
          $entity = $backtrace[$caller]["class"];
        }

        $result = [ENTITY => $entity,
                   MEMBER => $member];
      }
    }

    return $result;
  }

  // get name of calling function or method
  function _getCallerName($caller = 1) {
    $result = null;

    // extract the caller from the debug backtrace
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $caller+1);
    if (is_array($backtrace) && array_key_exists($caller, $backtrace) && is_array($backtrace[$caller])) {
      if (array_key_exists("function", $backtrace[$caller])) {
        $result = $backtrace[$caller]["function"]."()";

        if (array_key_exists("class", $backtrace[$caller]) && array_key_exists("type", $backtrace[$caller])) {
          $result = $backtrace[$caller]["class"].$backtrace[$caller]["type"].$result;
        }
      }
    }

    return $result;
  }

  // return the hostname if it is set or the IP address of the server
  // if nothing is available, "localhost" is default return value
  function _getDefaultHostname() {
    $result = "localhost";

    if (array_key_exists("SERVER_NAME", $_SERVER)) {
      // take server-provided ServerName as default
      $result = $_SERVER["SERVER_NAME"];
    } else {
      if (array_key_exists("HTTP_HOST", $_SERVER)) {
        // take provided "Host:" header next
        // remove port number if it's attached to the hostname
        $result = explode(COL, $_SERVER["HTTP_HOST"])[0];
      } else {
        if (array_key_exists("SERVER_ADDR", $_SERVER)) {
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

    if (array_key_exists("REQUEST_METHOD", $_SERVER)) {
      $result = strtoupper(trim($_SERVER["REQUEST_METHOD"]));
    }

    return $result;
  }

  // returns the port number
  function _getDefaultPort() {
    $result = null;

    if (array_key_exists("SERVER_PORT", $_SERVER)) {
      $result = $_SERVER["SERVER_PORT"];
    } else {
      // try to derive the port from the protocol
      switch (strtolower(_getDefaultProtocol())) {
        case HTTP_PROTOCOL:
          $result = HTTP_PORT;
          break;

        case HTTPS_PROTOCOL:
          $result = HTTPS_PORT;
          break;
      }
    }

    return $result;
  }

  // return the protocol string
  function _getDefaultProtocol() {
    $result = HTTP_PROTOCOL;

    if (array_key_exists("HTTPS", $_SERVER)) {
      if (0 !== strcasecmp($_SERVER["HTTPS"], "off")) {
        $result = HTTPS_PROTOCOL;
      }
    }

    return $result;
  }

  // return the default root URI
  function _getDefaultRootUri() {
    $result = US;

    if (array_key_exists("SCRIPT_NAME", $_SERVER)) {
      $result = lead(trail(dirname($_SERVER["SCRIPT_NAME"]), US), US);
    }

    return $result;
  }

  // return the URI
  function _getDefaultUri() {
    $result = US;

    if (array_key_exists("REQUEST_URI", $_SERVER)) {
      $result = lead(urldecode(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH)), US);
    }

    return $result;
  }

  // scan all subdirs of $path and find those that contain a $file
  // include the $file of each subdir that has been found
  function _loadAddons($path, $file) {
    $result = 0;

    if (is_dir($path)) {
      $file = basename($file);
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
    Logging::log("Current execution time: ".(microtime(true)-$_SERVER["REQUEST_TIME_FLOAT"])." sec", LOGGING_INFO);
    Logging::log("Current memory usage: ".(memory_get_usage()/1024/1024)." MB", LOGGING_INFO);
    Logging::log("Peak memory usage: ".(memory_get_peak_usage()/1024/1024)." MB", LOGGING_INFO);
  }

  // turn on/off all error reporting
  function _setDebugMode($debug = false) {
    if ($debug) {
      error_reporting(E_ALL | E_STRICT | E_NOTICE);
    } else {
      error_reporting(0);
    }

    ini_set("display_errors",         ($debug) ? 1 : 0);
    ini_set("display_startup_errors", ($debug) ? 1 : 0);
    ini_set("html_errors",            ($debug) ? 1 : 0);
    ini_set("track_errors",           ($debug) ? 1 : 0);
  }

  // start output buffering
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

  // stop output buffering
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
    if (extension_loaded("mbstring")) {
      // set internal encoding
      mb_internal_encoding($encoding);

      // set HTTP output encoding
      mb_http_output($encoding);

      $result = true;
    }

    return $result;
  }
