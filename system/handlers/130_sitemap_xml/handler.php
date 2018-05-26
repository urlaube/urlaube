<?php

  /**
    This is the SitemapXmlHandler class of the urlau.be CMS.

    This file contains the SitemapXmlHandler class of the urlau.be CMS. The
    sitemap.xml handler generates a sitemap file.

    @package urlaube\urlaube
    @version 0.1a2
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists("SitemapXmlHandler")) {
    class SitemapXmlHandler implements Handler {

      // INTERFACE FUNCTIONS

      public static function getContent($info) {
        return File::loadContentDir(USER_CONTENT_PATH, false,
                                    function ($content) {
                                      $result = null;

                                      // check that $content is not hidden
                                      if (!ishidden($content)) {
                                        $result = $content;
                                      }

                                      return $result;
                                    },
                                    true);
      }

      public static function getUri($info) {
        return Main::ROOTURI()."sitemap.xml";
      }

      public static function parseUri($uri) {
        $result = null;

        if (is_string($uri)) {
          if (1 === preg_match("@^\/sitemap\.xml$@",
                               $uri, $matches)) {
            $result = array();
          }
        }

        return $result;
      }

      // RUNTIME FUNCTIONS

      public static function handle() {
        $result = false;

        if (!Handlers::get(DEACTIVATE_SITEMAP_XML)) {
          $info = static::parseUri(Main::RELATIVEURI());
          if (null !== $info) {
            // set the content type
            header("Content-Type: application/xml");

            // return a minimalistic sitemap.xml
            print("<?xml version=\"1.0\" encoding=\"UTF-8\"?>".NL.
                  "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">".NL.
                  "  <url>".NL.
                  "    <loc>".Main::PROTOCOL().Main::HOSTNAME().Main::ROOTURI()."</loc>".NL.
                  "    <changefreq>daily</changefreq>".NL.
                  "    <priority>1.0</priority>".NL.
                  "  </url>".NL);

            $content = static::getContent($info);
            if (null !== $content) {
              foreach ($content as $content_item) {
                print("  <url>".NL.
                      "    <loc>".Main::PROTOCOL().Main::HOSTNAME().$content_item->get(URI)."</loc>".NL.
                      "    <lastmod>".date("Y-m-d", filemtime($content_item->get(FILE)))."</lastmod>".NL.
                      "    <changefreq>monthly</changefreq>".NL.
                      "    <priority>0.5</priority>".NL.
                      "  </url>".NL);
              }
            }

            print("</urlset>");

            // we handled this page
            $result = true;
          }
        }

        return $result;
      }

    }

    // activate handler by default
    Handlers::preset(DEACTIVATE_SITEMAP_XML, false);

    // register handler
    Handlers::register("SitemapXmlHandler", "handle",
                       "@^\/sitemap\.xml$@",
                       [GET], BEFORE_ADDSLASH);
  }

