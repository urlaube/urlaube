<?php

  /**
    This is the Redirect class of the urlau.be CMS.

    This file contains the Redirect class of the urlau.be CMS. This plugin provides a redirect feature that is
    available through fields in the content files.

    @package urlaube\urlaube
    @version 0.1a5
    @author  Yahe <hello@yahe.sh>
    @since   0.1a5
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists("Redirect")) {
    class Redirect extends Base implements Plugin {

      // RUNTIME FUNCTIONS

      public static function handle($content) {
        // check if $content is an array with a single entry,
        // if that's the case and it's a redirect then unpack the array
        if (is_array($content)) {
          if (1 === count($content)) {
            if ($content[0] instanceof Content) {
              if (isredirect($content[0])) {
                $content = $content[0];
              }
            }
          }
        }

        if ($content instanceof Content) {
          // check if the content is a redirect
          if (isredirect($content)) {
            // execute the redirect
            redirect(trim($content->get(REDIRECT)),
                     ((!$content->isset(REDIRECTTYPE)) || (!ispermanent($content->get(REDIRECTTYPE)))));

            // abort the execution to save time
            exit();
          }
        }

        return $content;
      }

    }

    // register plugin
    Plugins::register("Redirect", "handle", FILTER_CONTENT);
  }

