<?php

  /**
    This is the Handler interface of the urlau.be CMS core.

    This file contains the Handler interface of the urlau.be CMS core.This interface defines basic functions that each
    handler should implement to simply the implementation of further functions

    @package urlaube\urlaube
    @version 0.1a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!interface_exists("Handler")) {
    interface Handler {

      public static function getContent($info);
      public static function getUri($info);
      public static function parseUri($uri);

    }
  }

