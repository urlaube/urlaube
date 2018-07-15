<?php

  /**
    This is the Feed class of the urlau.be CMS.

    This file contains the Feed class of the urlau.be CMS core. The feed class simplifies the creation of RSS 2.0
    feeds.

    @package urlaube\urlaube
    @version 0.1a6
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists("Feed")) {
    class Feed extends Base implements Plugin {

      // RUNTIME FUNCTIONS

      public static function generate($content) {
        $result = fhtml("<?xml version=\"1.0\" encoding=\"UTF-8\" ?>".NL.
                        "<rss version=\"2.0\">".NL.
                        "  <channel>".NL.
                        "    <title>%s</title>".NL.
                        "    <link>%s</link>".NL.
                        "    <description>%s</description>".NL.
                        "    <language>%s</language>".NL,
                        Main::SITENAME(),
                        Main::PROTOCOL().Main::HOSTNAME().Main::ROOTURI(),
                        Main::SITESLOGAN(),
                        str_replace("_", "-", Translations::LANGUAGE()));

        if (is_array($content)) {
          foreach ($content as $content_item) {
            if ($content_item instanceof Content) {
              $result .= "    <item>".NL;

              if ($content_item->isset(TITLE)) {
                $result .= fhtml("      <title>%s</title>".NL,
                                 value($content_item, TITLE));
              }
              if ($content_item->isset(URI)) {
                $result .= fhtml("      <link>%s</link>".NL.
                                 "      <guid>%s</guid>".NL,
                                 Main::PROTOCOL().Main::HOSTNAME().value($content_item, URI),
                                 Main::PROTOCOL().Main::HOSTNAME().value($content_item, URI));
              }
              if ($content_item->isset(DATE)) {
                $time = strtotime(value($content_item, DATE));
                if (false !== $time) {
                  $result .= fhtml("      <pubDate>%s</pubDate>".NL,
                                   date("r", $time));
                }
              }
              if ($content_item->isset(CATEGORY)) {
                $categories = array_unique(explode(SP, strtolower(value($content_item, CATEGORY))));
                foreach ($categories as $categories_item) {
                  $result .= fhtml("      <category>%s</category>".NL,
                                   trim($categories_item));
                }
              }
              if ($content_item->isset(CONTENT)) {
                $result .= fhtml("      <description>%s</description>".NL,
                                 value($content_item, CONTENT));
              }

              $result .= "    </item>".NL;
            }
          }
        }

        $result .= "  </channel>".NL.
                   "</rss>";

        return $result;
      }

    }
  }

