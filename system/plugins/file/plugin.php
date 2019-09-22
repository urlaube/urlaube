<?php

  /**
    This is the FilePlugin class of the urlau.be CMS.

    This file contains the FilePlugin class of the urlau.be CMS core. This
    plugin simplifies the loading of file-based CMS entries.

    @package urlaube\urlaube
    @version 0.1a12
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  class FilePlugin extends BaseSingleton implements Plugin {

    // CONSTANTS

    const EXTENSION = ".md";
    const FILE      = "file";

    // HELPER FUNCTIONS

    protected static function fileToUri($filename) {
      $result = null;

      if (0 === strpos($filename, static::getPath())) {
        // get relevant part of the filename
        $filename = substr($filename, strlen(static::getPath()));

        // remove file extension
        $filename = notrail($filename, static::EXTENSION);

        // replace DS with US and prepend root URI
        $result = trail(value(Main::class, ROOTURI).strtr($filename, DS, US), US);
      }

      return $result;
    }

    public static function getPath() {
      // derive user paths
      $path = realpath(USER_PATH);
      if ((false !== $path) && is_dir($path)) {
        $path = trail($path, DS);
      } else {
        $path = ROOTPATH."user".DS;
      }

      return $path."content".DS;
    }

    // RUNTIME FUNCTIONS

    protected static function loadContent($filename, $skipcontent = false, $filter = null) {
      $result = null;

      // read the file as an array
      $file = file($filename);
      if (false !== $file) {
        // iterate through $file to read all attributes
        $index = 0;
        while ($index < count($file)) {
          $pos = strpos($file[$index], COL);
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

          // preset result
          if (null === $result) {
            $result = new Content();
          }

          // set content string
          $result->set(CONTENT, trim(implode($file)));
        }

        if (null !== $result) {
          // try to preset the update field to the file modification time
          $result->preset(UPDATE, date(value(Main::class, TIMEFORMAT), filemtime($filename)));

          // only set the file name and URI when there is a result
          $result->set(static::FILE, $filename);
          $result->set(URI,          static::fileToUri($filename));
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

    protected static function loadContentDir($dirname, $recursive = false, $skipcontent = false, $filter = null) {
      $result = null;

      // fix $dirname
      $dirname = trail($dirname, DS);

      // prepare $files array
      $files = scandir($dirname, SCANDIR_SORT_ASCENDING);
      if (false !== $files) {
        // iterate through the file list
        foreach ($files as $files_item) {
          // ignore current and previous dir entry
          if (("." !== $files_item) && (".." !== $files_item)) {
            // what to do if we encounter a file that has the correct extension
            if (is_file($dirname.$files_item) && istrail($files_item, static::EXTENSION)) {
              // load the content from file
              $temp = static::loadContent($dirname.$files_item, $skipcontent, $filter);
              if (null !== $temp) {
                // preset result
                if (null === $result) {
                  $result = [];
                }

                $result[] = $temp;
              }
            } else {
              // what to do if we encounter a folder and want to read recursively
              if (is_dir($dirname.$files_item) && $recursive) {
                // load the content from folder
                $temp = static::loadContentDir($dirname.$files_item, $recursive, $skipcontent, $filter);
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

      return $result;
    }

    public static function run($name = null, $recursive = false, $skipcontent = false, $filter = null) {
      $result = null;

      // fix $name
      $name = strtr($name, US, DS);

      if ($recursive) {
        // try to derive a potential dirname from the given name
        $dirname = trail(realpath(static::getPath().nolead($name, DS)), DS);

        // check if the dirname is located in the user content path and represents a folder
        if ((0 === strpos($dirname, static::getPath())) && is_dir($dirname)) {
          // load a whole directory
          $result = static::loadContentDir($dirname, $recursive, $skipcontent, $filter);
        }
      } else {
        // try to derive a potential filename from the given name
        $filename = realpath(static::getPath().notrail(nolead($name, DS), DS).FilePlugin::EXTENSION);

        // check if the filename is located in the user content path, really ends with the extension and represents a file,
        if ((0 === strpos($filename, static::getPath())) && istrail($filename, static::EXTENSION) && is_file($filename)) {
          // load a single file
          $result = static::loadContent($filename, $skipcontent, $filter);
        }
      }

      return $result;
    }

  }

  // register plugin
  Plugins::register(FilePlugin::class, "run", ON_CONTENT);
