<?php

  /**
    This is the SearchAddon class of the urlau.be CMS.

    The search addon lists all pages that contain a certain search keyword.

    @package urlaube/urlaube
    @version 0.2a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class SearchAddon extends BaseHandler {

    // CONSTANTS

    const SEARCH = "search";

    const MANDATORY = [self::SEARCH];
    const OPTIONAL  = [PAGE => 1];
    const PAGINATE  = true;
    const REGEX     = "~^\/".
                      "search\=(?P<search>[0-9A-Za-z\_\-\.]+)\/".
                      "(page\=(?P<page>[0-9]+)\/)?".
                      "$~";

    const REGEXPOST = "~^\/search\/$~";

    // ABSTRACT FUNCTIONS

    protected static function getResult($metadata) {
      $search = explode(DOT, $metadata->get(static::SEARCH));

      return callcontent(null, true, false,
                         function ($content) use ($search) {
                           $result = null;

                           // check that $content contains $keywords
                           if (haskeywords($content, AUTHOR, $search) ||
                               haskeywords($content, CATEGORY, $search) ||
                               haskeywords($content, CONTENT, $search) ||
                               haskeywords($content, DATE, $search) ||
                               haskeywords($content, DESCRIPTION, $search) ||
                               haskeywords($content, TITLE, $search)) {
                             $result = $content;
                           }

                           return $result;
                         });
    }

    protected static function prepareMetadata($metadata) {
      $metadata->set(static::SEARCH, strtolower($metadata->get(static::SEARCH)));

      return $metadata;
    }

    // INTERFACE FUNCTIONS

    public static function getContentPost($metadata, &$pagecount) {
      return null;
    }

    public static function getUriPost($metadata) {
      return gc(ROOTURI, null)."search".US;
    }

    public static function parseUriPost($uri) {
      $result = null;

      $metadata = preparecontent(parseuri($uri, static::REGEXPOST));
      if ($metadata instanceof Content) {
        $result = $metadata;
      }

      return $result;
    }

    // RUNTIME FUNCTIONS

    public static function runPost() {
      $result = false;

      $metadata = static::parseUriPost(relativeuri());
      if (null !== $metadata) {
        // check if the URI is correct
        $fixed = static::getUriPost($metadata);
        if (0 !== strcmp(gc(URI, null), $fixed)) {
          relocate($fixed.querystring(), false, true);
        } else {
          // prepare the post parameter
          if (array_key_exists(static::SEARCH, $_POST)) {
            $search = preg_replace("~[^0-9A-Za-z\_\-\.]~", "", preg_replace("~\s+~", DOT, $_POST[static::SEARCH]));

            $metadata = new Content();
            $metadata->set(static::SEARCH, $search);

            // retrieve URI
            $uri = static::getUri($metadata);
            if (null !== $uri) {
              relocate($uri.querystring(), false, false);
            }
          }
        }

        // we handled this page
        $result = true;
      }

      return $result;
    }

  }

  // register handler
  Handlers::register(SearchAddon::class, "run",     SearchAddon::REGEX,     [GET, POST], ERROR_SYSTEM);
  Handlers::register(SearchAddon::class, "runPost", SearchAddon::REGEXPOST, [POST],      ERROR_SYSTEM);
