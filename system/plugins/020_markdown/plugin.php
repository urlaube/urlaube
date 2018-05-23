<?php

  /**
    This is the Markdown class of the urlau.be CMS.

    This file contains the Markdown class of the urlau.be CMS. This plugin converts markdown-encoded content
    to HTML-encoded content.

    @package urlaube\urlaube
    @version 0.1a1
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists("Markdown")) {
    class Markdown implements Plugin {

      // RUNTIME FUNCTIONS

      public static function apply($content) {
        // instantiate markdown converter
        $parsedown = new Parsedown();

        if ($content instanceof Content) {
          if ($content->isset(CONTENT)) {
            // do not use markdown if markdown field is set to false
            if ((!$content->isset(MARKDOWN)) || (!isfalse($content->get(MARKDOWN)))) {
              $content->set(CONTENT, $parsedown->text($content->get(CONTENT)));
            }
          }
        } else {
          if (is_array($content)) {
            // iterate through all content items
            foreach ($content as $content_item) {
              if ($content_item instanceof Content) {
                if ($content_item->isset(CONTENT)) {
                  // do not use markdown if markdown field is set to false
                  if ((!$content_item->isset(MARKDOWN)) || (!isfalse($content_item->get(MARKDOWN)))) {
                    $content_item->set(CONTENT, $parsedown->text($content_item->get(CONTENT)));
                  }
                }
              }
            }
          }
        }

        return $content;
      }

      public static function handle($content) {
        return static::apply($content);
      }

    }

    // include Parsedown
    require_once(__DIR__."/vendors/parsedown/Parsedown.php");

    // register plugin
    Plugins::register("Markdown", "handle", FILTER_CONTENT);
  }

