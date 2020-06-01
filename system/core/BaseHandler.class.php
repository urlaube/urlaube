<?php

  /**
    This is the BaseHandler class of the urlau.be CMS core.

    This file contains the BaseHandler class of the urlau.be CMS core. Most of
    the system handlers do the same thing over and over again. This class helps
    to reduce the amount of duplicate code.

    @package urlaube\urlaube
    @version 0.1a12
    @author  Yahe <hello@yahe.sh>
    @since   0.1a7
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  abstract class BaseHandler extends BaseSingleton implements Handler {

    // ABSTRACT FUNCTIONS

    protected static abstract function getResult($metadata, &$cachable);
    protected static abstract function prepareMetadata($metadata);

    // HELPER VARIABLE

    protected static $disabledPlugins = null;

    // HELPER FUNCTIONS

    public static function disableFilterContent($plugins) {
      $result = $plugins;

      // preset variable
      static::$disabledPlugins = [];

      if (is_array($result)) {
        foreach ($result as $key => $value) {
          // do not store the current function locally
          if ((0 === strcasecmp(FILTER_PLUGINS, value($value, Plugins::EVENT))) &&
              (0 === strcasecmp(static::class,  value($value, Plugins::ENTITY))) &&
              (0 === strcasecmp(__FUNCTION__,   value($value, Plugins::FUNCTION)))) {
            // unregister plugin
            unset($result[$key]);
          } elseif (0 === strcasecmp(FILTER_CONTENT, value($value, Plugins::EVENT))) {
            // store plugin locally
            static::$disabledPlugins[] = $value;

            // unregister plugin
            unset($result[$key]);
          }
        }

        // renumber result
        $result = array_values($result);
      }

      return $result;
    }

    public static function enableFilterContent($plugins) {
      $result = $plugins;

      if (is_array($result)) {
        foreach ($result as $key => $value) {
          // unregister the currently called function
          if ((0 === strcasecmp(FILTER_PLUGINS, value($value, Plugins::EVENT))) &&
              (0 === strcasecmp(static::class,  value($value, Plugins::ENTITY))) &&
              (0 === strcasecmp(__FUNCTION__,   value($value, Plugins::FUNCTION)))) {
            // unregister plugin
            unset($result[$key]);
          }
        }

        if (is_array(static::$disabledPlugins)) {
          // re-register the locally stored plugins
          foreach (static::$disabledPlugins as $disabledPlugin) {
            $result[] = $disabledPlugin;
          }
          static::$disabledPlugins = null;
        }

        // renumber result
        $result = array_values($result);
      }

      return $result;
    }

    // INTERFACE FUNCTIONS

    public static function getContent($metadata, &$pagecount) {
      $pagecount = 1;
      $result    = null;

      // prepare metadata for sanitization
      $metadata = preparecontent($metadata, static::OPTIONAL, static::MANDATORY);
      if ($metadata instanceof Content) {
        // sanitize metadata
        $metadata = preparecontent(static::prepareMetadata($metadata), static::OPTIONAL, static::MANDATORY);
        if ($metadata instanceof Content) {
          // try to get data from cache
          if (getcache(static::getUri($metadata), $data, static::class)) {
            // check that the returned content matches
            if (is_array($data) && isset($data[CONTENT]) && isset($data[PAGECOUNT])) {
              $pagecount = $data[PAGECOUNT];
              $result    = $data[CONTENT];
            }
          } else {
            // disable FILTER_CONTENT plugins
            Plugins::register(static::class, "disableFilterContent", FILTER_PLUGINS);
            Plugins::filter();

            $result = preparecontent(static::getResult($metadata, $cachable));
            if (null !== $result) {
              // sort and paginate the content if it is an array
              if (is_array($result)) {
                // sort entries by DATE
                $result = sortcontent($result, DATE,
                                      function ($left, $right) {
                                        // reverse-sort
                                        return -datecmp($left, $right);
                                      });

                // handle pagination if the PAGE metadate is supported
                if ($metadata->isset(PAGE)) {
                  // set the maximum page count
                  $pagecount = ceil(count($result)/value(Main::class, PAGESIZE));

                  // execute the pagination
                  $result = paginate($result, value($metadata, PAGE));
                }
              }

              if ($cachable) {
                // try to set data in cache
                setcache(static::getUri($metadata), [CONTENT => $result, PAGECOUNT => $pagecount], static::class);
              }
            }

            // enable FILTER_CONTENT plugins
            Plugins::register(static::class, "enableFilterContent", FILTER_PLUGINS);
            Plugins::filter();
          }

          // filter the retrieved content
          $result = preparecontent(Plugins::run(FILTER_CONTENT, true, $result));
        }
      }

      return $result;
    }

    public static function getUri($metadata){
      $result = null;

      // prepare metadata for sanitization
      $metadata = preparecontent($metadata, static::OPTIONAL, static::MANDATORY);
      if ($metadata instanceof Content) {
        // handle pagination if the PAGE metadate is supported
        if ($metadata->isset(PAGE)) {
          $page = value($metadata, PAGE);
          if (is_numeric($page)) {
            $metadata->set(PAGE, intval($page));
          } else {
            // set page to the default value if it is set
            if (is_array(static::OPTIONAL) and array_key_exists(PAGE, static::OPTIONAL)) {
              $metadata->set(PAGE, static::OPTIONAL[PAGE]);
            } else {
              // set page to a standard value
              $metadata->set(PAGE, 1);
            }
          }
        }

        // sanitize metadata
        $metadata = preparecontent(static::prepareMetadata($metadata), static::OPTIONAL, static::MANDATORY);
        if ($metadata instanceof Content) {
          // get the base URI
          $result = value(Main::class, ROOTURI);

          // append the mandatory URI parts
          if (is_array(static::MANDATORY)) {
            foreach (static::MANDATORY as $value) {
              $result .= strtolower(trim($value)).EQ.value($metadata, $value).US;
            }
          }

          // append the optional URI parts
          if (is_array(static::OPTIONAL)) {
            foreach (static::OPTIONAL as $key => $value) {
              // only append it if they value differs from the default
              if ($value !== value($metadata, $key)) {
                $result .= strtolower(trim($key)).EQ.value($metadata, $key).US;
              }
            }
          }
        }
      }

      return $result;
    }

    public static function parseUri($uri) {
      $result = null;

      // prepare metadata for sanitization
      $metadata = preparecontent(parseuri($uri, static::REGEX), static::OPTIONAL, static::MANDATORY);
      if ($metadata instanceof Content) {
        // handle pagination if the PAGE metadate is supported
        if ($metadata->isset(PAGE)) {
          $page = value($metadata, PAGE);
          if (is_numeric($page)) {
            $metadata->set(PAGE, intval($page));
          } else {
            // set page to the default value if it is set
            if (is_array(static::OPTIONAL) and array_key_exists(PAGE, static::OPTIONAL)) {
              $metadata->set(PAGE, static::OPTIONAL[PAGE]);
            } else {
              // set page to a standard value
              $metadata->set(PAGE, 1);
            }
          }
        }

        // sanitize metadata
        $metadata = preparecontent(static::prepareMetadata($metadata), static::OPTIONAL, static::MANDATORY);
        if ($metadata instanceof Content) {
          $result = $metadata;
        }
      }

      return $result;
    }

    // RUNTIME FUNCTIONS

    public static function run() {
      $result = false;

      $metadata = static::parseUri(relativeuri());
      if (null !== $metadata) {
        // set the metadata to be processed by plugins and the theme
        Main::set(METADATA, $metadata);

        $content = preparecontent(static::getContent($metadata, $pagecount));
        if (null !== $content) {
          // check if the URI is correct
          $fixed = static::getUri($metadata);
          if (0 !== strcmp(value(Main::class, URI), $fixed)) {
            relocate($fixed.querystring(), false, true);
          } else {
            // set the content to be processed by plugins and the theme
            Main::set(CONTENT, $content);

            // handle pagination if the PAGE metadate is supported
            if ($metadata->isset(PAGE)) {
              Main::set(PAGE,      value($metadata, PAGE));
              Main::set(PAGECOUNT, $pagecount);
            } else {
              Main::set(PAGE,      1);
              Main::set(PAGECOUNT, 1);
            }

            // transfer the handling to the Themes class
            Themes::run();
          }

          // we handled this page
          $result = true;
        }
      }

      return $result;
    }

  }
