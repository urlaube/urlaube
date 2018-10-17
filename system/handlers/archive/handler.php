<?php

  /**
    This is the ArchiveHandler class of the urlau.be CMS.

    This file contains the ArchiveHandler class of the urlau.be CMS. The archive
    handler lists all pages that contain a certain date.

    @package urlaube\urlaube
    @version 0.1a7
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class ArchiveHandler extends BaseHandler {

    // CONSTANTS

    const DAY   = "day";
    const MONTH = "month";
    const YEAR  = "year";

    const MANDATORY = null;
    const OPTIONAL  = [self::YEAR => null, self::MONTH => null, self::DAY => null, PAGE => 1];
    const REGEX     = "~^\/".
                      "(year\=(?P<year>[0-9]+)\/)?".
                      "(month\=(?P<month>[0-9]+)\/)?".
                      "(day\=(?P<day>[0-9]+)\/)?".
                      "(page\=(?P<page>[0-9]+)\/)?".
                      "$~";

    // ABSTRACT FUNCTIONS

    protected static function getResult($metadata) {
      $day   = value($metadata, static::DAY);
      $month = value($metadata, static::MONTH);
      $year  = value($metadata, static::YEAR);

      return FilePlugin::loadContentDir(USER_CONTENT_PATH, false,
                                        function ($content) use ($year, $month, $day) {
                                          $result = null;

                                          // check that $content is not hidden
                                          if (!istrue(value($content, HIDDEN))) {
                                            // check that $content is not hidden from archive
                                            if (!istrue(value($content, HIDDENFROMARCHIVE))) {
                                              // check that $content is not a relocation
                                              if (null === value($content, RELOCATE)) {
                                                // check that $content has a DATE field
                                                if (((null === $year) && (null === $month) && (null === $day)) ||
                                                    hasdate($content, $year, $month, $day)) {
                                                  $result = $content;
                                                }
                                              }
                                            }
                                          }

                                          return $result;
                                        },
                                        true);
    }

    // INTERFACE FUNCTIONS

    // overwrite the default behaviour
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

        // prepare the integer values
        $day = value($metadata, static::DAY);
        if (is_numeric($day)) {
          $metadata->set(static::DAY, intval($day));
        }
        $month = value($metadata, static::MONTH);
        if (is_numeric($month)) {
          $metadata->set(static::MONTH, intval($month));
        }
        $year = value($metadata, static::YEAR);
        if (is_numeric($year)) {
          $metadata->set(static::YEAR, intval($year));
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

  }

  // register handler
  Handlers::register(ArchiveHandler::class, "run", ArchiveHandler::REGEX, [GET, POST], PAGE_SYSTEM);
