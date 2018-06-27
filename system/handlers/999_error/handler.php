<?php

  /**
    This is the ErrorHandler class of the urlau.be CMS.

    This file contains the ErrorHandler class of the urlau.be CMS. The error handler is a catch-all handler that is
    activated when no fitting handler is found.

    @package urlaube\urlaube
    @version 0.1a5
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists(ERROR_HANDLER)) {
    class ErrorHandler extends Base implements Handler {

      // INTERFACE FUNCTIONS

      public static function getContent($info) {
        $result = new Content();

        $result->set(CONTENT, tfhtml("<p>%s</p>".NL.
                                     "<p>%s <a href=\"%s\">%s</a> %s</p>",
                                     ERROR_HANDLER,
                                     "Die gewünschte Seite wurde leider nicht gefunden.",
                                     "Möchtest du stattdessen zur",
                                     Main::ROOTURI(),
                                     "Startseite",
                                     "gehen?"));
        $result->set(TITLE,   t("Nichts gefunden...", ERROR_HANDLER));

        // set pagination information
        Main::PAGEMAX(1);
        Main::PAGEMIN(1);
        Main::PAGENUMBER(1);

        return $result;
      }

      public static function getUri($info) {
        return null;
      }

      public static function parseUri($uri) {
        return null;
      }

      // RUNTIME FUNCTIONS

      public static function handle() {
        $result = false;

        if (!Handlers::get(DEACTIVATE_ERROR)) {
          // return a 404 return code
          http_response_code(404);

          $content = static::getContent(null);
          if (null !== $content) {
            // set the content to be processed by the theme
            Main::CONTENT($content);

            // transfer the handling to the Themes class
            Themes::run();

            // we handled this page
            $result = true;
          }
        }

        return $result;
      }

    }

    // activate handler by default
    Handlers::preset(DEACTIVATE_ERROR, false);


    // register handler
    Handlers::register(ERROR_HANDLER, "handle",
                       "@^.*$@",
                       [GET, POST], ERROR);

    // register the translation
    Translate::register(__DIR__.DS."lang".DS, ERROR_HANDLER);
  }

