<?php

  /**
    This is the FixUrlHandler class of the urlau.be CMS.

    This file contains the FixUrlHandler class of the urlau.be CMS. The fix URL handler is meant to improve
    incorrectly written URLs.

    @package urlaube\urlaube
    @version 0.1a3
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists(FIXURL_HANDLER)) {
    class FixUrlHandler implements Handler {

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

        if (!Handlers::get(DEACTIVATE_FIXURL)) {
          // take the given URI, fix the slashes and check whether the given
          // URI matches the fixed URI, if this is not the case then redirect
          // to the fixed URI
          $array = array_filter(explode(US, Main::URI()));
          foreach ($array as $array_key => $array_value) {
            // URL-encode the URL parts
            $array[$array_key] = urlencode($array_value);
          }

          $fixed = lead(implode(US, $array), US);
          if ((0 !== strcmp(Main::URI(), urldecode($fixed))) &&
              (0 !== strcmp(Main::URI(), trail(urldecode($fixed), US)))) {
            redirect($fixed);

            $result = true;
          }
        }

        return $result;
      }

    }

    // activate handler by default
    Handlers::preset(DEACTIVATE_FIXURL, false);

    // register handler
    Handlers::register(FIXURL_HANDLER, "handle",
                       "@^.*$@",
                       [GET, POST], FIXURL);
  }

