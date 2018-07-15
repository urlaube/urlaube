<?php

  /**
    This is the AddSlashHandler class of the urlau.be CMS.

    This file contains the AddSlashHandler class of the urlau.be CMS. The addslash handler is meant to provide a 
    generic URL structure for all other handlers.

    @package urlaube\urlaube
    @version 0.1a6
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists(ADDSLASH_HANDLER)) {
    class AddSlashHandler extends Base implements Handler {

      // INTERFACE FUNCTIONS

      public static function getContent($info) {
        return null;
      }

      public static function getUri($info) {
        return null;
      }

      public static function parseUri($uri) {
        return null;
      }

      // RUNTIME FUNCTIONS

      public static function handle() {
        $result = false;

        if (!Handlers::get(DEACTIVATE_ADDSLASH)) {
          // take the given URI and add a trailing slash
          if (!$result) {
            $fixed = trail(Main::URI(), US);
            if (0 !== strcmp(Main::URI(), $fixed)) {
              relocate($fixed, false, true);

              // we handled this page
              $result = true;
            }
          }
        }

        return $result;
      }

    }

    // activate handler by default
    Handlers::preset(DEACTIVATE_ADDSLASH, false);

    // register handler
    Handlers::register(ADDSLASH_HANDLER, "handle",
                       "@^.*$@",
                       [GET, POST], ADDSLASH);
  }
