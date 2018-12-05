<?php

  /**
    This is the PageHandler class of the urlau.be CMS.

    This file contains the PageHandler class of the urlau.be CMS. The page
    handler provides access to a single page stored in a file.

    @package urlaube\urlaube
    @version 0.1a11
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class PageHandler extends BaseHandler {

    // CONSTANTS

    const NAME = "name";

    const MANDATORY = [self::NAME];
    const OPTIONAL  = null;
    const REGEX     = "~^\/".
                      "(?P<name>[0-9A-Za-z\_\-\/\.]*)".
                      "$~";

    // ABSTRACT FUNCTIONS

    protected static function getResult($metadata, &$cachable) {
      // this result may NOT be cached
      $cachable = false;

      $name = value($metadata, static::NAME);

      return callcontent($name, false, false, null);
    }

    protected static function prepareMetadata($metadata) {
      $metadata->set(static::NAME, trail(lead(value($metadata, static::NAME), US), US));

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
          $result = value(Main::class, ROOTURI);

          // append the mandatory URI parts
          $result .= nolead(value($metadata, static::NAME), US);
        }
      }

      return $result;
    }

  }

  // register handler
  Handlers::register(PageHandler::class, "run", PageHandler::REGEX, [GET, POST], PAGE_HANDLER);
