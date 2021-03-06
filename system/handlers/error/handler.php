<?php

  /**
    This is the ErrorHandler class of the urlau.be CMS.

    This file contains the ErrorHandler class of the urlau.be CMS. The error
    handler is a catch-all handler that is activated when no fitting handler is
    found.

    @package urlaube\urlaube
    @version 0.1a12
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class ErrorHandler extends BaseSingleton implements Handler {

    // CONSTANTS

    const REGEX = "~^.*$~";

    // INTERFACE FUNCTIONS

    public static function getContent($metadata, &$pagecount) {
      $pagecount = 1;
      $result    = new Content();

      $result->set(CONTENT, tfhtml("<p>%s</p>".NL.
                                   "<p>%s <a href=\"%s\">%s</a> %s</p>",
                                   static::class,
                                   "Die gewünschte Seite wurde leider nicht gefunden.",
                                   "Möchtest du stattdessen zur",
                                   value(Main::class, ROOTURI),
                                   "Startseite",
                                   "gehen?"));
      $result->set(TITLE,   t("Nichts gefunden...", static::class));

      return $result;
    }

    public static function getUri($metadata) {
      return null;
    }

    public static function parseUri($uri) {
      return null;
    }

    // RUNTIME FUNCTIONS

    public static function run() {
      $result = false;

      // set the metadata to be processed by plugins and the theme
      Main::set(METADATA, new Content());

      $content = preparecontent(static::getContent(null, $pagecount));
      if (null !== $content) {
        // filter the content
        $content = preparecontent(Plugins::run(FILTER_CONTENT, true, $content));

        // return a 404 return code
        http_response_code(404);

        // set the content to be processed by plugins and the theme
        Main::set(CONTENT, $content);

        // handle pagination
        Main::set(PAGE,      1);
        Main::set(PAGECOUNT, 1);

        // transfer the handling to the Themes class
        Themes::run();

        // we handled this page
        $result = true;
      }

      return $result;
    }

  }

  // register handler
  Handlers::register(ErrorHandler::class, "run", ErrorHandler::REGEX, [GET, POST], ERROR_HANDLER);

  // register the translation
  Translate::register(__DIR__.DS."lang".DS, ErrorHandler::class);
