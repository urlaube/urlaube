<?php

  /**
    This is the FaviconIcoHandler class of the urlau.be CMS.

    This file contains the FaviconIcoHandler class of the urlau.be CMS. The favicon.ico handler generates static file
    contents for certain typically provided files.

    @package urlaube\urlaube
    @version 0.1a5
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists(FAVICON_ICO_HANDLER)) {
    class FaviconIcoHandler extends Base implements Handler {

      // INTERFACE FUNCTIONS

      public static function getContent($info) {
        return null;
      }

      public static function getUri($info) {
        return Main::ROOTURI()."favicon.ico";
      }

      public static function parseUri($uri) {
        $result = null;

        if (is_string($uri)) {
          if (1 === preg_match("@^\/favicon\.ico$@",
                               $uri, $matches)) {
            $result = array();
          }
        }

        return $result;
      }

      // RUNTIME FUNCTIONS

      public static function handle() {
        $result = false;

        if (!Handlers::get(DEACTIVATE_FAVICON_ICO)) {
          $info = static::parseUri(Main::RELATIVEURI());
          if (null !== $info) {
            // set the HTTP response code to "no content"
            http_response_code(204);

            $result = true;
          }
        }

        // we handled this page
        return $result;
      }

    }

    // activate handler by default
    Handlers::preset(DEACTIVATE_FAVICON_ICO, false);

    // register handler
    Handlers::register(FAVICON_ICO_HANDLER, "handle",
                       "@^\/favicon\.ico$@",
                       [GET], BEFORE_ADDSLASH);
  }

