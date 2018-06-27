<?php

  /**
    This is the HomeHandler class of the urlau.be CMS.

    This file contains the HomeHandler class of the urlau.be CMS. The
    home handler lists all pages that are not flagged to be hidden from the home page.

    @package urlaube\urlaube
    @version 0.1a5
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists(HOME_HANDLER)) {
    class HomeHandler extends Base implements Handler {

      // INTERFACE FUNCTIONS

      public static function getContent($info) {
        $result = null;

        if (is_array($info)) {
          $page = 1;
          if (isset($info[PAGE]) && is_numeric($info[PAGE])) {
            if (0 < $info[PAGE]) {
              $page = $info[PAGE];
            }
          }

          $result = File::loadContentDir(USER_CONTENT_PATH, false,
                                         function ($content) {
                                           $result = null;

                                           // check that $content is not hidden
                                           if (!ishidden($content)) {
                                             // check that $content is not a redirect
                                             if (!isredirect($content)) {
                                               // check that $content is not hidden from home
                                               if (!ishiddenfromhome($content)) {
                                                 $result = $content;
                                               }
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
        $result = Main::ROOTURI();

        if (is_array($info)) {
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

        if (1 === preg_match("@^\/(?:page\=([0-9]+)\/)?$@",
                             $uri, $matches)) {
          $result = array();

          // get the requested page number
          if (2 <= count($matches)) {
            if (is_numeric($matches[1])) {
              $result[PAGE] = intval($matches[1]);
            }
          }
        }

        return $result;
      }

      // RUNTIME FUNCTIONS

      public static function handle() {
        $result = false;

        if (!Handlers::get(DEACTIVATE_HOME)) {
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
    Handlers::preset(DEACTIVATE_HOME, false);

    // register handler
    Handlers::register(HOME_HANDLER, "handle",
                       "@^\/(?:page\=([0-9]+)\/)?$@",
                       [GET], SYSTEM);
  }

