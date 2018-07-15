<?php

  /**
    This is the CategoryHandler class of the urlau.be CMS.

    This file contains the CategoryHandler class of the urlau.be CMS. The
    category handler lists all pages that contain a certain category
    identifier.

    @package urlaube\urlaube
    @version 0.1a6
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists(CATEGORY_HANDLER)) {
    class CategoryHandler extends Base implements Handler {

      // INTERFACE FUNCTIONS

      public static function getContent($info) {
        $result = null;

        if (is_array($info)) {
          $category = null;
          if (isset($info[CATEGORY]) && is_string($info[CATEGORY])) {
            $category = $info[CATEGORY];
          }

          $page = 1;
          if (isset($info[PAGE]) && is_numeric($info[PAGE])) {
            if (0 < $info[PAGE]) {
              $page = $info[PAGE];
            }
          }

          $result = File::loadContentDir(USER_CONTENT_PATH, false,
                                         function ($content) use ($category) {
                                           $result = null;

                                           // check that $content is not hidden
                                           if (!istrue(value($content, HIDDEN))) {
                                             // check that $content is not a redirect
                                             if (null === value($content, REDIRECT)) {
                                               // check that $content has the $category
                                               if (hascategory($content, $category)) {
                                                 $result = $content;
                                               }
                                             }
                                           }

                                           return $result;
                                         },
                                         true);

         // sort entries by DATE
         $result = sortcontent($result, DATE,
                               function ($left, $right) {
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
        $result = Main::ROOTURI()."category".US;

        if (is_array($info)) {
          if (isset($info[CATEGORY]) && is_string($info[CATEGORY])) {
            $result .= urlencode(strtolower($info[CATEGORY])).US;
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

        if (1 === preg_match("@^\/category\/([0-9A-Za-z\_\-]+)\/(?:page\=([0-9]+)\/)?$@",
                             $uri, $matches)) {
          $result = array();

          // get the requested category name
          if (2 <= count($matches)) {
            $result[CATEGORY] = $matches[1];
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

        if (!Handlers::get(DEACTIVATE_CATEGORY)) {
          $info = static::parseUri(Main::RELATIVEURI());
          if (null !== $info) {
            // check if the URI is correct
            $fixed = static::getUri($info);
            if (0 !== strcmp(Main::URI(), $fixed)) {
              relocate($fixed, false, true);

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
    Handlers::preset(DEACTIVATE_CATEGORY, false);

    // register handler
    Handlers::register(CATEGORY_HANDLER, "handle",
                       "@^\/category\/([0-9A-Za-z\_\-]+)\/(?:page\=([0-9]+)\/)?$@",
                       [GET, POST], SYSTEM);
  }

