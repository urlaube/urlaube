<?php

  /**
    This is the PageHandler class of the urlau.be CMS.

    This file contains the PageHandler class of the urlau.be CMS. The page handler provides access to a single page
    stored in a file.

    @package urlaube\urlaube
    @version 0.1a4
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists(PAGE_HANDLER)) {
    class PageHandler extends Base implements Handler {

      // INTERFACE FUNCTIONS

      public static function getContent($info) {
        $result = null;

        if (is_array($info)) {
          $name = null;
          if (isset($info[NAME]) && is_string($info[NAME])) {
            $name = $info[NAME];
          }

          // KNOWN EDGE CASE:
          // In order to allow static start pages the PagesHandler reacts on
          // the relative URI "/". $path will evaluate to the following result:
          // $path = USER_CONTENT_PATH.CONTENT_FILE_EXT;
          // This is expected behaviour.
          $path = USER_CONTENT_PATH.
                  implode(DS, array_filter(explode(US, $name))).
                  CONTENT_FILE_EXT;

          $result = File::loadContent($path, false,
                                      function ($content) {
                                        $result = null;

                                        // check that $content is not hidden
                                        if (!ishidden($content)) {
                                          $result = $content;
                                        }

                                        return $result;
                                      });

          // set pagination information
          Main::PAGEMAX(1);
          Main::PAGEMIN(1);
          Main::PAGENUMBER(1);
        }

        return $result;
      }

      public static function getUri($info) {
        $result = Main::ROOTURI();

        if (is_array($info)) {
          if (isset($info[NAME]) && is_string($info[NAME])) {
            $result .= $info[NAME];
          }
        }

        return $result;
      }

      public static function parseUri($uri) {
        $result = null;

        if (1 === preg_match("@^\/([0-9A-Za-z\_\-\/]*)$@",
                             $uri, $matches)) {
          $result = array();

          // get the requested content name
          if (2 <= count($matches)) {
            $result[NAME] = $matches[1];
          }
        }

        return $result;
      }

      // RUNTIME FUNCTIONS

      public static function handle() {
        $result = false;

        if (!Handlers::get(DEACTIVATE_PAGE)) {
          $info = static::parseUri(Main::RELATIVEURI());
          if (null !== $info) {
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

        return $result;
      }

    }

    // activate handler by default
    Handlers::preset(DEACTIVATE_PAGE, false);

    // register handler
    Handlers::register(PAGE_HANDLER, "handle",
                       "@^\/[0-9A-Za-z\_\-\/]*$@",
                       [GET], SYSTEM);
  }

