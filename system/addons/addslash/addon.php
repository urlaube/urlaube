<?php

  /**
    This is the AddSlashAddon class of the urlau.be CMS.

    The add slash addon is meant to provide a generic URL structure for all other handlers.

    @package urlaube/urlaube
    @version 0.2a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class AddSlashAddon extends BaseSingleton implements Handler {

    // CONSTANTS

    const REGEX = "~^.*$~";

    // INTERFACE FUNCTIONS

    public static function getContent($metadata, &$pagecount) {
      return null;
    }

    public static function getUri($metadata) {
      return null;
    }

    public static function parseUri($uri) {
      return null;
    }

    // RUNTIME FUNCTIONS

    public static function run() {
      $result = false;

      // take the given URI and add a trailing slash
      $fixed = trail(gc(URI, null), US);
      if (0 !== strcmp(gc(URI, null), $fixed)) {
        relocate($fixed.querystring(), false, true);

        // we handled this page
        $result = true;
      }

      return $result;
    }

  }

  // register handler
  Handlers::register(AddSlashAddon::class, "run", AddSlashAddon::REGEX, null, ADDSLASH_ADDON);
