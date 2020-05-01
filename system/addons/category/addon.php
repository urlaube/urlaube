<?php

  /**
    This is the CategoryAddon class of the urlau.be CMS.

    The category addon lists all pages that contain a certain category identifier.

    @package urlaube/urlaube
    @version 0.2a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class CategoryAddon extends BaseHandler {

    // CONSTANTS

    const MANDATORY = [CATEGORY];
    const OPTIONAL  = [PAGE => 1];
    const PAGINATE  = true;
    const REGEX     = "~^\/".
                      "category\=(?P<category>[0-9A-Za-z\_\-]+)\/".
                      "(page\=(?P<page>[0-9]+)\/)?".
                      "$~";

    // ABSTRACT FUNCTIONS

    protected static function getResult($metadata) {
      $category = $metadata->get(CATEGORY);

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
      $metadata->set(CATEGORY, strtolower($metadata->get(CATEGORY)));

      return $metadata;
    }

  }

  // register handler
  Handlers::register(CategoryAddon::class, "run", CategoryAddon::REGEX, [GET, POST], ERROR_SYSTEM);
