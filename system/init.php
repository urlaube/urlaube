<?php

  /**
    This is the init file of the urlau.be CMS.

    This is the init file of the urlau.be CMS. The file includes all other
    relevant files of the system and kickstarts the execution of the CMS.

    @package urlaube\urlaube
    @version 0.1a10
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  // require helper functions
  require_once(SYSTEM_PATH."system.php");
  require_once(SYSTEM_PATH."user.php");

  // require constants
  require_once(SYSTEM_PATH."static.php");
  require_once(SYSTEM_PATH."recommended.php");
  require_once(SYSTEM_PATH."derived.php");

  // require interfaces
  require_once(SYSTEM_CORE_PATH."Handler.interface.php");
  require_once(SYSTEM_CORE_PATH."Plugin.interface.php");
  require_once(SYSTEM_CORE_PATH."Theme.interface.php");

  // require base classes
  require_once(SYSTEM_CORE_PATH."BaseSingleton.class.php");
  require_once(SYSTEM_CORE_PATH."BaseConfig.class.php");
  require_once(SYSTEM_CORE_PATH."BaseHandler.class.php");
  require_once(SYSTEM_CORE_PATH."Content.class.php");
  require_once(SYSTEM_CORE_PATH."Logging.class.php");
  require_once(SYSTEM_CORE_PATH."Translate.class.php");

  // require management classes
  require_once(SYSTEM_CORE_PATH."Handlers.class.php");
  require_once(SYSTEM_CORE_PATH."Plugins.class.php");
  require_once(SYSTEM_CORE_PATH."Themes.class.php");

  // require main class
  require_once(SYSTEM_CORE_PATH."Main.class.php");

  // preset the core configuration
  Main::configure();

  // include user configuration if possible
  if (is_file(USER_CONFIG_PATH."config.php")) {
    include_once(USER_CONFIG_PATH."config.php");
  }

  // transfer handling to the Main class
  print(Main::run());
