<?php

  /**
    These are the derived constants of the urlau.be CMS core.

    This file contains the derived constants of the urlau.be CMS core. These are
    used to separate logic from content like strings.

    @package urlaube\urlaube
    @version 0.1a9
    @author  Yahe <hello@yahe.sh>
    @since   0.1a7
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  // DERIVED CONSTANTS

  // derive system paths
  $path = realpath(SYSTEM_PATH);
  if ((false !== $path) && is_dir($path)) {
    $path = trail($path, DS);
  } else {
    $path = ROOTPATH."system".DS;
  }
  define("SYSTEM_CORE_PATH",     $path."core".DS);
  define("SYSTEM_HANDLERS_PATH", $path."handlers".DS);
  define("SYSTEM_PLUGINS_PATH",  $path."plugins".DS);

  // derive user paths
  $path = realpath(USER_PATH);
  if ((false !== $path) && is_dir($path)) {
    $path = trail($path, DS);
  } else {
    $path = ROOTPATH."user".DS;
  }
  define("USER_CACHE_PATH",    $path."cache".DS);
  define("USER_CONFIG_PATH",   $path."config".DS);
  define("USER_CONTENT_PATH",  $path."content".DS);
  define("USER_HANDLERS_PATH", $path."handlers".DS);
  define("USER_PLUGINS_PATH",  $path."plugins".DS);
  define("USER_THEMES_PATH",   $path."themes".DS);
