<?php

  /**
    This is the CategoryHandler class of the urlau.be CMS.

    This file contains the CategoryHandler class of the urlau.be CMS. The
    category handler lists all pages that contain a certain category identifier.

    @package urlaube\urlaube
    @version 0.1a12
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class CategoryHandler extends BaseHandler {

    // CONSTANTS

    const MANDATORY = [CATEGORY];
    const OPTIONAL  = [PAGE => 1];
    const REGEX     = "~^\/".
                      "category\=(?P<category>[0-9A-Za-z\_\-]+)\/".
                      "(page\=(?P<page>[0-9]+)\/)?".
                      "$~";

    // ABSTRACT FUNCTIONS

    protected static function getResult($metadata, &$cachable) {
      // this result may be cached
      $cachable = true;

      $category = value($metadata, CATEGORY);

      return callcontent(null, true, false,
                         function ($content) use ($category) {
                           $result = null;

                           // check that $content has the $category
                           if (hascategory($content, $category)) {
                             $result = $content;
                           }

                           return $result;
                         });
    }

    protected static function prepareMetadata($metadata) {
      $metadata->set(CATEGORY, strtolower(value($metadata, CATEGORY)));

      return $metadata;
    }

  }

  // register handler
  Handlers::register(CategoryHandler::class, "run", CategoryHandler::REGEX, [GET, POST], ERROR_SYSTEM);
