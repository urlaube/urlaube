<?php

  /**
    These are the derived constants of the urlau.be CMS core.

    This file contains the derived constants of the urlau.be CMS core. These are
    used to separate logic from content like strings.

    @package urlaube\urlaube
    @version 0.1a11
    @author  Yahe <hello@yahe.sh>
    @since   0.1a7
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  // DERIVED CONSTANTS

  // derive system paths
  try_define("SYSTEM_CORE_PATH",     trail(realpath(SYSTEM_PATH), DS)."core".DS);
  try_define("SYSTEM_HANDLERS_PATH", trail(realpath(SYSTEM_PATH), DS)."handlers".DS);
  try_define("SYSTEM_PLUGINS_PATH",  trail(realpath(SYSTEM_PATH), DS)."plugins".DS);
  try_define("SYSTEM_THEMES_PATH",   trail(realpath(SYSTEM_PATH), DS)."themes".DS);

  // derive user paths
  try_define("USER_CONFIG_PATH",   trail(realpath(USER_PATH), DS)."config".DS);
  try_define("USER_HANDLERS_PATH", trail(realpath(USER_PATH), DS)."handlers".DS);
  try_define("USER_PLUGINS_PATH",  trail(realpath(USER_PATH), DS)."plugins".DS);
  try_define("USER_THEMES_PATH",   trail(realpath(USER_PATH), DS)."themes".DS);
