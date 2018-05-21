<?php

  /**
    This is the FeedCategoryHandler class of the urlau.be CMS.

    This file contains the FeedCategoryHandler class of the urlau.be CMS. The
    feed category handler produces an RSS 2.0 feed of the first content page of a certain type.

    @package urlaube\urlaube
    @version 0.1a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists("FeedCategoryHandler")) {
    class FeedCategoryHandler implements Handler {

      // INTERFACE FUNCTIONS

      public static function getContent($info) {
        return CategoryHandler::getContent($info);
      }

      public static function getUri($info) {
        $result = Main::ROOTURI()."feed".US."category".US;

        if (is_array($info)) {
          if (isset($info[CATEGORY]) && is_string($info[CATEGORY])) {
            $result .= urlencode($info[CATEGORY]).US;
          }
        }

        return $result;
      }

      public static function parseUri($uri) {
        $result = null;

        if (1 === preg_match("@^\/feed\/category\/([0-9A-Za-z\_\-]+)\/$@",
                             $uri, $matches)) {
          $result = array();

          // get the requested category name
          if (2 <= count($matches)) {
            $result[CATEGORY] = $matches[1];
          }
        }

        return $result;
      }

      // RUNTIME FUNCTIONS

      public static function handle() {
        $result = false;

        if ((!Handlers::get(DEACTIVATE_CATEGORY)) &&
            (!Handlers::get(DEACTIVATE_FEED))) {
          $info = static::parseUri(Main::RELATIVEURI());
          if (null !== $info) {
            $content = static::getContent($info);
            if (null !== $content) {
              // set the content type
              header("Content-Type: application/rss+xml");

              print(Feed::generate($content));

              // we handled this page
              $result = true;
            }
          }
        }

        return $result;
      }

    }

    // activate handler by default
    Handlers::preset(DEACTIVATE_FEED, false);

    // register handler
    Handlers::register("FeedCategoryHandler", "handle",
                       "@^\/feed\/category\/([0-9A-Za-z\_\-]+)\/$@",
                       [GET], SYSTEM);
  }

