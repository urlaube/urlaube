<?php

  /**
    This is the BaseSingleton class of the urlau.be CMS core.

    This file contains the BaseSingleton class of the urlau.be CMS core. The
    core classes use a singleton pattern by means of static-only properties and
    methods. The Base class tries to prevent the instantiation of core classes.

    @package urlaube\urlaube
    @version 0.1a8
    @author  Yahe <hello@yahe.sh>
    @since   0.1a7
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  abstract class BaseSingleton {

    // CONSTRUCTORS / DESTRUCTORS

    protected function __clone() {}

    protected function __construct() {}

    protected function __destruct() {}

  }
