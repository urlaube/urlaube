<?php

  /**
    This is the StickyPlugin class of the urlau.be CMS.

    This file contains the StickyPlugin class of the urlau.be CMS. This plugin
    resorts content so that sticky entries are at always at the top.

    @package urlaube\urlaube
    @version 0.1a10
    @author  Yahe <hello@yahe.sh>
    @since   0.1a6
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class StickyPlugin extends BaseSingleton implements Plugin {

    // CONSTANTS

    const STICKY = "sticky";

    // RUNTIME FUNCTIONS

    public static function run($content) {
      $result = $content;

      if ((ArchiveHandler::class === Handlers::getActive()) ||
          ((FeedHandler::class === Handlers::getActive()) &&
           (ArchiveHandler::class === value(value(Main::class, METADATA), FeedHandler::FEED)))) {
        $result = sortcontent($result, static::STICKY,
                              function ($left, $right) {
                                // either both are sticky or unsticky, don't resort
                                $result = 0;

                                if (istrue(value($left, static::STICKY))) {
                                  if (!istrue(value($right, static::STICKY))) {
                                    // only the left one is sticky, it should come first
                                    $result = -1;
                                  }
                                } else {
                                  if (istrue(value($right, static::STICKY))) {
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
