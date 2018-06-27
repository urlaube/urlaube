<?php

  /**
    This is the FeedAuthorHandler class of the urlau.be CMS.

    This file contains the FeedAuthorHandler class of the urlau.be CMS. The
    feed author handler produces an RSS 2.0 feed of the first content page of a certain type.

    @package urlaube\urlaube
    @version 0.1a5
    @author  Yahe <hello@yahe.sh>
    @since   0.1a2
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists(FEED_AUTHOR_HANDLER)) {
    class FeedAuthorHandler extends Base implements Handler {

      // INTERFACE FUNCTIONS

      public static function getContent($info) {
        return AuthorHandler::getContent($info);
      }

      public static function getUri($info) {
        $result = Main::ROOTURI()."feed".US."author".US;

        if (is_array($info)) {
          if (isset($info[AUTHOR]) && is_string($info[AUTHOR])) {
            $result .= urlencode($info[AUTHOR]).US;
          }
        }

        return $result;
      }

      public static function parseUri($uri) {
        $result = null;

        if (1 === preg_match("@^\/feed\/author\/([0-9A-Za-z\_\-]+)\/$@",
                             $uri, $matches)) {
          $result = array();

          // get the requested author name
          if (2 <= count($matches)) {
            $result[AUTHOR] = $matches[1];
          }
        }

        return $result;
      }

      // RUNTIME FUNCTIONS

      public static function handle() {
        $result = false;

        if ((!Handlers::get(DEACTIVATE_AUTHOR)) &&
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
    Handlers::preset(DEACTIVATE_AUTHOR, false);
    Handlers::preset(DEACTIVATE_FEED,   false);

    // register handler
    Handlers::register(FEED_AUTHOR_HANDLER, "handle",
                       "@^\/feed\/author\/([0-9A-Za-z\_\-]+)\/$@",
                       [GET], SYSTEM);
  }

