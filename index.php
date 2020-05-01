<?php

  /**
    This is the index file of the urlau.be CMS.

    This file is the single entrypoint to the urlau.be CMS system. All calls to
    the system have to be directed at this file. It initializes the rest of the
    system.

    @package urlaube/urlaube
    @version 0.2a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // only define $name if it is not yet defined
  function try_define($name, $value) {
    $result = false;

    if (!defined($name)) {
      $result = define($name, $value);
    }

    return $result;
  }

  // define the URLAUBE constant that prevents the side-loading of other files
  try_define("URLAUBE", true);

  // require the user.php file if it exists
  if (is_file(__DIR__.DIRECTORY_SEPARATOR."user.php")) {
    require_once(__DIR__.DIRECTORY_SEPARATOR."user.php");
  }

  // define the default root path
  try_define("ROOTPATH", __DIR__.DIRECTORY_SEPARATOR);

  // define the system path
  try_define("SYSTEM_PATH", ROOTPATH."system".DIRECTORY_SEPARATOR);

  // define the user path
  try_define("USER_PATH", ROOTPATH."user".DIRECTORY_SEPARATOR);

  // require the init.php file
  require_once(SYSTEM_PATH."init.php");
