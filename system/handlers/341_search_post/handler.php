<?php

  /**
    This is the SearchPostHandler class of the urlau.be CMS.

    This file contains the SearchPostHandler class of the urlau.be CMS. The
    search-post handler redirects to the search-get handler.

    @package urlaube\urlaube
    @version 0.1a5
    @author  Yahe <hello@yahe.sh>
    @since   0.1a2
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists(SEARCH_POST_HANDLER)) {
    class SearchPostHandler extends Base implements Handler {

      // INTERFACE FUNCTIONS

      public static function getContent($info) {
        return null;
      }

      public static function getUri($info) {
        return Main::ROOTURI()."search".US;
      }

      public static function parseUri($uri) {
        $result = null;

        if (1 === preg_match("@^\/search\/$@",
                             $uri, $matches)) {
          $result = array();
        }

        return $result;
      }

      // RUNTIME FUNCTIONS

      public static function handle() {
        $result = false;

        if (!Handlers::get(DEACTIVATE_SEARCH)) {
          $info = static::parseUri(Main::RELATIVEURI());
          if (null !== $info) {
            $uri = static::getUri($info);

            // prepare the post parameter
            if (isset($_POST[SEARCH])) {
              $uri .= preg_replace("@[^0-9A-Za-z\_\-\.]@", "", preg_replace("@\s+@", ".", $_POST[SEARCH])).US;
            }

            redirect($uri);

            // we handled this page
            $result = true;
          }
        }

        return $result;
      }

    }

    // activate handler by default
    Handlers::preset(DEACTIVATE_SEARCH, false);

    // register handler
    Handlers::register(SEARCH_POST_HANDLER, "handle",
                       "@^\/search\/$@",
                       [POST], SYSTEM);
  }

