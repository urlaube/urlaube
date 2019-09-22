<?php

  /**
    This is the IndexPhpHandler class of the urlau.be CMS.

    This file contains the IndexPhpHandler class of the urlau.be CMS. The
    index.php handler generates static file contents for certain typically
    provided files.

    @package urlaube\urlaube
    @version 0.1a12
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class IndexPhpHandler extends BaseSingleton implements Handler {

    // CONSTANTS

    const REGEX = "~^\/index\.php$~";

    // INTERFACE FUNCTIONS

    public static function getContent($metadata, &$pagecount) {
      return null;
    }

    public static function getUri($metadata) {
      return value(Main::class, ROOTURI)."index.php";
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
        // redirect to the root URI
        relocate(value(Main::class, ROOTURI).querystring(), false, true);

        // we handled this page
        $result = true;
      }

      return $result;
    }

  }

  // register handler
  Handlers::register(IndexPhpHandler::class, "run", IndexPhpHandler::REGEX, [GET, POST], ADDSLASH_SYSTEM);
