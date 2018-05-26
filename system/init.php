<?php

  /**
    This is the init file of the urlau.be CMS.

    This is the init file of the urlau.be CMS. The file includes all other relevant files of the system and
    kickstarts the execution of the CMS.

    @package urlaube\urlaube
    @version 0.1a2
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
  require_once(SYSTEM_PATH."constants.php");
  require_once(SYSTEM_PATH."defaults.php");
  require_once(SYSTEM_PATH."recommends.php");

  // require base classes
  require_once(SYSTEM_CORE_PATH."Base.class.php");
  require_once(SYSTEM_CORE_PATH."Content.class.php");
  require_once(SYSTEM_CORE_PATH."Debug.class.php");

  // require main class
  require_once(SYSTEM_CORE_PATH."Main.class.php");

  // require management classes
  require_once(SYSTEM_CORE_PATH."Handlers.class.php");
  require_once(SYSTEM_CORE_PATH."Plugins.class.php");
  require_once(SYSTEM_CORE_PATH."Themes.class.php");
  require_once(SYSTEM_CORE_PATH."Translations.class.php");

  // require interfaces
  require_once(SYSTEM_CORE_PATH."Handler.interface.php");
  require_once(SYSTEM_CORE_PATH."Plugin.interface.php");
  require_once(SYSTEM_CORE_PATH."Theme.interface.php");
  require_once(SYSTEM_CORE_PATH."Translation.interface.php");

  // require default translation implementation
  require_once(SYSTEM_CORE_PATH."Translatable.class.php");

  // require config class
  require_once(SYSTEM_CORE_PATH."Config.class.php");

  // require user configuration
  if (is_file(USER_CONFIG_PATH.CONFIG_FILE)) {
    require_once(USER_CONFIG_PATH.CONFIG_FILE);
  }

  // transfer handling to the Main class
  print(Main::run());

