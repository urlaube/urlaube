<?php

  /**
    This is the RobotsTxtHandler class of the urlau.be CMS.

    This file contains the RobotsTxtHandler class of the urlau.be CMS. The
    robots.txt handler generates static file contents for certain typically
    provided files.

    @package urlaube\urlaube
    @version 0.1a10
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class RobotsTxtHandler extends BaseSingleton implements Handler {

    // CONSTANTS

    const REGEX = "~^\/robots\.txt$~";

    // INTERFACE FUNCTIONS

    public static function getContent($metadata, &$pagecount) {
      return null;
    }

    public static function getUri($metadata) {
      return value(Main::class, ROOTURI)."robots.txt";
    }

    public static function parseUri($uri) {
      $result = null;

      $metadata = preparecontent(parseuri($uri, static::REGEX));
      if ($metadata instanceof Content) {
        $result = $metadata;
      }

      return $result;
    }

    // RUNTIME FUNCTIONS

    public static function run() {
      $result = false;

      $metadata = static::parseUri(relativeuri());
      if (null !== $metadata) {
        // check if the URI is correct
        $fixed = static::getUri($metadata);
        if (0 !== strcmp(value(Main::class, URI), $fixed)) {
          relocate($fixed.querystring(), false, true);
        } else {
          // set the content type
          header("Content-Type: text/plain");

          // return a minimalistic robots.txt
          print(fhtml("User-agent: *".NL.
                      "Disallow:".NL.
                      NL.
                      "Sitemap: %s",
                      absoluteurl(SitemapXmlHandler::getUri(new Content()))));
        }

        // we handled this page
        $result = true;
      }

      return $result;
    }

  }

  // register handler
  Handlers::register(RobotsTxtHandler::class, "run", RobotsTxtHandler::REGEX, [GET, POST], ADDSLASH_SYSTEM);
