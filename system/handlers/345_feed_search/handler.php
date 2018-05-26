<?php

  /**
    This is the FeedSearchHandler class of the urlau.be CMS.

    This file contains the FeedSearchHandler class of the urlau.be CMS. The
    feed search handler produces an RSS 2.0 feed of the first content page of a certain type.

    @package urlaube\urlaube
    @version 0.1a2
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists("FeedSearchHandler")) {
    class FeedSearchHandler implements Handler {

      // INTERFACE FUNCTIONS

      public static function getContent($info) {
        return SearchHandler::getContent($info);
      }

      public static function getUri($info) {
        $result = Main::ROOTURI()."feed".US."search".US;

        if (is_array($info)) {
          if (isset($info[SEARCH]) && is_string($info[SEARCH])) {
            $result .= urlencode($info[SEARCH]).US;
          }
        }

        return $result;
      }

      public static function parseUri($uri) {
        $result = null;

        if (1 === preg_match("@^\/feed\/search\/([0-9A-Za-z\_\-\.]+)\/$@",
                             $uri, $matches)) {
          $result = array();

          // get the requested search string
          if (2 <= count($matches)) {
            $result[SEARCH] = $matches[1];
          }
        }

        return $result;
      }

      // RUNTIME FUNCTIONS

      public static function handle() {
        $result = false;

        if ((!Handlers::get(DEACTIVATE_SEARCH)) &&
            (!Handlers::get(DEACTIVATE_FEED))) {
          $info = static::parseUri(Main::RELATIVEURI());
          if (null !== $info) {
            $content = static::getContent($info);
            if (null !== $content) {
              // set the content type
              header("Content-Type: application/rss+xml");

              // filter the content before calling the feed generation
              $content = Plugins::run(FILTER_CONTENT, true, $content);

              // generate and output the feed
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
    Handlers::preset(DEACTIVATE_SEARCH, false);
    Handlers::preset(DEACTIVATE_FEED,   false);

    // register handler
    Handlers::register("FeedSearchHandler", "handle",
                       "@^\/feed\/search\/([0-9A-Za-z\_\-\.]+)\/$@",
                       [GET], SYSTEM);
  }

