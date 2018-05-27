<?php

  /**
    This is the FeedArchiveHandler class of the urlau.be CMS.

    This file contains the FeedArchiveHandler class of the urlau.be CMS. The
    feed archive handler produces an RSS 2.0 feed of the first content page of a certain type.

    @package urlaube\urlaube
    @version 0.1a3
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists(FEED_ARCHIVE_HANDLER)) {
    class FeedArchiveHandler implements Handler {

      // INTERFACE FUNCTIONS

      public static function getContent($info) {
        return ArchiveHandler::getContent($info);
      }

      public static function getUri($info) {
        $result = Main::ROOTURI()."feed".US."archive".US;

        if (is_array($info)) {
          if (isset($info[YEAR]) && is_numeric($info[YEAR])) {
            $result .= $info[YEAR].US;

            if (isset($info[MONTH]) && is_numeric($info[MONTH])) {
              $result .= $info[MONTH].US;

              if (isset($info[DAY]) && is_numeric($info[DAY])) {
                $result .= $info[DAY].US;
              }
            }
          }
        }

        return $result;
      }

      public static function parseUri($uri) {
        $result = null;

        if (1 === preg_match("@^\/feed\/archive\/(?:([0-9]+)\/)?(?:([0-9]+)\/)?(?:([0-9]+)\/)?$@",
                             $uri, $matches)) {
          $result = array();

          // get the requested archive year
          if (2 <= count($matches)) {
            if (is_numeric($matches[1])) {
              $result[YEAR] = intval($matches[1]);
            }
          }

          // get the requested archive month
          if (3 <= count($matches)) {
            if (is_numeric($matches[2])) {
              $result[MONTH] = intval($matches[2]);
            }
          }

          // get the requested archive day
          if (4 <= count($matches)) {
            if (is_numeric($matches[3])) {
              $result[DAY] = intval($matches[3]);
            }
          }
        }

        return $result;
      }

      // RUNTIME FUNCTIONS

      public static function handle() {
        $result = false;

        if ((!Handlers::get(DEACTIVATE_ARCHIVE)) &&
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
    Handlers::preset(DEACTIVATE_ARCHIVE, false);
    Handlers::preset(DEACTIVATE_FEED,    false);

    // register handler
    Handlers::register(FEED_ARCHIVE_HANDLER, "handle",
                       "@^\/feed\/archive\/(([0-9]+)\/)?(?:([0-9]+)\/)?(?:([0-9]+)\/)?$@",
                       [GET], SYSTEM);
  }

