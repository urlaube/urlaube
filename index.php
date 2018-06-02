<?php

  /**
    This is the index file of the urlau.be CMS.

    This file is the single entrypoint to the urlau.be CMS system. All calls to the system have to be directed at
    this file. It initializes the rest of the system.

    @package urlaube\urlaube
    @version 0.1a4
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== EDIT HERE =====

  // define the path to the system folder to find the init.php file
  define("SYSTEM_PATH", __DIR__."/system/");

  // define the path to the user folder to find the config.php file
  define("USER_PATH", __DIR__."/user/");

  // ===== DO NOT EDIT HERE =====

  // define the URLAUBE constant the prevents the side-loading of other files
  define("URLAUBE", true);

  // define the default root path
  define("ROOT_PATH", __DIR__.DIRECTORY_SEPARATOR);

  // define the start time
  define("START_TIME", microtime(true));

  // require the init.php file
  require_once(SYSTEM_PATH."init.php");

  // do some number crunching
  _logResourceUsage();

