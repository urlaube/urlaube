<?php

  /**
    These are the user functions of the urlau.be CMS core.

    This file contains the user functions of the urlau.be CMS core. Handler, plugin and them developers may rely on
    these functions as they will only change with prior notice.

    @package urlaube\urlaube
    @version 0.1a2
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  // call the $method in $entity and return its result value
  function callMethod($entity, $method, ...$arguments) {
    $result = false;

    // check if the method is callable
    if (checkMethod($entity, $method)) {
      // retrieve target
      $target = $method;
      if (null !== $entity) {
        $target = array($entity, $method);
      }

      $result = call_user_func_array($target, $arguments);
    }

    return $result;
  }

  // check if $method exists in $entity or as a function if $entity is null
  function checkMethod($entity, $method) {
    $result = false;

    // check if $entity is either an object or a class name
    if (is_object($entity) ||
        (is_string($entity) && class_exists($entity))) {
      // check if $method is a method of $entity
      $result = method_exists($entity, $method);
    } else {
      // check if $entity is empty
      if (null === $entity) {
        // check if $method is a function
        $result = function_exists($method);
      }
    }

    return $result;
  }

  // get URL of current page
  function curpage() {
    $result = null;

    // we use the current handler's getUri() method
    if (null !== Handlers::ACTIVE()) {
      $result = callMethod(Handlers::ACTIVE(), GETURI, Main::PAGEINFO());
    }

    return $result;
  }

  // compare a date string and return a value like strcmp
  function datecmp($left, $right) {
    $result = false;

    if (is_string($left) && is_string($right)) {
      $left  = strtotime($left);
      $right = strtotime($right);

      // only proceed if both strings are parsable dates
      if ((false !== $left) && (false !== $right)) {
        // substract $right from $left
        // if they are equal, the result is = 0
        // if $left < $right, the result is < 0
        // if $left > $right, the result is > 0
        $result = $left-$right;
      }
    }

    return $result;
  }

  // check if the given $content has one or more $keywords in $field
  function findkeywords($content, $field, $keywords) {
    $result = false;

    if (($content instanceof Content) && is_string($field) && is_array($keywords)) {
      // check if $field is set
      if ($content->isset($field)) {
        // check for each keyword if it is contained in the $field
        foreach ($keywords as $keywords_item) {
          $result = (false !== stripos($content->get($field), trim($keywords_item)));

          // if true then we're done
          if ($result) {
            break;
          }
        }
      }
    }

    return $result;
  }

  // get URL of first page
  function firstpage($force = false) {
    $result = null;

    // we use the current handler's getUri() method
    if (null !== Handlers::ACTIVE()) {
      // check that we're not already on the first page
      if ($force || (Main::PAGENUMBER() > Main::PAGEMIN())) {
        // update info to get the URI of the first page
        $info       = Main::PAGEINFO();
        $info[PAGE] = Main::PAGEMIN();

        $result = callMethod(Handlers::ACTIVE(), GETURI, $info);
      }
    }

    return $result;
  }

  // get the translation
  function gl($string) {
    return Translations::getTranslation($string);
  }

  // check if the given $content has a certain $author
  function hasauthor($content, $author) {
    $result = false;

    if (($content instanceof Content) && is_string($author)) {
      // check if AUTHOR is set
      if ($content->isset(AUTHOR)) {
        $result = (0 === strcasecmp(trim($author), trim($content->get(AUTHOR))));
      }
    }

    return $result;
  }

  // check if the given $content has a certain $category
  function hascategory($content, $category) {
    $result = false;

    if (($content instanceof Content) && is_string($category)) {
      // check if CATEGORY is set
      if ($content->isset(CATEGORY)) {
        // split the CATEGORY by spaces and iterate through them
        $content_category = explode(SP, $content->get(CATEGORY));
        foreach ($content_category as $content_category_item) {
          // check if the category from the URL matches the CATEGORY
          $result = (0 === strcasecmp(trim($category), trim($content_category_item)));

          // if true then we're done
          if ($result) {
            break;
          }
        }
      }
    }

    return $result;
  }

  // check if the given $content has a certain $date
  function hasdate($content, $year = null, $month = null, $day = null) {
    $result = false;

    if (($content instanceof Content) &&
        ((null === $year) || is_numeric($year)) &&
        ((null === $month) || is_numeric($month)) &&
        ((null === $day) || is_numeric($day))) {
      // check if DATE is set
      if ($content->isset(DATE)) {
        $time = strtotime($content->get(DATE));

        // only proceed if DATE is parsable
        if (false !== $time) {
          $date = getdate($time);

          // compare DATE with $year, $month and $day
          $result = (((null === $year) || ($date["year"] === $year)) &&
                     ((null === $month) || ($date["mon"] === $month)) &&
                     ((null === $day) || ($date["mday"] === $day)));
        }
      }
    }

    return $result;
  }

  // escape HTML in the given $string
  function html($string) {
    return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, Main::CHARSET(), false);
  }

  // check if the given $string represents a false state
  function isfalse($string) {
    $string = strtolower(trim($string));

    return ((0 === strcmp($string, "0")) ||
            (0 === strcmp($string, "false")) ||
            (0 === strcmp($string, "nein")) ||
            (0 === strcmp($string, "no")));
  }

  // check if the given $content is hidden
  function ishidden($content) {
    $result = false;

    if ($content instanceof Content) {
      $result = (($content->isset(HIDE)) && (istrue($content->get(HIDE))));
    }

    return $result;
  }

  // check if the given $content is hidden frome home
  function ishiddenfromhome($content) {
    $result = false;

    if ($content instanceof Content) {
      $result = (($content->isset(HOME)) && (isfalse($content->get(HOME))));
    }

    return $result;
  }

  // checks if $string starts with $lead
  function islead($string, $lead) {
    $result = false;

    if (is_string($string) && is_string($lead)) {
      $result = ($lead === substr($string, 0, strlen($lead)));
    }

    return $result;
  }

  // checks if $string ends with $trail
  function istrail($string, $trail) {
    $result = $string;

    if (is_string($string) && is_string($trail)) {
      $result = ($trail === substr($string, -strlen($trail)));
    }

    return $result;
  }

  // check if the given $string represents a true state
  function istrue($string) {
    $string = strtolower(trim($string));

    return ((0 === strcmp($string, "1")) ||
            (0 === strcmp($string, "ja")) ||
            (0 === strcmp($string, "true")) ||
            (0 === strcmp($string, "yes")));
  }

  // get URL of last page
  function lastpage($force = false) {
    $result = null;

    // we use the current handler's getUri() method
    if (null !== Handlers::ACTIVE()) {
      // check that we're not already on the last page
      if ($force || (Main::PAGENUMBER() < Main::PAGEMAX())) {
        // update info to get the URI of the last page
        $info       = Main::PAGEINFO();
        $info[PAGE] = Main::PAGEMAX();

        $result = callMethod(Handlers::ACTIVE(), GETURI, $info);
      }
    }

    return $result;
  }

  // checks if $string starts with $lead
  // if not then $lead is prepended to $string
  function lead($string, $lead) {
    $result = $string;

    if (is_string($result) && is_string($lead)) {
      if ($lead !== substr($result, 0, strlen($lead))) {
        $result = $lead.$result;
      }
    }

    return $result;
  }

  // get URL of next page
  function nextpage($force = false) {
    $result = null;

    // we use the current handler's getUri() method
    if (null !== Handlers::ACTIVE()) {
      // check if there's a next page
      if ($force || (Main::PAGENUMBER() < Main::PAGEMAX())) {
        // update info to get the URI of the next page
        $info       = Main::PAGEINFO();
        $info[PAGE] = Main::PAGENUMBER()+1;

        $result = callMethod(Handlers::ACTIVE(), GETURI, $info);
      }
    }

    return $result;
  }

  // checks if $string starts with $lead
  // if it does then $lead is removed from $string
  function nolead($string, $lead) {
    $result = $string;

    if (is_string($result) && is_string($lead)) {
      // repeat until there's no match
      while (0 === strpos($result, $lead)) {
        $result = substr($result, strlen($lead));
      }
    }

    return $result;
  }

  // checks if $string ends with $trail
  // if it does then $trail is removed from $string
  function notrail($string, $trail) {
    $result = $string;
 
    if (is_string($result) && is_string($trail)) {
      // repeat until there's no match
      while ($trail === substr($result, -strlen($trail))) {
        $result = substr($result, 0, -strlen($trail));
      }
    }

    return $result;
  }

  // return the entries of the given page
  function paginate($array, $page) {
    $result = null;

    if (is_array($array) && is_numeric($page)) {
      $result = array_slice($array, ($page-1)*Main::PAGESIZE(), Main::PAGESIZE(), false);

      // if the result is empty, we set it to null
      if (0 === count($result)) {
        $result = null;
      }
    }

    return $result;
  }

  // convert the given path to a URI
  function path2uri($path) {
    $result = null;

    if (is_string($path)) {
      if (0 === strpos($path, ROOT_PATH)) {
        // remove the root path
        $path = substr($path, strlen(ROOT_PATH));

        // prepend the root URI
        $result = Main::ROOTURI().strtr($path, DS, US);
      }
    }

    return $result;
  }

  // get URL of previous page
  function prevpage($force = false) {
    $result = null;

    // we use the current handler's getUri() method
    if (null !== Handlers::ACTIVE()) {
      // check if there's a previous page
      if ($force || (Main::PAGENUMBER() > Main::PAGEMIN())) {
        // update info to get the URI of the next page
        $info       = Main::PAGEINFO();
        $info[PAGE] = Main::PAGENUMBER()-1;

        $result = callMethod(Handlers::ACTIVE(), GETURI, $info);
      }
    }

    return $result;
  }

  // redirect to the given $uri
  function redirect($uri, $temporary = false) {
    $result = false;

    if (is_string($uri)) {
      if ($temporary) {
        http_response_code(302);
      } else {
        http_response_code(301);
      }

      header("Location: $uri");

      // success
      $result = true;
    }

    return $result;
  }

  // sort array of Content objects
  // splits entries with a certain fields from entries without that field
  // only entries with the value get sorted
  function sortcontent($array, $field, $comparator) {
    $result = $array;

    if (is_array($array) && is_string($field) && is_callable($comparator)) {
      // split elements into sortable and static
      $sortable   = array();
      $unsortable = array();
      foreach ($result as $result_item) {
        // check if the element is a content
        if ($result_item instanceof Content) {
          // if $field is set
          if ($result_item->isset($field)) {
            $sortable[] = $result_item;
          } else {
            $unsortable[] = $result_item;
          }
        }
      }

      // sort array by content field
      if (usort($sortable,
                function ($left, $right) use ($field, $comparator) {
                  return $comparator($left->get($field), $right->get($field));
                })) {
        // remerge previously split elements
        $result = array_merge($sortable, $unsortable);
      }
    }

    return $result;
  }

  // checks if $string ends with $trail
  // if not then $trail is appended to $string
  function trail($string, $trail) {
    $result = $string;

    if (is_string($result) && is_string($trail)) {
      if ($trail !== substr($result, -strlen($trail))) {
        $result = $result.$trail;
      }
    }

    return $result;
  }

  // convert the given URI to a path
  function uri2path($uri) {
    $result = null;

    if (is_string($uri)) {
      if (0 === strpos($uri, Main::ROOTURI())) {
        // remove the root path
        $uri = substr($uri, strlen(Main::ROOTURI()));

        // prepend the root path
        $result = ROOT_PATH.strtr($uri, US, DS);
      }
    }

    return $result;
  }

