<?php

  /**
    This is the SearchGetHandler class of the urlau.be CMS.

    This file contains the SearchGetHandler class of the urlau.be CMS. The
    search-get handler lists all pages that contain a certain search keyword.

    @package urlaube\urlaube
    @version 0.1a3
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists(SEARCH_GET_HANDLER)) {
    class SearchGetHandler implements Handler {

      // INTERFACE FUNCTIONS

      public static function getContent($info) {
        $result = null;

        if (is_array($info)) {
          $search = null;
          if (isset($info[SEARCH]) && is_array($info[SEARCH])) {
            $search = $info[SEARCH];
          }

          $page = 1;
          if (isset($info[PAGE]) && is_numeric($info[PAGE])) {
            if (0 < $info[PAGE]) {
              $page = $info[PAGE];
            }
          }

          $result = File::loadContentDir(USER_CONTENT_PATH, false,
                                         function ($content) use ($search) {
                                           $result = null;

                                           // check that $content is not hidden
                                           if (!ishidden($content)) {
                                             // check that $content contains $keywords
                                             if (findkeywords($content, AUTHOR, $search) ||
                                                 findkeywords($content, CATEGORY, $search) ||
                                                 findkeywords($content, CONTENT, $search) ||
                                                 findkeywords($content, DATE, $search) ||
                                                 findkeywords($content, DESCRIPTION, $search) ||
                                                 findkeywords($content, TITLE, $search)) {
                                               $result = $content;
                                             }
                                           }

                                           return $result;
                                         },
                                         true);

          // sort entries by DATE
          $result = sortcontent($result, DATE,
                                function($left, $right) {
                                  // reverse-sort
                                  return -datecmp($left, $right);
                                });

          // set pagination information
          Main::PAGEMAX(ceil(count($result)/Main::PAGESIZE()));
          Main::PAGEMIN(1);
          Main::PAGENUMBER($page);

          // do pagination
          $result = paginate($result, $page);
        }

        return $result;
      }

      public static function getUri($info) {
        $result = Main::ROOTURI()."search".US;

        if (is_array($info)) {
          if (isset($info[SEARCH]) && is_array($info[SEARCH])) {
            $result .= urlencode(implode(".", $info[SEARCH])).US;
          }

          if (isset($info[PAGE]) && is_numeric($info[PAGE])) {
            if (1 !== $info[PAGE]) {
              $result .= "page=".$info[PAGE].US;
            }
          }
        }

        return $result;
      }

      public static function parseUri($uri) {
        $result = null;

        if (1 === preg_match("@^\/search\/([0-9A-Za-z\_\-\.]+)\/(?:page\=([0-9]+)\/)?$@",
                             $uri, $matches)) {
          $result = array();

          // get the requested search string
          if (2 <= count($matches)) {
            $result[SEARCH] = explode(".", $matches[1]);
          }

          // get the requested page number
          if (3 <= count($matches)) {
            if (is_numeric($matches[2])) {
              $result[PAGE] = intval($matches[2]);
            }
          }
        }

        return $result;
      }

      // RUNTIME FUNCTIONS

      public static function handle() {
        $result = false;

        if (!Handlers::get(DEACTIVATE_SEARCH)) {
          $info = static::parseUri(Main::RELATIVEURI());
          if (null !== $info) {
            // check if the URI is correct
            $fixed = static::getUri($info);
            if (0 !== strcmp(Main::URI(), $fixed)) {
              redirect($fixed);

              // we handled this page
              $result = true;
            } else {
              $content = static::getContent($info);
              if (null !== $content) {
                // set the content to be processed by the theme
                Main::CONTENT($content);
                Main::PAGEINFO($info);

                // transfer the handling to the Themes class 
                Themes::run();

                // we handled this page
                $result = true;
              }
            }
          }
        }

        return $result;
      }

    }

    // activate handler by default
    Handlers::preset(DEACTIVATE_SEARCH, false);

    // register handler
    Handlers::register(SEARCH_GET_HANDLER, "handle",
                       "@^\/search\/([0-9A-Za-z\_\-\.]+)\/(?:page\=([0-9]+)\/)?$@",
                       [GET], SYSTEM);
  }

