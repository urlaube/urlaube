<?php

  /**
    These are the user functions of the urlau.be CMS core.

    This file contains the user functions of the urlau.be CMS core. Handler, plugin and them developers may rely on
    these functions as they will only change with prior notice.

    @package urlaube\urlaube
    @version 0.1a6
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  // ***** Content Functions *****

  // check if the given $content has one or more $keywords in $field
  function findkeywords($content, $field, $keywords) {
    $result = false;

    // get the requested field
    $value = value($content, $field);
    if (null !== $value) {
      if (is_array($keywords)) {
        // check for each keyword if it is contained in the $field
        foreach ($keywords as $keywords_item) {
          $result = (false !== stripos($value, trim($keywords_item)));

          // if true then we're done
          if ($result) {
            break;
          }
        }
      }
    }

    return $result;
  }

  // check if the given $content has a certain $author
  function hasauthor($content, $author) {
    $result = false;

    // get the author
    $value = value($content, AUTHOR);
    if (null !== $value) {
      $result = (0 === strcasecmp(trim($author), $value));
    }

    return $result;
  }

  // check if the given $content has a certain $category
  function hascategory($content, $category) {
    $result = false;

    // get the category
    $value = value($content, CATEGORY);
    if (null !== $value) {
      // split the CATEGORY by spaces and iterate through them
      $content_category = explode(SP, $value);
      foreach ($content_category as $content_category_item) {
        // check if the category from the URL matches the CATEGORY
        $result = (0 === strcasecmp(trim($category), trim($content_category_item)));

        // if true then we're done
        if ($result) {
          break;
        }
      }
    }

    return $result;
  }

  // check if the given $content has a certain $date
  function hasdate($content, $year = null, $month = null, $day = null) {
    $result = false;

    // get the date
    $value = value($content, DATE);
    if (null !== $value) {
      if (((null === $year) || is_numeric($year)) &&
          ((null === $month) || is_numeric($month)) &&
          ((null === $day) || is_numeric($day))) {
        $time = strtotime($value);

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
                  return $comparator(value($left, $field), value($right, $field));
                })) {
        // remerge previously split elements
        $result = array_merge($sortable, $unsortable);
      }
    }

    return $result;
  }

  // get the given value or NULL on error
  function value($content, $name) {
    $result = null;

    if ($content instanceof Content) {
      if ($content->isset($name)) {
        $result = trim($content->get($name));

        // if the length of the result is 0 then set it to NULL
        if (0 >= strlen($result)) {
          $result = null;
        }
      }
    }

    return $result;
  }

  // ***** String Manipulation Functions *****

  // get the formatted string and call html() on all $values
  function fhtml($string, ...$values) {
    $result = $string;

    // escape everything
    foreach ($values as $values_key => $values_value) {
      $values[$values_key] = html($values_value);
    }
    $result = sprintf($result, ...$values);

    return $result;
  }

  // escape HTML in the given $string
  function html($string) {
    return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, Main::CHARSET(), false);
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

  // get the translation
  function t($string, $name = null, ...$values) {
    $result = $string;

    if (0 === count($values)) {
      $result = Translate::get($result, $name);
    } else {
      $result = Translate::format($result, $name, ...$values);
    }

    return $result;
  }

  // get the formatted string and call html() on the translation of all $values
  function tfhtml($string, $name = null, ...$values) {
    $result = $string;

    // translate everything
    foreach ($values as $values_key => $values_value) {
      $values[$values_key] = Translate::get($values_value, $name);
    }
    $result = fhtml($result, ...$values);

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

  // ***** URL Functions *****

  // get URI of current page
  function curpage() {
    $result = null;

    // we use the current handler's getUri() method
    if (null !== Handlers::ACTIVE()) {
      $result = _callMethod(Handlers::ACTIVE(), GETURI, Main::PAGEINFO());
    }

    return $result;
  }

  // convert the PAGEINFO to a feed URI
  function feeduri() {
    $result = null;

    switch (Handlers::ACTIVE()) {
      case ARCHIVE_HANDLER:
        $result = FeedArchiveHandler::getUri(Main::PAGEINFO());
        break;

      case AUTHOR_HANDLER:
        $result = FeedAuthorHandler::getUri(Main::PAGEINFO());
        break;

      case CATEGORY_HANDLER:
        $result = FeedCategoryHandler::getUri(Main::PAGEINFO());
        break;

      case HOME_HANDLER:
        $result = FeedHomeHandler::getUri(Main::PAGEINFO());
        break;

      case SEARCH_GET_HANDLER:
        $result = FeedSearchHandler::getUri(Main::PAGEINFO());
        break;
    }

    return $result;
  }

  // get URI of first page
  function firstpage($force = false) {
    $result = null;

    // we use the current handler's getUri() method
    if (null !== Handlers::ACTIVE()) {
      // check that we're not already on the first page
      if ($force || (Main::PAGENUMBER() > Main::PAGEMIN())) {
        // update info to get the URI of the first page
        $info       = Main::PAGEINFO();
        $info[PAGE] = Main::PAGEMIN();

        $result = _callMethod(Handlers::ACTIVE(), GETURI, $info);
      }
    }

    return $result;
  }

  // get URI of last page
  function lastpage($force = false) {
    $result = null;

    // we use the current handler's getUri() method
    if (null !== Handlers::ACTIVE()) {
      // check that we're not already on the last page
      if ($force || (Main::PAGENUMBER() < Main::PAGEMAX())) {
        // update info to get the URI of the last page
        $info       = Main::PAGEINFO();
        $info[PAGE] = Main::PAGEMAX();

        $result = _callMethod(Handlers::ACTIVE(), GETURI, $info);
      }
    }

    return $result;
  }

  // get URI of next page
  function nextpage($force = false) {
    $result = null;

    // we use the current handler's getUri() method
    if (null !== Handlers::ACTIVE()) {
      // check if there's a next page
      if ($force || (Main::PAGENUMBER() < Main::PAGEMAX())) {
        // update info to get the URI of the next page
        $info       = Main::PAGEINFO();
        $info[PAGE] = Main::PAGENUMBER()+1;

        $result = _callMethod(Handlers::ACTIVE(), GETURI, $info);
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

  // get URI of previous page
  function prevpage($force = false) {
    $result = null;

    // we use the current handler's getUri() method
    if (null !== Handlers::ACTIVE()) {
      // check if there's a previous page
      if ($force || (Main::PAGENUMBER() > Main::PAGEMIN())) {
        // update info to get the URI of the next page
        $info       = Main::PAGEINFO();
        $info[PAGE] = Main::PAGENUMBER()-1;

        $result = _callMethod(Handlers::ACTIVE(), GETURI, $info);
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

  // ***** Helper Functions *****

  // compare a date string and return a value like strcmp
  function datecmp($left, $right) {
    $result = 0;

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

  // check if the given $string represents a false state
  function isfalse($string) {
    $string = strtolower(trim($string));

    return ((0 === strcmp($string, "0")) ||
            (0 === strcmp($string, "false")) ||
            (0 === strcmp($string, "nein")) ||
            (0 === strcmp($string, "no")));
  }

  // check if the given $string represents a true state
  function istrue($string) {
    $string = strtolower(trim($string));

    return ((0 === strcmp($string, "1")) ||
            (0 === strcmp($string, "ja")) ||
            (0 === strcmp($string, "true")) ||
            (0 === strcmp($string, "yes")));
  }

  // relocate to the given $uri
  function relocate($uri, $permanent = false, $redirect = false) {
    $result = false;

    if (is_string($uri)) {
      if ($permanent) {
        if ($redirect) {
          http_response_code(308);
        } else {
          http_response_code(301);
        }
      } else {
        if ($redirect) {
          http_response_code(307);
        } else {
          http_response_code(302);
        }
      }

      header("Location: $uri");

      // success
      $result = true;
    }

    return $result;
  }

  // call the widgets and filter them afterwards
  function widgets() {
    $result = array();

    // call the widget plugins
    $widgets = Plugins::run(ON_WIDGETS);

    // filter wrong entries
    foreach ($widgets as $widgets_item) {
      if ($widgets_item instanceof Content) {
        $result[] = $widgets_item;
      }
    }

    // return null if no widget is set
    if (0 >= count($result)) {
      $result = null;
    }

    // filter the widgets and return them
    return Plugins::run(FILTER_WIDGETS, true, $result);
  }

