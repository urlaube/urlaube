<?php

  /**
    This is the Feed class of the urlau.be CMS.

    This file contains the Feed class of the urlau.be CMS core. The feed class simplifies the creation of RSS 2.0
    feeds.

    @package urlaube\urlaube
    @version 0.1a2
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists("Feed")) {
    class Feed implements Plugin {

      // RUNTIME FUNCTIONS

      public static function generate($content) {
        $result = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>".NL.
                  "<rss version=\"2.0\">".NL.
                  "  <channel>".NL.
                  "    <title>".html(Main::SITENAME())."</title>".NL.
                  "    <link>".html(Main::PROTOCOL().Main::HOSTNAME().Main::ROOTURI())."</link>".NL.
                  "    <description>".html(Main::SITESLOGAN())."</description>".NL.
                  "    <language>".html(str_replace("_", "-", Translations::LANGUAGE()))."</language>".NL;

        if (is_array($content)) {
          foreach ($content as $content_item) {
            if ($content_item instanceof Content) {
              $result .= "    <item>".NL;

              if ($content_item->isset(TITLE)) {
                $result .= "      <title>".html($content_item->get(TITLE))."</title>".NL;
              }
              if ($content_item->isset(URI)) {
                $result .= "      <link>".html(Main::PROTOCOL().Main::HOSTNAME().$content_item->get(URI))."</link>".NL;
                $result .= "      <guid>".html(Main::PROTOCOL().Main::HOSTNAME().$content_item->get(URI))."</guid>".NL;
              }
              if ($content_item->isset(DATE)) {
                $time = strtotime($content_item->get(DATE));
                if (false !== $time) {
                  $result .= "      <pubDate>".html(date("r", $time))."</pubDate>".NL;
                }
              }
              if ($content_item->isset(CATEGORY)) {
                $categories = array_unique(explode(SP, strtolower($content_item->get(CATEGORY))));
                foreach ($categories as $categories_item) {
                  $result .= "      <category>".html(trim($categories_item))."</category>".NL;
                }
              }
              if ($content_item->isset(CONTENT)) {
                $result .= "      <description>".html($content_item->get(CONTENT))."</description>".NL;
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

