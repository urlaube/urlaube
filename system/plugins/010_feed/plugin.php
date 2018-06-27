<?php

  /**
    This is the Feed class of the urlau.be CMS.

    This file contains the Feed class of the urlau.be CMS core. The feed class simplifies the creation of RSS 2.0
    feeds.

    @package urlaube\urlaube
    @version 0.1a5
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
                                 $content_item->get(TITLE));
              }
              if ($content_item->isset(URI)) {
                $result .= fhtml("      <link>%s</link>".NL.
                                 "      <guid>%s</guid>".NL,
                                 Main::PROTOCOL().Main::HOSTNAME().$content_item->get(URI),
                                 Main::PROTOCOL().Main::HOSTNAME().$content_item->get(URI));
              }
              if ($content_item->isset(DATE)) {
                $time = strtotime($content_item->get(DATE));
                if (false !== $time) {
                  $result .= fhtml("      <pubDate>%s</pubDate>".NL,
                                   date("r", $time));
                }
              }
              if ($content_item->isset(CATEGORY)) {
                $categories = array_unique(explode(SP, strtolower($content_item->get(CATEGORY))));
                foreach ($categories as $categories_item) {
                  $result .= fhtml("      <category>%s</category>".NL,
                                   trim($categories_item));
                }
              }
              if ($content_item->isset(CONTENT)) {
                $result .= fhtml("      <description>%s</description>".NL,
                                 $content_item->get(CONTENT));
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

