<?php

  /**
    This is the AuthorHandler class of the urlau.be CMS.

    This file contains the AuthorHandler class of the urlau.be CMS. The
    author handler lists all pages that are written by the given author.

    @package urlaube\urlaube
    @version 0.1a3
    @author  Yahe <hello@yahe.sh>
    @since   0.1a2
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists(AUTHOR_HANDLER)) {
    class AuthorHandler implements Handler {

      // INTERFACE FUNCTIONS

      public static function getContent($info) {
        $result = null;

        if (is_array($info)) {
          $author = null;
          if (isset($info[AUTHOR]) && is_string($info[AUTHOR])) {
            $author = $info[AUTHOR];
          }

          $page = 1;
          if (isset($info[PAGE]) && is_numeric($info[PAGE])) {
            if (0 < $info[PAGE]) {
              $page = $info[PAGE];
            }
          }

          $result = File::loadContentDir(USER_CONTENT_PATH, false,
                                         function ($content) use ($author) {
                                           $result = null;

                                           // check that $content is not hidden
                                           if (!ishidden($content)) {
                                             // check that $content has the $author
                                             if (hasauthor($content, $author)) {
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
        $result = Main::ROOTURI()."author".US;

        if (is_array($info)) {
          if (isset($info[AUTHOR]) && is_string($info[AUTHOR])) {
            $result .= urlencode(strtolower($info[AUTHOR])).US;
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

        if (1 === preg_match("@^\/author\/([0-9A-Za-z\_\-]+)\/(?:page\=([0-9]+)\/)?$@",
                             $uri, $matches)) {
          $result = array();

          // get the requested author name
          if (2 <= count($matches)) {
            $result[AUTHOR] = $matches[1];
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

        if (!Handlers::get(DEACTIVATE_AUTHOR)) {
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
    Handlers::preset(DEACTIVATE_AUTHOR, false);

    // register handler
    Handlers::register(AUTHOR_HANDLER, "handle",
                       "@^\/author\/([0-9A-Za-z\_\-]+)\/(?:page\=([0-9]+)\/)?$@",
                       [GET], SYSTEM);
  }

