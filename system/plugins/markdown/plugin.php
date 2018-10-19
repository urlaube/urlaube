<?php

  /**
    This is the MarkdownPlugin class of the urlau.be CMS.

    This file contains the MarkdownPlugin class of the urlau.be CMS. This plugin
    converts markdown-encoded content to HTML-encoded content.

    @package urlaube\urlaube
    @version 0.1a7
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class MarkdownPlugin extends BaseSingleton implements Plugin {

    // RUNTIME FUNCTIONS

    public static function run($content) {
      // instantiate markdown converter
      $parsedown = new ParsedownExtra();

      if ($content instanceof Content) {
        if ($content->isset(CONTENT)) {
          // do not use markdown if markdown field is set to false
          if (!istrue(value($content, NOMARKDOWN))) {
            $content->set(CONTENT, $parsedown->text(value($content, CONTENT)));
          }
        }
      } else {
        if (is_array($content)) {
          // iterate through all content items
          foreach ($content as $content_item) {
            if ($content_item instanceof Content) {
              if ($content_item->isset(CONTENT)) {
                // do not use markdown if markdown field is set to false
                if (!istrue(value($content_item, NOMARKDOWN))) {
                  $content_item->set(CONTENT, $parsedown->text(value($content_item, CONTENT)));
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
  Plugins::register(MarkdownPlugin::class, "run", FILTER_CONTENT);
