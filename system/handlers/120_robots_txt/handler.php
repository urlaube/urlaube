<?php

  /**
    This is the RobotsTxtHandler class of the urlau.be CMS.

    This file contains the RobotsTxtHandler class of the urlau.be CMS. The robots.txt handler generates static file
    contents for certain typically provided files.

    @package urlaube\urlaube
    @version 0.1a5
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists(ROBOTS_TXT_HANDLER)) {
    class RobotsTxtHandler extends Base implements Handler {

      // INTERFACE FUNCTIONS

      public static function getContent($info) {
        return null;
      }

      public static function getUri($info) {
        return Main::ROOTURI()."robots.txt";
      }

      public static function parseUri($uri) {
        $result = null;

        if (is_string($uri)) {
          if (1 === preg_match("@^\/robots\.txt$@",
                               $uri, $matches)) {
            $result = array();
          }
        }

        return $result;
      }

      // RUNTIME FUNCTIONS

      public static function handle() {
        $result = false;

        if (!Handlers::get(DEACTIVATE_ROBOTS_TXT)) {
          $info = static::parseUri(Main::RELATIVEURI());
          if (null !== $info) {
            // set the content type
            header("Content-Type: text/plain");

            // return a minimalistic robots.txt
            print("User-agent: *".NL.
                  "Disallow:".NL);

            if (!Handlers::get(DEACTIVATE_SITEMAP_XML)) {
              print(NL.
                    "Sitemap: ".Main::PROTOCOL().Main::HOSTNAME().SitemapXmlHandler::getUri(array()));
            }

            // we handled this page
            $result = true;
          }
        }

        return $result;
      }

    }

    // activate handler by default
    Handlers::preset(DEACTIVATE_ROBOTS_TXT, false);

    // register handler
    Handlers::register(ROBOTS_TXT_HANDLER, "handle",
                       "@^\/robots\.txt$@",
                       [GET], BEFORE_ADDSLASH);
  }

