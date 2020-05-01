<?php

  /**
    This is the Handler interface of the urlau.be CMS core.

    This interface defines basic functions that each handler should implement to simplify the implementation of further
    functions.

    @package urlaube/urlaube
    @version 0.2a0
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  interface Handler {

    public static function getContent($metadata, &$pagecount);
    public static function getUri($metadata);
    public static function parseUri($uri);

  }
