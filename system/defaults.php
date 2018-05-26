<?php

  /**
    These are the defaults of the urlau.be CMS core.

    This file contains the defaults of the urlau.be CMS core. These are used to pre-configure core classes.

    @package urlaube\urlaube
    @version 0.1a2
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  // STATIC DEFAULTS

  define("DEFAULT_CHARSET", "UTF-8");

  define("DEFAULT_DEBUGMODE", false);

  define("DEFAULT_DEBUG_LOGLEVEL",   DEBUG_NONE);
  define("DEFAULT_DEBUG_LOGTARGET",  DEBUG_OUTPUT);
  define("DEFAULT_DEBUG_TIMEFORMAT", "c");

  define("DEFAULT_LANGUAGE",   "de_DE");
  define("DEFAULT_PAGEINFO",   "");
  define("DEFAULT_PAGESIZE",   5);
  define("DEFAULT_SITENAME",   "Your Website");
  define("DEFAULT_SITESLOGAN", "Your Slogan");
  define("DEFAULT_TIMEZONE",   "Europe/Berlin");

  // DERIVED DEFAULTS

  define("DEFAULT_HOSTNAME", _getDefaultHostname());
  define("DEFAULT_METHOD",   _getDefaultMethod());
  define("DEFAULT_PORT",     _getDefaultPort());
  define("DEFAULT_PROTOCOL", _getDefaultProtocol());
  define("DEFAULT_ROOTURI",  _getDefaultRootUri());
  define("DEFAULT_URI",      _getDefaultUri());

