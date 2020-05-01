<?php

  /**
    This is the HideAddon class of the urlau.be CMS.

    This addon introduces the hide feature to remove content from certain handler results.

    @package urlaube/urlaube
    @version 0.2a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class HideAddon extends BaseSingleton implements Plugin {

    // CONSTANTS

    const HIDDEN             = "hidden";
    const HIDDENFROMARCHIVE  = "hiddenfromarchive";
    const HIDDENFROMAUTHOR   = "hiddenfromauthor";
    const HIDDENFROMCATEGORY = "hiddenfromcategory";
    const HIDDENFROMSEARCH   = "hiddenfromsearch";
    const HIDDENFROMSITEMAP  = "hiddenfromsitemap";

    // HELPER FUNCTIONS

    protected static function isActive($entity) {
      $handler = gethandler();

      return ((null !== $handler) &&
              (($entity === $handler[ENTITY]) ||
               ((FeedAddon::class === $handler[ENTITY]) && ($entity === gv(gc(METADATA, null), FeedAddon::FEED)))));
    }

    protected static function isHidden($content) {
      return (istrue(gv($content, static::HIDDEN)) ||
              (static::isActive(ArchiveAddon::class) && istrue(gv($content, static::HIDDENFROMARCHIVE))) ||
              (static::isActive(AuthorAddon::class) && istrue(gv($content, static::HIDDENFROMAUTHOR))) ||
              (static::isActive(CategoryAddon::class) && istrue(gv($content, static::HIDDENFROMCATEGORY))) ||
              (static::isActive(SearchAddon::class) && istrue(gv($content, static::HIDDENFROMSEARCH))) ||
              (static::isActive(SitemapXmlAddon::class) && istrue(gv($content, static::HIDDENFROMSITEMAP))));
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

          // renumber result or set it to null
          if (0 < count($result)) {
            $result = array_values($result);
          } else {
            $result = null;
          }
        }
      }

      return $result;
    }

  }

  // register plugin
  Plugins::register(HideAddon::class, "run", FILTER_CONTENT);
