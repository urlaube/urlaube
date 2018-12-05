<?php

  /**
    These are the static constants of the urlau.be CMS core.

    This file contains the static constants of the urlau.be CMS core. These are
    used to separate logic from content like strings.

    @package urlaube\urlaube
    @version 0.1a11
    @author  Yahe <hello@yahe.sh>
    @since   0.1a7
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  // STATIC CONSTANTS

  // define Urlaube information
  try_define("URLAUBE_NAME",        "Urlaube");
  try_define("URLAUBE_URL",         "https://urlau.be/");
  try_define("URLAUBE_VERSION",     "0.1a11");
  try_define("URLAUBE_CODENAME",    "Freizeit");
  try_define("URLAUBE_RELEASEDATE", "05.12.2018");

  // define shortcodes
  try_define("AMP", "&");                 // ampersand
  try_define("BR",  "<br>");              // HTML line break
  try_define("COL", ":");                 // colon
  try_define("DOT", ".");                 // dot
  try_define("DP",  ",");                 // decimal point
  try_define("DS",  DIRECTORY_SEPARATOR); // directory separator
  try_define("EOL", PHP_EOL);             // end of line
  try_define("EQ",  "=");                 // equal sign
  try_define("NL",  "\n");                // new line
  try_define("QM",  "?");                 // question mark
  try_define("SP",  " ");                 // space character
  try_define("US",  "/");                 // URI separator

  // define HTTP methods
  try_define("CONNECT", "CONNECT");
  try_define("DELETE",  "DELETE");
  try_define("GET",     "GET");
  try_define("HEAD",    "HEAD");
  try_define("OPTIONS", "OPTIONS");
  try_define("PATCH",   "PATCH");
  try_define("POST",    "POST");
  try_define("PUT",     "PUT");
  try_define("TRACE",   "TRACE");

  // define HTTP defaults
  try_define("HTTP_PORT",      "80");
  try_define("HTTP_PROTOCOL",  "http://");
  try_define("HTTPS_PORT",     "443");
  try_define("HTTPS_PROTOCOL", "https://");

  // define names of main configuration
  try_define("CACHE",        "cache");        // the activation status of the cache
  try_define("CACHEAGE",     "cacheage");     // the time in seconds a value shall typically be cached
  try_define("CHARSET",      "charset");      // the charset used by the system
  try_define("CONTENT",      "content");      // the content provided to plugins and themes
  try_define("CONTENTTYPE",  "contenttype");  // the default content type set by the system
  try_define("DEBUGMODE",    "debugmode");    // actives printing of warning and error messages
  try_define("HOSTNAME",     "hostname");     // the hostname as taken from the URL
  try_define("LANGUAGE",     "language");     // the language used by the system
  try_define("LOGLEVEL",     "loglevel");     // the minimum level of log entries that get printed
  try_define("LOGTARGET",    "logtarget");    // the target of log entries (either NULL for direct output or a filename)
  try_define("METADATA",     "metadata");     // the metadata provided to plugins and themes
  try_define("METHOD",       "method");       // the HTTP method used to call the system
  try_define("PAGE",         "page");         // the page number that is displayed during pagination
  try_define("PAGECOUNT",    "pagecount");    // the maximum number of pages available during pagination
  try_define("PAGESIZE",     "pagesize");     // the number of entries per page displayed during pagination
  try_define("PORT",         "port");         // the port number as taken from the URL
  try_define("PROTOCOL",     "protocol");     // the protocol as taken from the URL
  try_define("ROOTURI",      "rooturi");      // the root URI the system is reachable at
  try_define("RESPONSECODE", "responsecode"); // the default HTTP response code set by the system
  try_define("TIMEFORMAT",   "timeformat");   // the time format used for log entries
  try_define("TIMEZONE",     "timezone");     // the time zones used by the system
  try_define("THEMENAME",    "themename");    // the name of the active theme
  try_define("URI",          "uri");          // the URI as taken from the URL

  // define names of predefined interface methods
  try_define("GETCONTENT", "getContent");
  try_define("GETURI",     "getUri");
  try_define("PARSEURI",   "parseUri");

  // define core trigger events
  // AFTER_* and BEFORE_* plugins just get triggered
  try_define("AFTER_HANDLER",  "after_handler");  // is called after the handlers have finished running
  try_define("AFTER_MAIN",     "after_main");     // is called after the core has finished running
  try_define("AFTER_THEME",    "after_theme");    // is called after the theme has finished running
  try_define("BEFORE_HANDLER", "before_handler"); // is called before the handlers are run
  try_define("BEFORE_MAIN",    "before_main");    // is called before the core is run
  try_define("BEFORE_THEME",   "before_theme");   // is called before the theme is run

  // define core filter events
  // FILTER_* plugins get content and shall filter it
  try_define("FILTER_CONTENT",  "filter_content");  // SHALL be called by handlers before they call the theme
  try_define("FILTER_HANDLERS", "filter_handlers"); // is called after the handlers have been registered
  try_define("FILTER_OUTPUT",   "filter_output");   // is called before Main::run() exits
  try_define("FILTER_PAGINATE", "filter_paginate"); // is called before pagination is applied in paginate()
  try_define("FILTER_PLUGINS",  "filter_plugins");  // is called after the plugins have been registered
  try_define("FILTER_THEMES",   "filter_themes");   // is called after the themes have been registered
  try_define("FILTER_WIDGETS",  "filter_widgets");  // is called after ON_WIDGETS plugins in callwidgets()

  // define core content events
  // ON_* plugins shall return content
  try_define("ON_CONTENT", "on_content"); // try to read content from content plugin
  try_define("ON_WIDGETS", "on_widgets"); // try to read content from widgets plugins

  // define cache events
  try_define("GET_CACHE", "get_cache"); // try to get content from the cache
  try_define("SET_CACHE", "set_cache"); // try to set content in the cache
