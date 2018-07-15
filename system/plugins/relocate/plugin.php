<?php

  /**
    This is the Relocate class of the urlau.be CMS.

    This file contains the Relocate class of the urlau.be CMS. This plugin provides a relocation feature that is
    available through fields in the content files.

    @package urlaube\urlaube
    @version 0.1a6
    @author  Yahe <hello@yahe.sh>
    @since   0.1a5
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists("Relocate")) {
    class Relocate extends Base implements Plugin {

      // HELPER FUNCTIONS

      // check if the given $string represents a permanent redirect state
      protected static function ispermanent($string) {
        $string = strtolower(trim($string));

        return ((0 === strcmp($string, "301")) ||
                (0 === strcmp($string, "308")) ||
                (0 === strcmp($string, "permanent")) ||
                (0 === strcmp($string, "permanently")));
      }

      // check if the given $string represents a redirect state (in comparison to a move state)
      protected static function isredirect($string) {
        $string = strtolower(trim($string));

        return ((0 === strcmp($string, "307")) ||
                (0 === strcmp($string, "308")) ||
                (0 === strcmp($string, "redirect")) ||
                (0 === strcmp($string, "redirection")));
      }

      // RUNTIME FUNCTIONS

      public static function handle($content) {
        // check if $content is an array with a single entry then unpack the array
        if (is_array($content)) {
          if (1 === count($content)) {
            $content = $content[0];
          }
        }

        if ($content instanceof Content) {
          // get the target URL
          $value = value($content, RELOCATE);
          if (null !== $value) {
            // default is a moved temporarily
            $permanent = false;
            $redirect  = false;

            // get the relocation type
            $type = value($content, RELOCATETYPE);
            if (null !== $type) {
              $type = explode(SP, $type);
              foreach ($type as $type_item) {
                $permanent = $permanent || static::ispermanent($type_item);
                $redirect  = $redirect || static::isredirect($type_item);
              }
            }

            // execute the redirect
            relocate($value, $permanent, $redirect);

            // abort the execution to save time
            exit();
          }
        }

        return $content;
      }

    }

    // register plugin
    Plugins::register("Relocate", "handle", FILTER_CONTENT);
  }

