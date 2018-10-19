<?php

  /**
    This is the SearchHandler class of the urlau.be CMS.

    This file contains the SearchHandler class of the urlau.be CMS. The search
    handler lists all pages that contain a certain search keyword.

    @package urlaube\urlaube
    @version 0.1a7
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class SearchHandler extends BaseHandler {

    // CONSTANTS

    const SEARCH = "search";

    const MANDATORY = [self::SEARCH];
    const OPTIONAL  = [PAGE => 1];
    const REGEX     = "~^\/".
                      "search\=(?P<search>[0-9A-Za-z\_\-\.]+)\/".
                      "(page\=(?P<page>[0-9]+)\/)?".
                      "$~";

    const REGEXPOST = "~^\/search\/$~";

    // ABSTRACT FUNCTIONS

    protected static function getResult($metadata) {
      $search = explode(DOT, value($metadata, static::SEARCH));

      return FilePlugin::loadContentDir(USER_CONTENT_PATH, false,
                                        function ($content) use ($search) {
                                          $result = null;

                                          // check that $content is not hidden
                                          if (!istrue(value($content, HIDDEN))) {
                                            // check that $content is not hidden from search
                                            if (!istrue(value($content, HIDDENFROMSEARCH))) {
                                              // check that $content is not a relocation
                                              if (null === value($content, RELOCATE)) {
                                                // check that $content contains $keywords
                                                if (haskeywords($content, AUTHOR, $search) ||
                                                    haskeywords($content, CATEGORY, $search) ||
                                                    haskeywords($content, CONTENT, $search) ||
                                                    haskeywords($content, DATE, $search) ||
                                                    haskeywords($content, DESCRIPTION, $search) ||
                                                    haskeywords($content, TITLE, $search)) {
                                                  $result = $content;
                                                }
                                              }
                                            }
                                          }

                                          return $result;
                                        },
                                        true);
    }

    // INTERFACE FUNCTIONS

    public static function getContentPost($metadata, &$pagecount) {
      return null;
    }

    public static function getUriPost($metadata) {
      return value(Main::class, ROOTURI)."search".US;
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
        if (0 !== strcmp(value(Main::class, URI), $fixed)) {
          relocate($fixed, false, true);

          // we handled this page
          $result = true;
        } else {
          // prepare the post parameter
          if (isset($_POST[static::SEARCH])) {
            $search = preg_replace("~[^0-9A-Za-z\_\-\.]~", "", preg_replace("~\s+~", DOT, $_POST[static::SEARCH]));

            $metadata = new Content();
            $metadata->set(static::SEARCH, $search);

            // retrieve URI
            $uri = static::getUri($metadata);
            if (null !== $uri) {
              relocate($uri, false, false);

              // we handled this page
              $result = true;
            }
          }
        }
      }

      return $result;
    }

  }

  // register handler
  Handlers::register(SearchHandler::class, "run",     SearchHandler::REGEX,     [GET, POST], PAGE_SYSTEM);
  Handlers::register(SearchHandler::class, "runPost", SearchHandler::REGEXPOST, [POST],      PAGE_SYSTEM);
