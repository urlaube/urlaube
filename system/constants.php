<?php

  /**
    These are the constants of the urlau.be CMS core.

    This file contains the constants of the urlau.be CMS core. These are used to separate logic from content like
    strings.

    @package urlaube\urlaube
    @version 0.1a5
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  // STATIC CONSTANTS

  // define Urlaube information
  define("URLAUBE_NAME",        "Urlaube");
  define("URLAUBE_URL",         "https://urlau.be/");
  define("URLAUBE_VERSION",     "0.1a5");
  define("URLAUBE_CODENAME",    "Freizeit");
  define("URLAUBE_RELEASEDATE", "27.06.2018");

  // define shortcodes
  define("BR",  "<br />");
  define("DS",  DIRECTORY_SEPARATOR);
  define("EOL", PHP_EOL);
  define("NL",  "\n");
  define("SP",  " ");
  define("US",  "/");

  // define HTTP methods
  define("CONNECT", "CONNECT");
  define("DELETE",  "DELETE");
  define("GET",     "GET");
  define("HEAD",    "HEAD");
  define("OPTIONS", "OPTIONS");
  define("PATCH",   "PATCH");
  define("POST",    "POST");
  define("PUT",     "PUT");
  define("TRACE",   "TRACE");

  // define file names
  define("CONFIG_FILE",  "config.php");
  define("HANDLER_FILE", "handler.php");
  define("PLUGIN_FILE",  "plugin.php");
  define("THEME_FILE",   "theme.php");

  // define debug levels
  define("DEBUG_NONE",  -1); // do not log
  define("DEBUG_DEBUG",  0); // something might help when debugging
  define("DEBUG_INFO",   1); // something might be interesting
  define("DEBUG_WARN",   2); // something shouldn't be done
  define("DEBUG_ERROR",  3); // something went wrong

  // define debug target (either null for output or a file)
  define("DEBUG_OUTPUT", null);

  // define prefixes for Base class getter/setter magic
  define("GETTER_PREFIX", "get");
  define("SETTER_PREFIX", "set");

  // define name of predefined methods
  define("GETCONTENT",     "getContent");
  define("GETTRANSLATION", "getTranslation");
  define("GETURI",         "getUri");
  define("PARSEURI",       "parseUri");

  // define name of multibyte extension
  define("MBSTRING", "mbstring");

  // define handler array attributes
  define("HANDLER_ENTITY",   "entity");
  define("HANDLER_FUNCTION", "function");
  define("HANDLER_METHODS",  "methods");
  define("HANDLER_PRIORITY", "priority");
  define("HANDLER_REGEX",    "regex");

  // define plugin array attributes
  define("PLUGIN_ENTITY",   "entity");
  define("PLUGIN_EVENT",    "event");
  define("PLUGIN_FUNCTION", "function");

  // define theme array attributes
  define("THEME_ENTITY",   "entity");
  define("THEME_FUNCTION", "function");

  // define core plugin hooks
  // AFTER_* and BEFORE_* plugins just get called
  define("AFTER_HANDLER",  "after_handler");
  define("AFTER_MAIN",     "after_main");
  define("AFTER_THEME",    "after_theme");
  define("BEFORE_HANDLER", "before_handler");
  define("BEFORE_MAIN",    "before_main");
  define("BEFORE_THEME",   "before_theme");

  // define core plugin filter hooks
  // FILTER_* plugins get content and shall filter it
  define("FILTER_CONTENT", "filter_content");
  define("FILTER_OUTPUT",  "filter_output");

  // DERIVED CONSTANTS

  // derive system paths
  $path = SYSTEM_PATH;
  if (is_dir($path)) {
    $path = lead($path, DS);
  } else {
    $path = ROOT_PATH."system".DS;
  }
  define("SYSTEM_CORE_PATH",     $path."core".DS);
  define("SYSTEM_HANDLERS_PATH", $path."handlers".DS);
  define("SYSTEM_PLUGINS_PATH",  $path."plugins".DS);

  // derive user paths
  $path = USER_PATH;
  if (is_dir($path)) {
    $path = lead($path, DS);
  } else {
    $path = ROOT_PATH."user".DS;
  }
  define("USER_CONFIG_PATH",   $path."config".DS);
  define("USER_CONTENT_PATH",  $path."content".DS);
  define("USER_HANDLERS_PATH", $path."handlers".DS);
  define("USER_PLUGINS_PATH",  $path."plugins".DS);
  define("USER_THEMES_PATH",   $path."themes".DS);

