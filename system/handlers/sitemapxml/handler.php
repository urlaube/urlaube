<?php

  /**
    This is the SitemapXmlHandler class of the urlau.be CMS.

    This file contains the SitemapXmlHandler class of the urlau.be CMS. The
    sitemap.xml handler generates a sitemap file.

    @package urlaube\urlaube
    @version 0.1a7
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class SitemapXmlHandler extends BaseSingleton implements Handler {

    // CONSTANTS

    const REGEX = "~^\/sitemap\.xml$~";

    // INTERFACE FUNCTIONS

    public static function getContent($metadata, &$pagecount) {
      $pagecount = 1;

      return FilePlugin::loadContentDir(USER_CONTENT_PATH, false,
                                        function ($content) {
                                          $result = null;

                                          // check that $content is not hidden
                                          if (!istrue(value($content, HIDDEN))) {
                                            // check that $content is not hidden from sitemap
                                            if (!istrue(value($content, HIDDENFROMSITEMAP))) {
                                              // check that $content is not a relocation
                                              if (null === value($content, RELOCATE)) {
                                                $result = $content;
                                              }
                                            }
                                          }

                                          return $result;
                                        },
                                        true);
    }

    public static function getUri($metadata) {
      return value(Main::class, ROOTURI)."sitemap.xml";
    }

    public static function parseUri($uri) {
      $result = null;

      $metadata = preparecontent(parseuri($uri, static::REGEX));
      if ($metadata instanceof Content) {
        $result = $metadata;
      }

      return $result;
    }

    // RUNTIME FUNCTIONS

    public static function run() {
      $result = false;

      $metadata = static::parseUri(relativeuri());
      if (null !== $metadata) {
        // set the metadata to be processed by plugins
        Main::set(METADATA, $metadata);

        // check if the URI is correct
        $fixed = static::getUri($metadata);
        if (0 !== strcmp(value(Main::class, URI), $fixed)) {
          relocate($fixed, false, true);

          // we handled this page
          $result = true;
        } else {
          $content = static::getContent($metadata, $pagecount);
          if (null !== $content) {
            // filter the content
            $content = preparecontent(Plugins::run(FILTER_CONTENT, true, $content));

            // set the content type
            header("Content-Type: application/xml");

            // return a minimalistic sitemap.xml
            print(fhtml("<?xml version=\"1.0\" encoding=\"UTF-8\"?>".NL.
                        "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">".NL.
                        "  <url>".NL.
                        "    <loc>%s</loc>".NL.
                        "    <changefreq>daily</changefreq>".NL.
                        "    <priority>1.0</priority>".NL.
                        "  </url>".NL,
                        absoluteurl("/")));

            if (is_array($content)) {
              foreach ($content as $content_item) {
                print(fhtml("  <url>".NL.
                            "    <loc>%s</loc>".NL.
                            "    <lastmod>%s</lastmod>".NL.
                            "    <changefreq>monthly</changefreq>".NL.
                            "    <priority>0.5</priority>".NL.
                            "  </url>".NL,
                            absoluteurl(value($content_item, URI)),
                            date("Y-m-d", filemtime(value($content_item, FILE)))));
              }
            }

            print("</urlset>");

            // we handled this page
            $result = true;
          }
        }
      }

      return $result;
    }

  }

  // register handler
  Handlers::register(SitemapXmlHandler::class, "run", SitemapXmlHandler::REGEX, [GET, POST], ADDSLASH_SYSTEM);
