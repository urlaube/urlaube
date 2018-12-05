<?php

  /**
    This is the RelocatePlugin class of the urlau.be CMS.

    This file contains the RelocatePlugin class of the urlau.be CMS. This plugin
    provides a relocation feature that is available through fields in the
    content files.

    @package urlaube\urlaube
    @version 0.1a11
    @author  Yahe <hello@yahe.sh>
    @since   0.1a5
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class RelocatePlugin extends BaseSingleton implements Plugin {

    // CONSTANTS

    const RELOCATE     = "relocate";
    const RELOCATETYPE = "relocatetype";

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

    public static function run($content) {
      $result = $content;

      // if $result is an array with a single entry then unpack it
      if (is_array($result)) {
        if (1 === count($result)) {
          $result = $result[0];
        }
      }

      if ($result instanceof Content) {
        // get the target URL
        $value = value($result, static::RELOCATE);
        if (null !== $value) {
          // default is a moved temporarily
          $permanent = false;
          $redirect  = false;

          // get the relocation type
          $type = value($result, static::RELOCATETYPE);
          if (null !== $type) {
            $type = explode(SP, $type);
            foreach ($type as $type_item) {
              $permanent = $permanent || static::ispermanent($type_item);
              $redirect  = $redirect || static::isredirect($type_item);
            }
          }

          // execute the relocation
          relocate($value, $permanent, $redirect);

          // abort the execution to save time
          exit();
        }
      } else {
        if (is_array($result)) {
          // iterate through all content items
          foreach ($result as $key => $value) {
            if ($value instanceof Content) {
              // hide relocations in a list of content items
              if (null !== value($value, static::RELOCATE)) {
                unset($result[$key]);
              }
            }
          }
        }
      }

      return $result;
    }

  }

  // register plugin
  Plugins::register(RelocatePlugin::class, "run", FILTER_CONTENT);
