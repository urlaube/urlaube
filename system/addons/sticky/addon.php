<?php

  /**
    This is the StickyAddon class of the urlau.be CMS.

    This addon resorts content so that sticky entries are at always at the top.

    @package urlaube/urlaube
    @version 0.2a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a6
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class StickyAddon extends BaseSingleton implements Plugin {

    // CONSTANTS

    const STICKY = "sticky";

    // RUNTIME FUNCTIONS

    public static function run($content) {
      $result = $content;

      $handler = gethandler();
      if ((null !== $handler) &&
          ((ArchiveAddon::class === $handler[ENTITY]) ||
           ((FeedAddon::class === $handler[ENTITY]) && (ArchiveAddon::class === gv(gc(METADATA, null), FeedAddon::FEED))))) {
        $result = sortcontent($result, static::STICKY,
                              function ($left, $right) {
                                // either both are sticky or unsticky, don't resort
                                $result = 0;

                                if (istrue(gv($left, static::STICKY))) {
                                  if (!istrue(gv($right, static::STICKY))) {
                                    // only the left one is sticky, it should come first
                                    $result = -1;
                                  }
                                } else {
                                  if (istrue(gv($right, static::STICKY))) {
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
  Plugins::register(StickyAddon::class, "run", FILTER_PAGINATE);
