<?php

  /**
    This is the RobotsTxtAddon class of the urlau.be CMS.

    The robots.txt addon generates the contents of the robots text file.

    @package urlaube/urlaube
    @version 0.2a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class RobotsTxtAddon extends BaseSingleton implements Handler {

    // CONSTANTS

    const REGEX = "~^\/robots\.txt$~";

    // INTERFACE FUNCTIONS

    public static function getContent($metadata, &$pagecount) {
      return null;
    }

    public static function getUri($metadata) {
      return gc(ROOTURI, null)."robots.txt";
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
        if (0 !== strcmp(gc(URI, null), $fixed)) {
          relocate($fixed.querystring(), false, true);
        } else {
          // set the content type
          header("Content-Type: text/plain");

          // return a minimalistic robots.txt
          print(fhtml("User-agent: *".NL.
                      "Disallow:".NL.
                      NL.
                      "Sitemap: %s",
                      absoluteurl(SitemapXmlAddon::getUri(new Content()))));
        }

        // we handled this page
        $result = true;
      }

      return $result;
    }

  }

  // register handler
  Handlers::register(RobotsTxtAddon::class, "run", RobotsTxtAddon::REGEX, [GET, POST], ADDSLASH_SYSTEM);
