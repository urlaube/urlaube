<?php

  /**
    This is the FixUrlAddon class of the urlau.be CMS.

    The fix URL addon is meant to improve incorrectly written URLs.

    @package urlaube/urlaube
    @version 0.2a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class FixUrlAddon extends BaseSingleton implements Handler {

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

      // take the given URI, fix the slashes, kill ./ and ../ and check whether the given URI matches the fixed URI,
      // if this is not the case then redirect to the fixed URI
      $array = array_filter(explode(US, gc(URI, null)));
      $fixed = [];
      foreach ($array as $array_item) {
        switch ($array_item) {
          // do nothing
          case "." :
            break;

          // remove one element from the URI
          case ".." :
            array_pop($fixed);
            break;

          // append to the URI
          default:
            array_push($fixed, urlencode($array_item));
        }
      }

      $fixed = lead(implode(US, $fixed), US);
      if ((0 !== strcmp(gc(URI, null), urldecode($fixed))) &&
          (0 !== strcmp(gc(URI, null), urldecode(trail($fixed, US))))) {
        relocate($fixed.querystring(), false, true);

        // we handled this page
        $result = true;
      }

      return $result;
    }

  }

  // register handler
  Handlers::register(FixUrlAddon::class, "run", FixUrlAddon::REGEX, null, FIXURL_ADDON);
