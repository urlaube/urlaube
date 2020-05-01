<?php

  /**
    This is the IndexPhpAddon class of the urlau.be CMS.

    The index.php addon redirects a call to the index file to root URI.

    @package urlaube/urlaube
    @version 0.2a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class IndexPhpAddon extends BaseSingleton implements Handler {

    // CONSTANTS

    const REGEX = "~^\/index\.php$~";

    // INTERFACE FUNCTIONS

    public static function getContent($metadata, &$pagecount) {
      return null;
    }

    public static function getUri($metadata) {
      return gc(ROOTURI, null)."index.php";
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
        relocate(gc(ROOTURI, null).querystring(), false, true);

        // we handled this page
        $result = true;
      }

      return $result;
    }

  }

  // register handler
  Handlers::register(IndexPhpAddon::class, "run", IndexPhpAddon::REGEX, [GET, POST], ADDSLASH_SYSTEM);
