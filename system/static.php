<?php

  /**
    These are the static constants of the urlau.be CMS core.

    This file contains the static constants of the urlau.be CMS core. These are
    used to separate logic from content like strings.

    @package urlaube\urlaube
    @version 0.1a9
    @author  Yahe <hello@yahe.sh>
    @since   0.1a7
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  // STATIC CONSTANTS

  // define Urlaube information
  define("URLAUBE_NAME",        "Urlaube");
  define("URLAUBE_URL",         "https://urlau.be/");
  define("URLAUBE_VERSION",     "0.1a9");
  define("URLAUBE_CODENAME",    "Freizeit");
  define("URLAUBE_RELEASEDATE", "25.10.2018");

  // define shortcodes
  define("AMP", "&");                 // ampersand
  define("BR",  "<br>");              // HTML line break
  define("COL", ":");                 // colon
  define("DOT", ".");                 // dot
  define("DP",  ",");                 // decimal point
  define("DS",  DIRECTORY_SEPARATOR); // directory separator
  define("EOL", PHP_EOL);             // end of line
  define("EQ",  "=");                 // equal sign
  define("NL",  "\n");                // new line
  define("QM",  "?");                 // question mark
  define("SP",  " ");                 // space character
  define("US",  "/");                 // URI separator

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

  // define HTTP defaults
  define("HTTP_PORT",      "80");
  define("HTTP_PROTOCOL",  "http://");
  define("HTTPS_PORT",     "443");
  define("HTTPS_PROTOCOL", "https://");

  // define names of main configuration
  define("CACHE",        "cache");        // the activation status of the cache
  define("CACHEAGE",     "cacheage");     // the time in seconds a value shall typically be cached
  define("CHARSET",      "charset");      // the charset used by the system
  define("CONTENT",      "content");      // the content provided to plugins and themes
  define("CONTENTTYPE",  "contenttype");  // the default content type set by the system
  define("DEBUGMODE",    "debugmode");    // actives printing of warning and error messages
  define("HOSTNAME",     "hostname");     // the hostname as taken from the URL
  define("LANGUAGE",     "language");     // the language used by the system
  define("LOGLEVEL",     "loglevel");     // the minimum level of log entries that get printed
  define("LOGTARGET",    "logtarget");    // the target of log entries (either NULL for direct output or a filename)
  define("METADATA",     "metadata");     // the metadata provided to plugins and themes
  define("METHOD",       "method");       // the HTTP method used to call the system
  define("PAGE",         "page");         // the page number that is displayed during pagination
  define("PAGECOUNT",    "pagecount");    // the maximum number of pages available during pagination
  define("PAGESIZE",     "pagesize");     // the number of entries per page displayed during pagination
  define("PORT",         "port");         // the port number as taken from the URL
  define("PROTOCOL",     "protocol");     // the protocol as taken from the URL
  define("ROOTURI",      "rooturi");      // the root URI the system is reachable at
  define("RESPONSECODE", "responsecode"); // the default HTTP response code set by the system
  define("TIMEFORMAT",   "timeformat");   // the time format used for log entries
  define("TIMEZONE",     "timezone");     // the time zones used by the system
  define("THEMENAME",    "themename");    // the name of the active theme
  define("URI",          "uri");          // the URI as taken from the URL

  // define names of predefined interface methods
  define("GETCONTENT", "getContent");
  define("GETURI",     "getUri");
  define("PARSEURI",   "parseUri");

  // define core trigger events
  // AFTER_* and BEFORE_* plugins just get triggered
  define("AFTER_HANDLER",  "after_handler");  // is called after the handlers have finished running
  define("AFTER_MAIN",     "after_main");     // is called after the core has finished running
  define("AFTER_THEME",    "after_theme");    // is called after the theme has finished running
  define("BEFORE_HANDLER", "before_handler"); // is called before the handlers are run
  define("BEFORE_MAIN",    "before_main");    // is called before the core is run
  define("BEFORE_THEME",   "before_theme");   // is called before the theme is run

  // define core filter events
  // FILTER_* plugins get content and shall filter it
  define("FILTER_CONTENT",  "filter_content");  // SHALL be called by handlers before they call the theme
  define("FILTER_HANDLERS", "filter_handlers"); // is called after the handlers have been registered
  define("FILTER_OUTPUT",   "filter_output");   // is called before Main::run() exits
  define("FILTER_PAGINATE", "filter_paginate"); // is called before pagination is applied in paginate()
  define("FILTER_PLUGINS",  "filter_plugins");  // is called after the plugins have been registered
  define("FILTER_THEMES",   "filter_themes");   // is called after the themes have been registered
  define("FILTER_WIDGETS",  "filter_widgets");  // is called after ON_WIDGETS plugins have been called in widgets()

  // define core content events
  // ON_* plugins shall return content
  define("ON_WIDGETS", "on_widgets"); // try to read content from widgets plugins

  // define cache events
  define("GET_CACHE", "get_cache"); // try to get content from the cache
  define("SET_CACHE", "set_cache"); // try to set content in the cache
