<?php

  /**
    This is the FeedHomeHandler class of the urlau.be CMS.

    This file contains the FeedHomeHandler class of the urlau.be CMS. The
    feed home handler produces an RSS 2.0 feed of the first content page of a certain type.

    @package urlaube\urlaube
    @version 0.1a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists("FeedHomeHandler")) {
    class FeedHomeHandler implements Handler {

      // INTERFACE FUNCTIONS

      public static function getContent($info) {
        return HomeHandler::getContent($info);
      }

      public static function getUri($info) {
        return Main::ROOTURI()."feed".US;
      }

      public static function parseUri($uri) {
        $result = null;

        if (1 === preg_match("@^\/feed\/$@",
                             $uri, $matches)) {
          $result = array();
        }

        return $result;
      }

      // RUNTIME FUNCTIONS

      public static function handle() {
        $result = false;

        if ((!Handlers::get(DEACTIVATE_HOME)) &&
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
    Handlers::register("FeedHomeHandler", "handle",
                       "@^\/feed\/$@",
                       [GET], SYSTEM);
  }

