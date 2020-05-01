<?php

  /**
    This is the AuthorAddon class of the urlau.be CMS.

    The author addon lists all pages that are written by the given author.

    @package urlaube/urlaube
    @version 0.2a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a2
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class AuthorAddon extends BaseHandler {

    // CONSTANTS

    const MANDATORY = [AUTHOR];
    const OPTIONAL  = [PAGE => 1];
    const PAGINATE  = true;
    const REGEX     = "~^\/".
                      "author\=(?P<author>[0-9A-Za-z\_\-]+)\/".
                      "(page\=(?P<page>[0-9]+)\/)?".
                      "$~";

    // ABSTRACT FUNCTIONS

    protected static function getResult($metadata) {
      $author = $metadata->get(AUTHOR);

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
      $metadata->set(AUTHOR, strtolower($metadata->get(AUTHOR)));

      return $metadata;
    }

  }

  // register handler
  Handlers::register(AuthorAddon::class, "run", AuthorAddon::REGEX, [GET, POST], ERROR_SYSTEM);
