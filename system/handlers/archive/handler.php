<?php

  /**
    This is the ArchiveHandler class of the urlau.be CMS.

    This file contains the ArchiveHandler class of the urlau.be CMS. The archive
    handler lists all pages that contain a certain date.

    @package urlaube\urlaube
    @version 0.1a12
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

    protected static function getResult($metadata, &$cachable) {
      // this result may be cached
      $cachable = true;

      $day   = value($metadata, static::DAY);
      $month = value($metadata, static::MONTH);
      $year  = value($metadata, static::YEAR);

      return callcontent(null, true, false,
                         function ($content) use ($year, $month, $day) {
                           $result = null;

                           // check that $content has a DATE field
                           if (null !== value($content, DATE)) {
                             // the date either has to match the given date or
                             // no date must be given
                             if (((null === $year) && (null === $month) && (null === $day)) ||
                                 hasdate($content, $year, $month, $day)) {
                               $result = $content;
                             }
                           }

                           return $result;
                         });
    }

    protected static function prepareMetadata($metadata) {
      $value = value($metadata, static::DAY);
      if (is_numeric($value)) {
        $metadata->set(static::DAY, intval($value));
      } else {
        $metadata->set(static::DAY, static::OPTIONAL[static::DAY]);
      }

      $value = value($metadata, static::MONTH);
      if (is_numeric($value)) {
        $metadata->set(static::MONTH, intval($value));
      } else {
        $metadata->set(static::MONTH, static::OPTIONAL[static::MONTH]);
      }

      $value = value($metadata, static::YEAR);
      if (is_numeric($value)) {
        $metadata->set(static::YEAR, intval($value));
      } else {
        $metadata->set(static::YEAR, static::OPTIONAL[static::YEAR]);
      }

      return $metadata;
    }

  }

  // register handler
  Handlers::register(ArchiveHandler::class, "run", ArchiveHandler::REGEX, [GET, POST], ERROR_SYSTEM);
