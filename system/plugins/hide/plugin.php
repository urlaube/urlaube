<?php

  /**
    This is the HidePlugin class of the urlau.be CMS.

    This file contains the HidePlugin class of the urlau.be CMS. This plugin
    introduces the hide feature to remove content from certain handler results.

    @package urlaube\urlaube
    @version 0.1a10
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class HidePlugin extends BaseSingleton implements Plugin {

    // CONSTANTS

    const HIDDEN             = "hidden";
    const HIDDENFROMARCHIVE  = "hiddenfromarchive";
    const HIDDENFROMAUTHOR   = "hiddenfromauthor";
    const HIDDENFROMCATEGORY = "hiddenfromcategory";
    const HIDDENFROMSEARCH   = "hiddenfromsearch";
    const HIDDENFROMSITEMAP  = "hiddenfromsitemap";

    // HELPER FUNCTIONS

    protected static function isActive($handler) {
      return (($handler === Handlers::getActive()) ||
              ((FeedHandler::class === Handlers::getActive()) &&
               ($handler === value(value(Main::class, METADATA), FeedHandler::FEED))));
    }

    protected static function isHidden($content) {
      return (istrue(value($content, static::HIDDEN)) ||
              (static::isActive(ArchiveHandler::class) && istrue(value($content, static::HIDDENFROMARCHIVE))) ||
              (static::isActive(AuthorHandler::class) && istrue(value($content, static::HIDDENFROMAUTHOR))) ||
              (static::isActive(CategoryHandler::class) && istrue(value($content, static::HIDDENFROMCATEGORY))) ||
              (static::isActive(SearchHandler::class) && istrue(value($content, static::HIDDENFROMSEARCH))) ||
              (static::isActive(SitemapXmlHandler::class) && istrue(value($content, static::HIDDENFROMSITEMAP))));
    }

    // RUNTIME FUNCTIONS

    public static function run($content) {
      $result = $content;

      if ($content instanceof Content) {
        if (static::isHidden($result)) {
          $result = null;
        }
      } else {
        if (is_array($result)) {
          // iterate through all content items
          foreach ($result as $key => $value) {
            if ($value instanceof Content) {
              if (static::isHidden($value)) {
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
  Plugins::register(HidePlugin::class, "run", FILTER_CONTENT);
