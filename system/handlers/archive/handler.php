<?php

  /**
    This is the ArchiveHandler class of the urlau.be CMS.

    This file contains the ArchiveHandler class of the urlau.be CMS. The
    archive handler lists all pages that contain a certain date.

    @package urlaube\urlaube
    @version 0.1a6
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists(ARCHIVE_HANDLER)) {
    class ArchiveHandler extends Base implements Handler {

      // INTERFACE FUNCTIONS

      public static function getContent($info) {
        $result = null;

        if (is_array($info)) {
          $year = null;
          if (isset($info[YEAR]) && is_numeric($info[YEAR])) {
            $year = $info[YEAR];
          }

          $month = null;
          if (isset($info[MONTH]) && is_numeric($info[MONTH])) {
            $month = $info[MONTH];
          }

          $day = null;
          if (isset($info[DAY]) && is_numeric($info[DAY])) {
            $day = $info[DAY];
          }

          $page = 1;
          if (isset($info[PAGE]) && is_numeric($info[PAGE])) {
            if (0 < $info[PAGE]) {
              $page = $info[PAGE];
            }
          }

          $result = File::loadContentDir(USER_CONTENT_PATH, false,
                                         function ($content) use ($year, $month, $day) {
                                           $result = null;

                                           // check that $content is not hidden
                                           if (!istrue(value($content, HIDDEN))) {
                                             // check that $content is not a redirect
                                             if (null === value($content, REDIRECT)) {
                                               // check that $content has the $year, $month and $day
                                               if (hasdate($content, $year, $month, $day)) {
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
        $result = Main::ROOTURI()."archive".US;

        if (is_array($info)) {
          if (isset($info[YEAR]) && is_numeric($info[YEAR])) {
            $result .= $info[YEAR].US;

            if (isset($info[MONTH]) && is_numeric($info[MONTH])) {
              $result .= $info[MONTH].US;

              if (isset($info[DAY]) && is_numeric($info[DAY])) {
                $result .= $info[DAY].US;
              }
            }
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

        if (1 === preg_match("@^\/archive\/(?:([0-9]+)\/)(?:([0-9]+)\/)?(?:([0-9]+)\/)?(?:page\=([0-9]+)\/)?$@",
                             $uri, $matches)) {
          $result = array();

          // get the requested archive year
          if (2 <= count($matches)) {
            if (is_numeric($matches[1])) {
              $result[YEAR] = intval($matches[1]);
            }
          }

          // get the requested archive month
          if (3 <= count($matches)) {
            if (is_numeric($matches[2])) {
              $result[MONTH] = intval($matches[2]);
            }
          }

          // get the requested archive day
          if (4 <= count($matches)) {
            if (is_numeric($matches[3])) {
              $result[DAY] = intval($matches[3]);
            }
          }

          // get the requested page number
          if (5 <= count($matches)) {
            if (is_numeric($matches[4])) {
              $result[PAGE] = intval($matches[4]);
            }
          }
        }

        return $result;
      }

      // RUNTIME FUNCTIONS

      public static function handle() {
        $result = false;

        if (!Handlers::get(DEACTIVATE_ARCHIVE)) {
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
    Handlers::preset(DEACTIVATE_ARCHIVE, false);

    // register handler
    Handlers::register(ARCHIVE_HANDLER, "handle",
                       "@^\/archive\/(([0-9]+)\/)(?:([0-9]+)\/)?(?:([0-9]+)\/)?(?:page\=([0-9]+)\/)?$@",
                       [GET, POST], SYSTEM);
  }

