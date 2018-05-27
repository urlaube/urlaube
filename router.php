<?php

  /**
    This is the router file of the urlau.be CMS.

    This file can be used as a routing file when testing urlau.be with the built-in PHP webserver.
    Usage: `php -S localhost:8080 ./router.php`

    @package urlaube\urlaube
    @version 0.1a3
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // this script shall only be called from the CLI server
  if ("cli-server" !== PHP_SAPI) { die(""); }

  // route calls to index file
  $result = (!is_file(__DIR__.$_SERVER["REQUEST_URI"]));
  if ($result) {
    require(__DIR__."/index.php");
  }
  return $result;

