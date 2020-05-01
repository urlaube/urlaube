<?php

  /**
    This is the FeedAddon class of the urlau.be CMS.

    The feed addon produces RSS 2.0 feeds for supported system handlers.

    @package urlaube/urlaube
    @version 0.2a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a7
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class FeedAddon extends BaseSingleton implements Handler {

    // CONSTANTS

    const FEED   = "feed";
    const SUBURL = "suburl";

    const REGEX = "~^\/".
                  "feed\/".
                  "(?P<suburl>[0-9A-Za-z\_\-\/\.\=]*)".
                  "$~";

    const SOURCES = [ArchiveAddon::class,
                     AuthorAddon::class,
                     CategoryAddon::class,
                     SearchAddon::class];

    // INTERFACE FUNCTIONS

    public static function getContent($metadata, &$pagecount) {
      $pagecount = 1;
      $result    = null;

      $metadata = preparecontent($metadata, null, [static::FEED]);
      if ($metadata instanceof Content) {
        // check that only supported feed sources are handled
        if (in_array($metadata->get(static::FEED), static::SOURCES)) {
          // get the content of the feed source
          $result = _callFunction($metadata->get(static::FEED), GETCONTENT, [$metadata, &$pagecount]);
        }
      }

      return $result;
    }

    public static function getUri($metadata) {
      $result = null;

      $metadata = preparecontent($metadata, null, [static::FEED]);
      if ($metadata instanceof Content) {
        // check that only supported feed sources are handled
        if (in_array($metadata->get(static::FEED), static::SOURCES)) {
          // get the root URI of the feed source
          $uri = _callFunction($metadata->get(static::FEED), GETURI, [$metadata]);
          if (null !== $uri) {
            // retrieve the relative URI
            $uri = relativeuri($uri);

            // now produce the absolute URI
            $result = gc(ROOTURI, null).static::FEED.$uri;
          }
        }
      }

      return $result;
    }

    public static function parseUri($uri) {
      $result = null;

      $metadata = preparecontent(parseuri($uri, static::REGEX), [static::SUBURL => US], null);
      if ($metadata instanceof Content) {
        // check that the SUBURL starts and ends with a slash
        $suburl = trail(lead($metadata->get(static::SUBURL), US), US);

        // fix the suburl field
        $metadata->set(static::SUBURL, $suburl);

        // iterate through the potential feed sources
        foreach (static::SOURCES as $feed_item) {
          // try to parse the SUBURL with a feed source
          $temp = _callFunction($feed_item, PARSEURI, [$suburl]);
          if ($temp instanceof Content) {
            // store the name of the feed source
            $metadata->set(static::FEED, $feed_item);

            // store the metadata of the feed source
            $metadata->merge($temp);

            // we're done
            $result = $metadata;

            break;
          }
        }
      }

      return $result;
    }

    // RUNTIME FUNCTIONS

    public static function run() {
      $result = false;

      $metadata = static::parseUri(relativeuri());
      if (null !== $metadata) {
        // set the metadata to be processed by plugins
        Config::set(METADATA, $metadata);

        $content = preparecontent(static::getContent($metadata, $pagecount));
        if (null !== $content) {
          // check if the URI is correct
          $fixed = static::getUri($metadata);
          if (0 !== strcmp(gc(URI, null), $fixed)) {
            relocate($fixed.querystring(), false, true);
          } else {
            // set the content type
            header("Content-Type: application/rss+xml");

            // return a minimalistic RSS 2.0 feed
            print(fhtml("<?xml version=\"1.0\" encoding=\"UTF-8\" ?>".NL.
                        "<rss version=\"2.0\">".NL.
                        "  <channel>".NL.
                        "    <title>%s</title>".NL.
                        "    <link>%s</link>".NL.
                        "    <description>%s</description>".NL.
                        "    <language>%s</language>".NL,
                        gc(SITENAME, null),
                        absoluteurl("/"),
                        gc(SITESLOGAN, null),
                        strtr(gc(LANGUAGE, null), "_", "-")));

            if (null !== $content) {
              // make sure that we are handling an array
              if (!is_array($content)) {
                $content = [$content];
              }

              foreach ($content as $content_item) {
                print(fhtml("    <item>".NL));

                if ($content_item->isset(TITLE)) {
                  print(fhtml("      <title>%s</title>".NL,
                              $content_item->get(TITLE)));
                }
                if ($content_item->isset(URI)) {
                  print(fhtml("      <link>%s</link>".NL.
                              "      <guid>%s</guid>".NL,
                              absoluteurl($content_item->get(URI)),
                              absoluteurl($content_item->get(URI))));
                }
                if ($content_item->isset(DATE)) {
                  $time = strtotime($content_item->get(DATE));
                  if (false !== $time) {
                    print(fhtml("      <pubDate>%s</pubDate>".NL,
                                date("r", $time)));
                  }
                }
                if ($content_item->isset(CATEGORY)) {
                  $categories = array_unique(explode(SP, strtolower($content_item->get(CATEGORY))));
                  foreach ($categories as $categories_item) {
                    print(fhtml("      <category>%s</category>".NL,
                                trim($categories_item)));
                  }
                }
                if ($content_item->isset(CONTENT)) {
                  print(fhtml("      <description>%s</description>".NL,
                              $content_item->get(CONTENT)));
                }

                print(fhtml("    </item>".NL));
              }
            }

            print(fhtml("  </channel>".NL.
                        "</rss>"));
          }

          // we handled this page
          $result = true;
        }
      }

      return $result;
    }

  }

  // register handler
  Handlers::register(FeedAddon::class, "run", FeedAddon::REGEX, [GET, POST], ERROR_SYSTEM);
