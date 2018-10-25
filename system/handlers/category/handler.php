<?php

  /**
    This is the CategoryHandler class of the urlau.be CMS.

    This file contains the CategoryHandler class of the urlau.be CMS. The
    category handler lists all pages that contain a certain category identifier.

    @package urlaube\urlaube
    @version 0.1a8
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

      return FilePlugin::loadContentDir(USER_CONTENT_PATH, false,
                                        function ($content) use ($category) {
                                          $result = null;

                                          // check that $content is not hidden
                                          if (!istrue(value($content, HIDDEN))) {
                                            // check that $content is not hidden from category
                                            if (!istrue(value($content, HIDDENFROMCATEGORY))) {
                                              // check that $content is not a relocation
                                              if (null === value($content, RELOCATE)) {
                                                // check that $content has the $category
                                                if (hascategory($content, $category)) {
                                                  $result = $content;
                                                }
                                              }
                                            }
                                          }

                                          return $result;
                                        },
                                        true);
    }

    protected static function prepareMetadata($metadata) {
      $metadata->set(CATEGORY, strtolower(value($metadata, CATEGORY)));

      return $metadata;
    }

  }

  // register handler
  Handlers::register(CategoryHandler::class, "run", CategoryHandler::REGEX, [GET, POST], PAGE_SYSTEM);
