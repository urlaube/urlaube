<?php

  /**
    This is the StickyPlugin class of the urlau.be CMS.

    This file contains the StickyPlugin class of the urlau.be CMS. This plugin
    resorts content so that sticky entries are at always at the top.

    @package urlaube\urlaube
    @version 0.1a9
    @author  Yahe <hello@yahe.sh>
    @since   0.1a6
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class StickyPlugin extends BaseSingleton implements Plugin {

    // RUNTIME FUNCTIONS

    public static function run($content) {
      $result = $content;

      if ((ArchiveHandler::class === Handlers::getActive()) ||
          ((FeedHandler::class === Handlers::getActive()) &&
           (ArchiveHandler::class === value(value(Main::class, METADATA), FeedHandler::FEED)))) {
        $result = sortcontent($result, STICKY,
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

      return $result;
    }

  }

  // register plugin
  Plugins::register(StickyPlugin::class, "run", FILTER_PAGINATE);
