<?php

  /**
    This is the Plugin interface of the urlau.be CMS core.

    This file contains the Plugin interface of the urlau.be CMS core.This interface defines basic functions that each
    plugin should implement to simply the implementation of further functions

    @package urlaube\urlaube
    @version 0.1a1
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!interface_exists("Plugin")) {
    interface Plugin {

    }
  }

