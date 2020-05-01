<?php

  /**
    This is the MarkdownAddon class of the urlau.be CMS.

    The markdown addon converts markdown-encoded content to HTML-encoded content.

    @package urlaube/urlaube
    @version 0.2a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class MarkdownAddon extends BaseSingleton implements Plugin {

    // CONSTANTS

    const NOMARKDOWN           = "nomarkdown";
    const NOMARKDOWNPARAGRAPHS = "nomarkdownparagraphs";

    // RUNTIME FUNCTIONS

    public static function run($content) {
      // instantiate markdown converter
      $parsedown = new ParsedownExtra();

      if ($content instanceof Content) {
        if ($content->isset(CONTENT)) {
          // do not use markdown if markdown field is set to false
          if (!istrue(gv($content, static::NOMARKDOWN))) {
            // switch between markdown and inline markdown
            if (!istrue(gv($content, static::NOMARKDOWNPARAGRAPHS))) {
              $content->set(CONTENT, $parsedown->text($content->get(CONTENT)));
            } else {
              $content->set(CONTENT, $parsedown->line($content->get(CONTENT)));
            }
          }
        }
      } else {
        if (is_array($content)) {
          // iterate through all content items
          foreach ($content as $content_item) {
            if ($content_item instanceof Content) {
              if ($content_item->isset(CONTENT)) {
                // do not use markdown if markdown field is set to false
                if (!istrue(gv($content_item, static::NOMARKDOWN))) {
                  // switch between markdown and inline markdown
                  if (!istrue(gv($content_item, static::NOMARKDOWNPARAGRAPHS))) {
                    $content_item->set(CONTENT, $parsedown->text($content_item->get(CONTENT)));
                  } else {
                    $content_item->set(CONTENT, $parsedown->line($content_item->get(CONTENT)));
                  }
                }
              }
            }
          }
        }
      }

      return $content;
    }

  }

  // include Parsedown
  require_once(__DIR__."/vendors/parsedown/Parsedown.php");
  // include Parsedown-Extra
  require_once(__DIR__."/vendors/parsedown-extra/ParsedownExtra.php");

  // register plugin
  Plugins::register(MarkdownAddon::class, "run", FILTER_CONTENT);
