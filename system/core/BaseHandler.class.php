<?php

  /**
    This is the BaseHandler class of the urlau.be CMS core.

    This file contains the BaseHandler class of the urlau.be CMS core. Most of
    the system handlers do the same thing over and over again. This class helps
    to reduce the amount of duplicate code.

    @package urlaube/urlaube
    @version 0.2a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a7
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  abstract class BaseHandler extends BaseSingleton implements Handler {

    // ABSTRACT CONSTANTS

    const MADATORY = null;  // must be NULL or an array
    const OPTIONAL = null;  // must be NULL or an associative array
    const PAGINATE = false; // must be boolean
    const REGEX    = null;  // must be a regular expression string

    // ABSTRACT FUNCTIONS

    protected static abstract function getResult($metadata);
    protected static abstract function prepareMetadata($metadata);

    // INTERFACE FUNCTIONS

    public static function getContent($metadata, &$pagecount) {
      $pagecount = 1;
      $result    = null;

      // prepare metadata for sanitization
      $metadata = preparecontent($metadata, static::OPTIONAL, static::MANDATORY);
      if ($metadata instanceof Content) {
        // sanitize metadata
        $metadata = preparecontent(static::prepareMetadata($metadata), static::OPTIONAL, static::MANDATORY);
        if ($metadata instanceof Content) {
          $result = preparecontent(static::getResult($metadata));
          if (null !== $result) {
            // sort and paginate the content if it is an array
            if (is_array($result)) {
              // sort entries by DATE
              $result = sortcontent($result, DATE,
                                    function ($left, $right) {
                                      // reverse-sort
                                      return -datecmp($left, $right);
                                    });

              // handle pagination if the PAGE metadate is supported
              if (static::PAGINATE && is_numeric($metadata->get(PAGE)) && is_numeric(Config::get(PAGESIZE))) {
                // set the maximum page count
                $pagecount = ceil(count($result)/Config::get(PAGESIZE));

                // execute the pagination
                $result = paginate($result, $metadata->get(PAGE));
              }
            }
          }
        }
      }

      return $result;
    }

    public static function getUri($metadata){
      $result = null;

      // prepare metadata for sanitization
      $metadata = preparecontent($metadata, static::OPTIONAL, static::MANDATORY);
      if ($metadata instanceof Content) {
        // handle pagination if the PAGE metadate is supported
        if ($metadata->isset(PAGE)) {
          $page = $metadata->get(PAGE);
          if (is_numeric($page)) {
            $metadata->set(PAGE, intval($page));
          } else {
            // set page to the default value if it is set
            if (is_array(static::OPTIONAL) and array_key_exists(PAGE, static::OPTIONAL)) {
              $metadata->set(PAGE, static::OPTIONAL[PAGE]);
            } else {
              // set page to a standard value
              $metadata->set(PAGE, 1);
            }
          }
        }

        // sanitize metadata
        $metadata = preparecontent(static::prepareMetadata($metadata), static::OPTIONAL, static::MANDATORY);
        if ($metadata instanceof Content) {
          // get the base URI
          $result = Config::get(ROOTURI);

          // append the mandatory URI parts
          if (is_array(static::MANDATORY)) {
            foreach (static::MANDATORY as $value) {
              $result .= strtolower(trim($value)).EQ.$metadata->get($value).US;
            }
          }

          // append the optional URI parts
          if (is_array(static::OPTIONAL)) {
            foreach (static::OPTIONAL as $key => $value) {
              // only append it if they value differs from the default
              if ($value !== $metadata->get($key)) {
                $result .= strtolower(trim($key)).EQ.$metadata->get($key).US;
              }
            }
          }
        }
      }

      return $result;
    }

    public static function parseUri($uri) {
      $result = null;

      // prepare metadata for sanitization
      $metadata = preparecontent(parseuri($uri, static::REGEX), static::OPTIONAL, static::MANDATORY);
      if ($metadata instanceof Content) {
        // handle pagination if the PAGE metadate is supported
        if ($metadata->isset(PAGE)) {
          $page = $metadata->get(PAGE);
          if (is_numeric($page)) {
            $metadata->set(PAGE, intval($page));
          } else {
            // set page to the default value if it is set
            if (is_array(static::OPTIONAL) and array_key_exists(PAGE, static::OPTIONAL)) {
              $metadata->set(PAGE, static::OPTIONAL[PAGE]);
            } else {
              // set page to a standard value
              $metadata->set(PAGE, 1);
            }
          }
        }

        // sanitize metadata
        $metadata = preparecontent(static::prepareMetadata($metadata), static::OPTIONAL, static::MANDATORY);
        if ($metadata instanceof Content) {
          $result = $metadata;
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
        Config::set(METADATA, $metadata);

        $content = preparecontent(static::getContent($metadata, $pagecount));
        if (null !== $content) {
          // check if the URI is correct
          $fixed = static::getUri($metadata);
          if (0 !== strcmp(Config::get(URI), $fixed)) {
            relocate($fixed.querystring(), false, true);
          } else {
            // set the content to be processed by plugins and the theme
            Config::set(CONTENT, $content);

            // handle pagination if the PAGE metadate is supported
            if ($metadata->isset(PAGE)) {
              Config::set(PAGE,      $metadata->get(PAGE));
              Config::set(PAGECOUNT, $pagecount);
            } else {
              Config::set(PAGE,      1);
              Config::set(PAGECOUNT, 1);
            }
          }

          // we handled this page
          $result = true;
        }
      }

      return $result;
    }

  }
