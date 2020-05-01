<?php

  /**
    This is the FaviconIcoHandler class of the urlau.be CMS.

    The favicon.ico addon prevents a missing favicon.ico from causing error log entries.

    @package urlaube/urlaube
    @version 0.2a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class FaviconAddon extends BaseSingleton implements Handler {

    // CONSTANTS

    const REGEX = "~^\/favicon\.ico$~";

    // INTERFACE FUNCTIONS

    public static function getContent($metadata, &$pagecount) {
      return null;
    }

    public static function getUri($metadata) {
      return gc(ROOTURI, null)."favicon.ico";
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
          // set the HTTP response code to "no content"
          http_response_code(204);
        }

        // we handled this page
        $result = true;
      }

      return $result;
    }

  }

  // register handler
  Handlers::register(FaviconAddon::class, "run", FaviconAddon::REGEX, [GET, POST], ADDSLASH_SYSTEM);
