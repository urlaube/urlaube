<?php

  /**
    This is the BaseHandler class of the urlau.be CMS core.

    This file contains the BaseHandler class of the urlau.be CMS core. Most of
    the system handlers do the same thing over and over again. This class helps
    to reduce the amount of duplicate code.

    @package urlaube\urlaube
    @version 0.1a7
    @author  Yahe <hello@yahe.sh>
    @since   0.1a7
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  abstract class BaseHandler extends BaseSingleton implements Handler {

    // ABSTRACT FUNCTIONS

    protected static abstract function getResult($metadata);

    // INTERFACE FUNCTIONS

    public static function getContent($metadata, &$pagecount) {
      $pagecount = 1;
      $result    = null;

      $metadata = preparecontent($metadata, static::OPTIONAL, static::MANDATORY);
      if ($metadata instanceof Content) {
        $result = preparecontent(static::getResult($metadata));

        // sort and paginate the content if it is an array
        if (is_array($result)) {
          // sort entries by DATE
          $result = sortcontent($result, DATE,
                                function ($left, $right) {
                                  // reverse-sort
                                  return -datecmp($left, $right);
                                });

          // handle pagination if the PAGE metadate is supported
          if ($metadata->isset(PAGE)) {
            // set the maximum page count
            $pagecount = ceil(count($result)/value(Main::class, PAGESIZE));

            // execute the pagination
            $result = paginate($result, value($metadata, PAGE));
          }
        }
      }

      return $result;
    }

    public static function getUri($metadata){
      $result = null;

      $metadata = preparecontent($metadata, static::OPTIONAL, static::MANDATORY);
      if ($metadata instanceof Content) {
        // handle pagination if the PAGE metadate is supported
        if ($metadata->isset(PAGE)) {
          $page = value($metadata, PAGE);
          if (is_numeric($page)) {
            $metadata->set(PAGE, intval($page));
          }
        }

        // get the base URI
        $result = value(Main::class, ROOTURI);

        // append the mandatory URI parts
        if (is_array(static::MANDATORY)) {
          foreach (static::MANDATORY as $value) {
            $result .= strtolower(trim($value)).EQ.value($metadata, $value).US;
          }
        }

        // append the optional URI parts
        if (is_array(static::OPTIONAL)) {
          foreach (static::OPTIONAL as $key => $value) {
            // only append it if they value differs from the default
            if ($value !== value($metadata, $key)) {
              $result .= strtolower(trim($key)).EQ.value($metadata, $key).US;
            }
          }
        }
      }

      return $result;
    }

    public static function parseUri($uri) {
      $result = preparecontent(parseuri($uri, static::REGEX), static::OPTIONAL, static::MANDATORY);

      if ($result instanceof Content) {
        // handle pagination if the PAGE metadate is supported
        if ($result->isset(PAGE)) {
          $page = value($result, PAGE);
          if (is_numeric($page)) {
            $result->set(PAGE, intval($page));
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
        // set the metadata to be processed by plugins and the theme
        Main::set(METADATA, $metadata);

        // check if the URI is correct
        $fixed = static::getUri($metadata);
        if (0 !== strcmp(value(Main::class, URI), $fixed)) {
          relocate($fixed, false, true);

          // we handled this page
          $result = true;
        } else {
          $content = static::getContent($metadata, $pagecount);
          if (null !== $content) {
            // filter the content
            $content = preparecontent(Plugins::run(FILTER_CONTENT, true, $content));

            // set the content to be processed by plugins and the theme
            Main::set(CONTENT, $content);

            // handle pagination if the PAGE metadate is supported
            if ($metadata->isset(PAGE)) {
              Main::set(PAGE,      value($metadata, PAGE));
              Main::set(PAGECOUNT, $pagecount);
            } else {
              Main::set(PAGE,      1);
              Main::set(PAGECOUNT, 1);
            }

            // transfer the handling to the Themes class
            Themes::run();

            // we handled this page
            $result = true;
          }
        }
      }

      return $result;
    }

  }
