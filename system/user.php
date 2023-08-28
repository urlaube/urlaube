<?php

  /**
    These are the user functions of the urlau.be CMS core.

    This file contains the user functions of the urlau.be CMS core. Handler,
    plugin and them developers may rely on these functions as they will only
    change with prior notice.

    @package urlaube\urlaube
    @version 0.1a12
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  // ***** CONTENT FUNCTIONS *****

  // find content element by field value
  function findcontent($content, $field, $value) {
    $result = null;

    if ($content instanceof Content) {
      $content_value = value($content, $field);
      if (null !== $content_value) {
        if (0 === strcasecmp(trim($value), trim($content_value))) {
          $result = $content;
        }
      }
    } else {
      if (is_array($content)) {
        foreach ($content as $content_item) {
          $content_value = value($content_item, $field);
          if (null !== $content_value) {
            if (0 === strcasecmp(trim($value), trim($content_value))) {
              $result = $content_item;

              break;
            }
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
      $time = strtotime($value);

      // only proceed if DATE is parsable
      if (false !== $time) {
        $date = getdate($time);

        // compare DATE with $year, $month and $day
        $result = (((!is_numeric($year))  || ($date["year"] === intval($year))) &&
                   ((!is_numeric($month)) || ($date["mon"]  === intval($month))) &&
                   ((!is_numeric($day))   || ($date["mday"] === intval($day))));
      }
    }

    return $result;
  }

  // check if the given $content has one or more $keywords in $field
  function haskeywords($content, $field, $keywords) {
    $result = false;

    // get the requested field
    $value = value($content, $field);
    if (null !== $value) {
      // make sure that we work with an array
      if (!is_array($keywords)) {
        $keywords = [$keywords];
      }

      // check for each keyword if it is contained in the $field
      foreach ($keywords as $keywords_item) {
        $result = (false !== stripos($value, trim($keywords_item)));

        // if true then we're done
        if ($result) {
          break;
        }
      }
    }

    return $result;
  }

  // return the entries of the given page
  function paginate($array, $page) {
    $result = preparecontent(Plugins::run(FILTER_PAGINATE, true, $array));

    if (is_array($result) && is_numeric($page)) {
      $result = array_slice($result, ($page-1)*value(Main::class, PAGESIZE), value(Main::class, PAGESIZE), false);

      // if the result is empty, we set it to null
      if (0 === count($result)) {
        $result = null;
      }
    }

    return $result;
  }

  // prepare content (array) and make sure that mandatory fields are set,
  // preset fields with default values
  function preparecontent($content, $defaults = null, $mandatory = null) {
    $result = null;

    if ($content instanceof Content) {
      $failed = false;

      // preset fields with default values
      if (is_array($defaults)) {
        foreach ($defaults as $key => $value) {
          $content->preset($key, $value);
        }
      }

      if (is_array($mandatory)) {
        // check if all mandatory fields are set
        foreach ($mandatory as $mandatory_item) {
          $failed = (!$content->isset($mandatory_item)) || $failed;
        }
      }

      if (!$failed) {
        $result = $content;
      }
    } else {
      if (is_array($content)) {
        $result = [];

        // iterate through the array
        foreach ($content as $content_item) {
          if ($content_item instanceof Content) {
            $failed = false;

            // preset fields with default values
            if (is_array($defaults)) {
              foreach ($defaults as $key => $value) {
                $content_item->preset($key, $value);
              }
            }

            if (is_array($mandatory)) {
              // check if all mandatory fields are set
              foreach ($mandatory as $mandatory_item) {
                $failed = (!$content_item->isset($mandatory_item)) || $failed;
              }
            }

            if (!$failed) {
              $result[] = $content_item;
            }
          }
        }

        if (0 >= count($result)) {
          $result = null;
        }
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
      $sortable   = [];
      $unsortable = [];
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
        $result = $content->get($name);
      }
    } else {
      if (is_subclass_of($content, BaseConfig::class)) {
        if ($content::isset($name)) {
          $result = $content::get($name);
        }
      }
    }

    if (is_string($result)) {
      $result = trim($result);

      // if the length of the result is 0 then set it to NULL
      if (0 >= strlen($result)) {
        $result = null;
      }
    }

    return $result;
  }

  // ***** STRING MANIPULATION FUNCTIONS *****

  // get the formatted string and call html() on all $values
  function fhtml($string, ...$values) {
    $result = $string;

    // escape everything
    foreach ($values as $values_key => $values_value) {
      $values[$values_key] = html($values_value);
    }
    $result = vsprintf($result, $values);

    return $result;
  }

  // escape HTML in the given $string
  function html($string) {
    $result = $string;

    if (is_string($result)) {
      $result = htmlspecialchars($result, ENT_QUOTES | ENT_HTML5, value(Main::class, CHARSET), false);
    }

    return $result;
  }

  // checks if $string starts with $lead
  function islead($string, $lead) {
    return ($lead === substr(($string ?? ""), 0, strlen($lead)));
  }

  // checks if $string ends with $trail
  function istrail($string, $trail) {
    return ($trail === substr(($string ?? ""), -strlen($trail)));
  }

  // checks if $string starts with $lead
  // if not then $lead is prepended to $string
  function lead($string, $lead) {
    $result = ($string ?? "");

    if ($lead !== substr($result, 0, strlen($lead))) {
      $result = $lead.$result;
    }

    return $result;
  }

  // checks if $string starts with $lead
  // if it does then $lead is removed from $string
  function nolead($string, $lead) {
    $result = ($string ?? "");

    // repeat until there's no match
    while (0 === strpos($result, $lead)) {
      $result = substr($result, strlen($lead));
    }

    return $result;
  }

  // checks if $string ends with $trail
  // if it does then $trail is removed from $string
  function notrail($string, $trail) {
    $result = ($string ?? "");

    // repeat until there's no match
    while ($trail === substr($result, -strlen($trail))) {
      $result = substr($result, 0, -strlen($trail));
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
    $result = ($string ?? "");

    if ($trail !== substr($result, -strlen($trail))) {
      $result = $result.$trail;
    }

    return $result;
  }

  // ***** URL FUNCTIONS *****

  // get the absolute URL from a relative URI
  function absoluteurl($uri = null) {
    // find out if we have to prepend the port number
    $port = "";
    if (((0 === strcasecmp(HTTP_PROTOCOL, value(Main::class, PROTOCOL))) &&
         (HTTP_PORT !== value(Main::class, PORT))) ||
        ((0 === strcasecmp(HTTPS_PROTOCOL, value(Main::class, PROTOCOL))) &&
         (HTTPS_PORT !== value(Main::class, PORT)))) {
      $port = COL.value(Main::class, PORT);
    }

    return value(Main::class, PROTOCOL).
           value(Main::class, HOSTNAME).
           $port.
           value(Main::class, ROOTURI).
           nolead(relativeuri($uri), US);
  }

  // get URI of current page
  function curpage() {
    $result = null;

    // we use the current handler's getUri() method
    if (null !== Handlers::getActive()) {
      $result = _callMethod(Handlers::getActive(), GETURI, [value(Main::class, METADATA)]);
    }

    return $result;
  }

  // convert the PAGEINFO to a feed URI
  function feeduri() {
    $result = null;

    // check that only supported feed sources are handled
    if (in_array(Handlers::getActive(), FeedHandler::SOURCES)) {
      // update metadata to get the feed URI
      $metadata = value(Main::class, METADATA);
      if ($metadata instanceof Content) {
        $metadata = $metadata->clone();
        $metadata->set(FeedHandler::FEED, Handlers::getActive());

        $result = FeedHandler::getUri($metadata);
      }
    }

    return $result;
  }

  // get URI of first page
  function firstpage($force = false) {
    $result = null;

    // we use the current handler's getUri() method
    if (null !== Handlers::getActive()) {
      // check that we're not already on the first page
      if ($force || (value(Main::class, PAGE) > 1)) {
        // update metadata to get the URI of the first page
        $metadata = value(Main::class, METADATA);
        if ($metadata instanceof Content) {
          $metadata = $metadata->clone();
          $metadata->set(PAGE, 1);

          $result = _callMethod(Handlers::getActive(), GETURI, [$metadata]);
        }
      }
    }

    return $result;
  }

  // get URI of last page
  function lastpage($force = false) {
    $result = null;

    // we use the current handler's getUri() method
    if (null !== Handlers::getActive()) {
      // check that we're not already on the last page
      if ($force || (value(Main::class, PAGE) < value(Main::class, PAGECOUNT))) {
        // update metadata to get the URI of the last page
        $metadata = value(Main::class, METADATA);
        if ($metadata instanceof Content) {
          $metadata = $metadata->clone();
          $metadata->set(PAGE, value(Main::class, PAGECOUNT));

          $result = _callMethod(Handlers::getActive(), GETURI, [$metadata]);
        }
      }
    }

    return $result;
  }

  // get URI of next page
  function nextpage($force = false) {
    $result = null;

    // we use the current handler's getUri() method
    if (null !== Handlers::getActive()) {
      // check if there's a next page
      if ($force || (value(Main::class, PAGE) < value(Main::class, PAGECOUNT))) {
        // update metadata to get the URI of the next page
        $metadata = value(Main::class, METADATA);
        if ($metadata instanceof Content) {
          $metadata = $metadata->clone();
          $metadata->set(PAGE, value(Main::class, PAGE)+1);

          $result = _callMethod(Handlers::getActive(), GETURI, [$metadata]);
        }
      }
    }

    return $result;
  }

  // parse the URI based on a REGEX and only return named subpatterns
  function parseuri($uri, $regex) {
    $result = null;

    // check if the regex matches
    if (1 === preg_match($regex, $uri, $matches)) {
      // if it does, always return a Content object
      $result = new Content();

      foreach ($matches as $key => $value) {
        // only transfer named subpatterns to the Content object
        if (1 === preg_match("~^[A-Za-z][0-9A-Za-z]*$~", $key)) {
          $result->set($key, $value);
        }
      }
    }

    return $result;
  }

  // convert the given path to a URI
  function path2uri($path) {
    $result = null;

    // check if the path starts with the root path
    $path = realpath($path);
    if (0 === strpos($path, ROOTPATH)) {
      // remove the root path
      $path = substr($path, strlen(ROOTPATH));

      // prepend the root URI
      $result = value(Main::class, ROOTURI).strtr(nolead($path, DS), DS, US);
    }

    return $result;
  }

  // get URI of previous page
  function prevpage($force = false) {
    $result = null;

    // we use the current handler's getUri() method
    if (null !== Handlers::getActive()) {
      // check if there's a previous page
      if ($force || (value(Main::class, PAGE) > 1)) {
        // update metadata to get the URI of the next page
        $metadata = value(Main::class, METADATA);
        if ($metadata instanceof Content) {
          $metadata = $metadata->clone();
          $metadata->set(PAGE, value(Main::class, PAGE)-1);

          $result = _callMethod(Handlers::getActive(), GETURI, [$metadata]);
        }
      }
    }

    return $result;
  }

  // get the URL-encoded query string
  function querystring($parameters = null) {
    $result = null;

    // use the GET parameters if no value is given
    if (null === $parameters) {
      $parameters = $_GET;
    }

    if (is_array($parameters)) {
      $parts = [];
      foreach ($parameters as $key => $value) {
        $parts[] = urlencode($key).EQ.urlencode($value);
      }

      // set result
      if (0 < count($parts)) {
        $result = QM.implode(AMP, $parts);
      }
    }

    return $result;
  }

  // get the relative URI from a URI based on the root URI
  function relativeuri($uri = null) {
    $result = null;

    // use a specific URI if $uri is null
    if (null === $uri) {
      $uri = value(Main::class, URI);
    }

    // check if the URI starts with the root URI
    $uri = lead(parse_url($uri, PHP_URL_PATH), US);
    if (0 === strpos($uri, value(Main::class, ROOTURI))) {
      $result = lead(substr($uri, strlen(value(Main::class, ROOTURI))), US);
    }

    return $result;
  }

  // convert the given URI to a path
  function uri2path($uri) {
    $result = null;

    // only handle the relative URI
    $uri = relativeuri($uri);
    if (null !== $uri) {
      $result = ROOTPATH.strtr(nolead($uri, US), US, DS);
    }

    return ;
  }

  // ***** HELPER FUNCTIONS *****

  // call the content plugins and filter them afterwards
  function callcontent($name = null, $recursive = false, $skipcontent = false, $filter = null) {
    // call the content plugins
    $result = preparecontent(Plugins::run(ON_CONTENT, false, null, [$name, $recursive, $skipcontent, $filter]));

    // filter the content
    $result = preparecontent(Plugins::run(FILTER_CONTENT, true, $result));

    return $result;
  }

  // call the widget plugins and filter them afterwards
  function callwidgets() {
    // call the widget plugins
    $result = preparecontent(Plugins::run(ON_WIDGETS));

    // filter the widgets
    $result = preparecontent(Plugins::run(FILTER_WIDGETS, true, $result));

    return $result;
  }

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

  // call cache plugins and get cached content
  function getcache($key, &$value, $name = null) {
    $result = false;

    // only proceed when caching is active
    if (value(Main::class, CACHE)) {
      // store value temporarily first
      $temp = null;

      // try to store content through a caching plugin
      $cached = Plugins::run(GET_CACHE, false, null, [$key, &$temp, $name]);

      // check that the returned content matches
      if (is_array($temp) && isset($temp[CONTENT]) && isset($temp[DATE])) {
        // check that the kill date has not been reached
        if ((0 >= $temp[DATE]) || (time() <= $temp[DATE])) {
          // find out if at least on plugin was called and returned TRUE
          if (is_array($cached)) {
            foreach ($cached as $cached_item) {
              // check if a caching plugin returned true
              $result = (true === $cached_item);
              if ($result) {
                break;
              }
            }

            // store result permanently
            if ($result) {
              $value = $temp[CONTENT];
            }
          }
        }
      }
    }

    return $result;
  }

  // check if the given $string represents a false state
  function isfalse($string) {
    $result = false;

    if (is_string($string)) {
      $string = strtolower(trim($string));

      $result = ((0 === strcmp($string, "0"))     ||
                 (0 === strcmp($string, "false")) ||
                 (0 === strcmp($string, "nein"))  ||
                 (0 === strcmp($string, "no")));
    }

    return $result;
  }

  // check if the given $string represents a true state
  function istrue($string) {
    $result = false;

    if (is_string($string)) {
      $string = strtolower(trim($string));

      $result = ((0 === strcmp($string, "1"))    ||
                 (0 === strcmp($string, "ja"))   ||
                 (0 === strcmp($string, "true")) ||
                 (0 === strcmp($string, "yes")));
    }

    return $result;
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

  // call cache plugins and set cached content
  function setcache($key, $value, $name = null, $age = 0) {
    $result = false;

    // only proceed when caching is active
    if (value(Main::class, CACHE)) {
      // only proceed when the cached value cannot be retrieved anymore
      if (!getcache($key, $temp, $name)) {
        // check if the cache age shall be set to the configured cache age
        if (0 === $age) {
          $age = value(Main::class, CACHEAGE);
        }

        // get the date when the cached value shall be killed,
        // negative values prevent the value from being killed
        $killdate = -1;
        if (0 < $age) {
          $killdate = time()+$age;
        }

        // try to store content through a caching plugin
        $cached = Plugins::run(SET_CACHE, false, null, [$key, [CONTENT => $value, DATE => $killdate], $name]);

        // find out if at least on plugin was called and returned TRUE
        if (is_array($cached)) {
          foreach ($cached as $cached_item) {
            // check if a caching plugin returned true
            $result = (true === $cached_item);
            if ($result) {
              break;
            }
          }
        }
      }
    }

    return $result;
  }
