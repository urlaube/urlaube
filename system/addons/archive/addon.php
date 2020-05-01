<?php

  /**
    This is the ArchiveAddon class of the urlau.be CMS.

    The archive addon lists all pages that contain a certain date.

    @package urlaube/urlaube
    @version 0.2a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class ArchiveAddon extends BaseHandler {

    // CONSTANTS

    const DAY   = "day";
    const MONTH = "month";
    const YEAR  = "year";

    const MANDATORY = null;
    const OPTIONAL  = [self::YEAR => null, self::MONTH => null, self::DAY => null, PAGE => 1];
    const PAGINATE  = true;
    const REGEX     = "~^\/".
                      "(year\=(?P<year>[0-9]+)\/)?".
                      "(month\=(?P<month>[0-9]+)\/)?".
                      "(day\=(?P<day>[0-9]+)\/)?".
                      "(page\=(?P<page>[0-9]+)\/)?".
                      "$~";

    // ABSTRACT FUNCTIONS

    protected static function getResult($metadata) {
      $day   = $metadata->get(static::DAY);
      $month = $metadata->get(static::MONTH);
      $year  = $metadata->get(static::YEAR);

      return callcontent(null, true, false,
                         function ($content) use ($year, $month, $day) {
                           $result = null;

                           // check that $content has a DATE field
                           if (null !== gv($content, DATE)) {
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
      $value = $metadata->get(static::DAY);
      if (is_numeric($value)) {
        $metadata->set(static::DAY, intval($value));
      } else {
        $metadata->set(static::DAY, static::OPTIONAL[static::DAY]);
      }

      $value = $metadata->get(static::MONTH);
      if (is_numeric($value)) {
        $metadata->set(static::MONTH, intval($value));
      } else {
        $metadata->set(static::MONTH, static::OPTIONAL[static::MONTH]);
      }

      $value = $metadata->get(static::YEAR);
      if (is_numeric($value)) {
        $metadata->set(static::YEAR, intval($value));
      } else {
        $metadata->set(static::YEAR, static::OPTIONAL[static::YEAR]);
      }

      return $metadata;
    }

  }

  // register handler
  Handlers::register(ArchiveAddon::class, "run", ArchiveAddon::REGEX, [GET, POST], ERROR_SYSTEM);
