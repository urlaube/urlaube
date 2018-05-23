<?php

  /**
    This is the ErrorHandler class of the urlau.be CMS.

    This file contains the ErrorHandler class of the urlau.be CMS. The error handler is a catch-all handler that is
    activated when no fitting handler is found.

    @package urlaube\urlaube
    @version 0.1a1
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists("ErrorHandler")) {
    class ErrorHandler extends Translatable implements Handler, Translation {

      // INTERFACE FUNCTIONS

      public static function getContent($info) {
        $result = new Content();

        $result->set(CONTENT, "<p>".html(gl("Die gewünschte Seite wurde leider nicht gefunden."))."</p>".NL.
                               "<p>".html(gl("Möchtest du stattdessen zur "))."<a href=\"".html(Main::ROOTURI()).
                               "\">".html(gl("Startseite"))."</a>".html(gl(" gehen?"))."</p>");
        $result->set(TITLE,   gl("Nichts gefunden..."));

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

      public function handle() {
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

    // instantiate translatable handler
    $handler = new ErrorHandler();
    $handler->setTranslationsPath(__DIR__.DS."lang".DS);

    // register handler
    Handlers::register($handler, "handle",
                       "@^.*$@",
                       [GET, POST], ERROR);
  }

