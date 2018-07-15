<?php

  /**
    This is the Sticky class of the urlau.be CMS.

    This file contains the Sticky class of the urlau.be CMS. This plugin resorts content so that sticky entries are
    at always at the top

    @package urlaube\urlaube
    @version 0.1a6
    @author  Yahe <hello@yahe.sh>
    @since   0.1a6
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists("Sticky")) {
    class Sticky extends Base implements Plugin {

      // RUNTIME FUNCTIONS

      public static function handle($content) {
        return sortcontent($content, STICKY,
                           function ($left, $right) {
                             // either both are sticky or unsticky, don't resort
                             $result = 0;

                             if (istrue(value($left, STICKY))) {
                               if (!istrue(value($right, STICKY))) {
                                 // only the left one is sticky, it should come first
                                 $result = -1;
                               }
                             } else {
                               if (istrue(value($right, STICKY))) {
                                 // only the right one is sticky, it should come first
                                 $result = 1;
                               }
                             }

                             return $result;
                           });
      }

    }

    // register plugin
    Plugins::register("Sticky", "handle", FILTER_CONTENT);
  }

