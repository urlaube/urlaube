<?php

  /**
    This is the IndexPhpHandler class of the urlau.be CMS.

    This file contains the IndexPhpHandler class of the urlau.be CMS. The index.php handler generates static file
    contents for certain typically provided files.

    @package urlaube\urlaube
    @version 0.1a5
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists(INDEX_PHP_HANDLER)) {
    class IndexPhpHandler extends Base implements Handler {

      // INTERFACE FUNCTIONS

      public static function getContent($info) {
        return null;
      }

      public static function getUri($info) {
        return Main::ROOTURI()."index.php";
      }

      public static function parseUri($uri) {
        $result = null;

        if (is_string($uri)) {
          if (1 === preg_match("@^\/index\.php$@",
                               $uri, $matches)) {
            $result = array();
          }
        }

        return $result;
      }

      // RUNTIME FUNCTIONS

      public static function handle() {
        $result = false;

        if (!Handlers::get(DEACTIVATE_INDEX_PHP)) {
          $info = static::parseUri(Main::RELATIVEURI());
          if (null !== $info) {
            redirect(Main::ROOTURI());

            // we handled this page
            $result = true;
          }
        }

        return $result;
      }

    }

    // activate handler by default
    Handlers::preset(DEACTIVATE_INDEX_PHP, false);

    // register handler
    Handlers::register(INDEX_PHP_HANDLER, "handle",
                       "@^\/index\.php$@",
                       [GET], BEFORE_ADDSLASH);
  }

