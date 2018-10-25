<?php

  /**
    This is the FilePlugin class of the urlau.be CMS.

    This file contains the FilePlugin class of the urlau.be CMS core. This
    plugin simplifies the loading of file-based CMS entries.

    @package urlaube\urlaube
    @version 0.1a8
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class FilePlugin extends BaseSingleton implements Plugin {

    // CONSTANTS

    const EXTENSION = ".md";

    // HELPER FUNCTIONS

    protected static function fileToUri($filename) {
      $result = null;

      if (0 === strpos($filename, USER_CONTENT_PATH)) {
        // get relevant part of the filename
        $filename = substr($filename, strlen(USER_CONTENT_PATH));

        // remove file extension
        $filename = notrail($filename, static::EXTENSION);

        // replace DS with US and prepend root URI
        $result = trail(value(Main::class, ROOTURI).strtr($filename, DS, US), US);
      }

      return $result;
    }

    // RUNTIME FUNCTIONS

    public static function loadContent($filename, $skipcontent = false, $filter = null) {
      $result = null;

      // fix $filename
      $filename = realpath($filename);

      // check if the file is located in the user content path
      if (0 === strpos($filename, USER_CONTENT_PATH)) {
        // check if the file exists
        if (is_file($filename) && istrail($filename, static::EXTENSION)) {
          // read the file as an array
          $file = file($filename);
          if (false !== $file) {
            // iterate through $file to read all attributes
            $index = 0;
            while ($index < count($file)) {
              $pos = strpos($file[$index], ":");
              if (false !== $pos) {
                $left  = trim(substr($file[$index], 0, $pos));
                $right = trim(substr($file[$index], $pos+1));

                // only proceed when the name on the left is given
                if (0 < strlen($left)) {
                  // preset result
                  if (null === $result) {
                    $result = new Content();
                  }

                  $result->set($left, $right);
                } else {
                  Logging::log("ignored line because it does not contain a field name", Logging::DEBUG);
                }
              } else {
                // check if this is the empty line
                if (0 === strlen(trim($file[$index]))) {
                  // break the loop
                  break;
                } else {
                  // ignore the line
                  Logging::log("ignored line because it does not contain a colon", Logging::DEBUG);
                }
              }

              // increment index
              $index++;
            }

            // try to set the content
            if (!$skipcontent) {
              // delete all lines that do not belong to the content
              for ($counter = $index-1; $counter >= 0; $counter--) {
                unset($file[$counter]);
              }

              // get content string
              $content = trim(implode($file));
              if (0 < strlen($content)) {
                // preset result
                if (null === $result) {
                  $result = new Content();
                }

                $result->set(CONTENT, $content);
              }
            }

            if (null !== $result) {
              // try to preset the update field to the file modification time
              $result->preset(UPDATE, date(value(Main::class, TIMEFORMAT), filemtime($filename)));

              // only set the file name and URI when there is a result
              $result->set(FILE, $filename);
              $result->set(URI,  static::fileToUri($filename));
            }
          }
        }
      }

      // call the filter function if one is given
      if (null !== $result) {
        if (is_callable($filter)) {
          // if the filter wants to drop the entry it has to return null
          $result = $filter($result);
        }
      }

      return $result;
    }

    public static function loadContentDir($dirname, $skipcontent = false, $filter = null, $recursive = false) {
      $result = null;

      if (is_dir($dirname)) {
        $dirname = trail($dirname, DS);

        // prepare $files array
        $files = scandir($dirname, SCANDIR_SORT_ASCENDING);
        if (false !== $files) {
          // iterate through the file list
          foreach ($files as $files_item) {
            if (is_file($dirname.$files_item)) {
              $temp = static::loadContent($dirname.$files_item, $skipcontent, $filter);
              if (null !== $temp) {
                // preset result
                if (null === $result) {
                  $result = [];
                }

                $result[] = $temp;
              }
            } else {
              // read files recursively
              if (("." !== $files_item) && (".." !== $files_item)) {
                if (is_dir($dirname.$files_item) && $recursive) {
                  $temp = static::loadContentDir($dirname.$files_item, $skipcontent, $filter, $recursive);
                  if (is_array($temp)) {
                    // preset result
                    if (null === $result) {
                      $result = [];
                    }

                    $result = array_merge($result, $temp);
                  }
                }
              }
            }
          }
        }
      }

      return $result;
    }

  }
