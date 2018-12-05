<?php

  /**
    This is the AuthorHandler class of the urlau.be CMS.

    This file contains the AuthorHandler class of the urlau.be CMS. The author
    handler lists all pages that are written by the given author.

    @package urlaube\urlaube
    @version 0.1a11
    @author  Yahe <hello@yahe.sh>
    @since   0.1a2
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class AuthorHandler extends BaseHandler {

    // CONSTANTS

    const MANDATORY = [AUTHOR];
    const OPTIONAL  = [PAGE => 1];
    const REGEX     = "~^\/".
                      "author\=(?P<author>[0-9A-Za-z\_\-]+)\/".
                      "(page\=(?P<page>[0-9]+)\/)?".
                      "$~";

    // ABSTRACT FUNCTIONS

    protected static function getResult($metadata, &$cachable) {
      // this result may be cached
      $cachable = true;

      $author = value($metadata, AUTHOR);

      return callcontent(null, true, false,
                         function ($content) use ($author) {
                           $result = null;

                           // check that $content has the $author
                           if (hasauthor($content, $author)) {
                             $result = $content;
                           }

                           return $result;
                         });
    }

    protected static function prepareMetadata($metadata) {
      $metadata->set(AUTHOR, strtolower(value($metadata, AUTHOR)));

      return $metadata;
    }

  }

  // register handler
  Handlers::register(AuthorHandler::class, "run", AuthorHandler::REGEX, [GET, POST], PAGE_SYSTEM);
