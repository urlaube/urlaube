<?php

  /**
    This is the RobotsTxtHandler class of the urlau.be CMS.

    This file contains the RobotsTxtHandler class of the urlau.be CMS. The
    robots.txt handler generates static file contents for certain typically
    provided files.

    @package urlaube\urlaube
    @version 0.1a12
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class RobotsTxtHandler extends BaseSingleton implements Handler {

    // CONSTANTS

    const REGEX = "~^\/robots\.txt$~";

    // INTERFACE FUNCTIONS

    public static function getContent($metadata, &$pagecount) {
      return null;
    }

    public static function getUri($metadata) {
      return value(Main::class, ROOTURI)."robots.txt";
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
        // check if the URI is correct
        $fixed = static::getUri($metadata);
        if (0 !== strcmp(value(Main::class, URI), $fixed)) {
          relocate($fixed.querystring(), false, true);
        } else {
          // set the content type
          header("Content-Type: text/plain");

          // return a minimalistic robots.txt
          print(fhtml("User-agent: *".NL.
                      "Disallow:".NL.
                      NL.
                      "User-agent: AdsBot-Google".NL.
                      "User-agent: Amazonbot".NL.
                      "User-agent: anthropic-ai".NL.
                      "User-agent: Applebot".NL.
                      "User-agent: Applebot-Extended".NL.
                      "User-agent: AwarioRssBot".NL.
                      "User-agent: AwarioSmartBot".NL.
                      "User-agent: Bytespider".NL.
                      "User-agent: CCBot".NL.
                      "User-agent: ChatGPT-User".NL.
                      "User-agent: ClaudeBot".NL.
                      "User-agent: Claude-Web".NL.
                      "User-agent: cohere-ai".NL.
                      "User-agent: DataForSeoBot".NL.
                      "User-agent: Diffbot".NL.
                      "User-agent: FacebookBot".NL.
                      "User-agent: FriendlyCrawler".NL.
                      "User-agent: Google-Extended".NL.
                      "User-agent: GoogleOther".NL.
                      "User-agent: GPTBot".NL.
                      "User-agent: img2dataset".NL.
                      "User-agent: ImagesiftBot".NL.
                      "User-agent: magpie-crawler".NL.
                      "User-agent: Meltwater".NL.
                      "User-agent: omgili".NL.
                      "User-agent: omgilibot".NL.
                      "User-agent: peer39_crawler".NL.
                      "User-agent: peer39_crawler/1.0".NL.
                      "User-agent: PerplexityBot".NL.
                      "User-agent: PiplBot".NL.
                      "User-agent: scoop.it".NL.
                      "User-agent: Seekr".NL.
                      "User-agent: YouBot".NL.
                      "Disallow: /".NL.
                      NL.
                      "Sitemap: %s",
                      absoluteurl(SitemapXmlHandler::getUri(new Content()))));
        }

        // we handled this page
        $result = true;
      }

      return $result;
    }

  }

  // register handler
  Handlers::register(RobotsTxtHandler::class, "run", RobotsTxtHandler::REGEX, [GET, POST], ADDSLASH_SYSTEM);
