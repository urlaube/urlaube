<?php

  /**
    These are the derived constants of the urlau.be CMS core.

    This file contains the derived constants of the urlau.be CMS core. These are
    used to separate logic from content like strings.

    @package urlaube/urlaube
    @version 0.2a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a7
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  // DERIVED CONSTANTS

  // derive system paths
  try_define("SYSTEM_ADDONS_PATH", trail(realpath(SYSTEM_PATH), DS)."addons".DS);
  try_define("SYSTEM_CORE_PATH",   trail(realpath(SYSTEM_PATH), DS)."core".DS);

  // derive user paths
  try_define("USER_ADDONS_PATH",  trail(realpath(USER_PATH), DS)."addons".DS);
  try_define("USER_CACHE_PATH",   trail(realpath(USER_PATH), DS)."cache".DS);
  try_define("USER_CONFIG_PATH",  trail(realpath(USER_PATH), DS)."config".DS);
  try_define("USER_CONTENT_PATH", trail(realpath(USER_PATH), DS)."content".DS);
  try_define("USER_UPLOADS_PATH", trail(realpath(USER_PATH), DS)."uploads".DS);
