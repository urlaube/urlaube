<?php

  /**
    This is the PageAddon class of the urlau.be CMS.

    The page addon provides access to a single page stored in a file.

    @package urlaube/urlaube
    @version 0.2a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class PageAddon extends BaseHandler {

    // CONSTANTS

    const NAME = "name";

    const MANDATORY = [self::NAME];
    const OPTIONAL  = null;
    const PAGINATE  = false;
    const REGEX     = "~^\/".
                      "(?P<name>[0-9A-Za-z\_\-\/\.]*)".
                      "$~";

    // ABSTRACT FUNCTIONS

    protected static function getResult($metadata) {
      return callcontent($metadata->get(static::NAME), false, false, null);
    }

    protected static function prepareMetadata($metadata) {
      $metadata->set(static::NAME, trail(lead($metadata->get(static::NAME), US), US));

      return $metadata;
    }

    // INTERFACE FUNCTIONS

    // overwrite the default behaviour
    public static function getUri($metadata) {
      $result = null;

      // prepare metadata for sanitization
      $metadata = preparecontent($metadata, static::OPTIONAL, static::MANDATORY);
      if ($metadata instanceof Content) {
        // sanitize metadata
        $metadata = preparecontent(static::prepareMetadata($metadata), static::OPTIONAL, static::MANDATORY);
        if ($metadata instanceof Content) {
          // get the base URI
          $result = gc(ROOTURI, null);

          // append the mandatory URI parts
          $result .= nolead($metadata->get(static::NAME), US);
        }
      }

      return $result;
    }

  }

  // register handler
  Handlers::register(PageAddon::class, "run", PageAddon::REGEX, [GET, POST], PAGE_ADDON);
