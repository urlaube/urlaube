<?php

  /**
    This is the FixUrlHandler class of the urlau.be CMS.

    This file contains the FixUrlHandler class of the urlau.be CMS. The fix URL handler is meant to improve
    incorrectly written URLs.

    @package urlaube\urlaube
    @version 0.1a6
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists(FIXURL_HANDLER)) {
    class FixUrlHandler extends Base implements Handler {

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
          // take the given URI, fix the slashes, kill ./ and ../ and check whether the given URI matches the fixed URI,
          // if this is not the case then redirect to the fixed URI
          $array = array_filter(explode(US, Main::URI()));
          $fixed = array();
          foreach ($array as $array_value) {
            switch ($array) {
              // do nothing
              case "." :
                break;

              // remove one element from the URI
              case ".." :
                array_pop($fixed);
                break;

              // append to the URI
              default:
                array_push($fixed, urlencode($array_value));
            }
          }

          $fixed = lead(implode(US, $fixed), US);
          if ((0 !== strcmp(Main::URI(), urldecode($fixed))) &&
              (0 !== strcmp(Main::URI(), trail(urldecode($fixed), US)))) {
            relocate($fixed, false, true);

            // we handled this page
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

