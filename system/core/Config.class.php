<?php

  /**
    This is the Config class of the urlau.be CMS core.

    This file contains the Config class of the urlau.be CMS core. This class is the single entrypoint to configure
    all core class fields. By using this class the user does not have to know which configuration value belongs to
    which core class as the Config class is used as a facade.

    @package urlaube\urlaube
    @version 0.1a1
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  if (!class_exists("Config")) {
    class Config extends Base {

      // GETTER FUNCTIONS

      public static function getCharset() {
        return Main::CHARSET();
      }

      public static function getContent() {
        return Main::CONTENT();
      }

      public static function getDebugmode() {
        return Debug::DEBUGMODE();
      }

      public static function getHandler() {
        return null;
      }

      public static function getHostname() {
        return Main::HOSTNAME();
      }

      public static function getLanguage() {
        return Translations::LANGUAGE();
      }

      public static function getLoglevel() {
        return Debug::LOGLEVEL();
      }

      public static function getLogtimeformat() {
        return Debug::TIMEFORMAT();
      }

      public static function getLogtarget() {
        return Debug::LOGTARGET();
      }

      public static function getMethod() {
        return Main::METHOD();
      }

      public static function getPageinfo() {
        return Main::PAGEINFO();
      }

      public static function getPagesize() {
        return Main::PAGESIZE();
      }

      public static function getPlugin() {
        return null;
      }

      public static function getPort() {
        return Main::PORT();
      }

      public static function getProtocol() {
        return Main::PROTOCOL();
      }

      public static function getRooturi() {
        return Main::ROOTURI();
      }

      public static function getSitename() {
        return Main::SITENAME();
      }

      public static function getSiteslogan() {
        return Main::SITESLOGAN();
      }

      public static function getTheme() {
        return null;
      }

      public static function getThemename() {
        return Themes::THEMENAME();
      }

      public static function getTimezone() {
        return Main::TIMEZONE();
      }

      public static function getUri() {
        return Main::URI();
      }

      // SETTER FUNCTIONS

      public static function setCharset($value) {
        return Main::CHARSET($value);
      }

      public static function setContent($value) {
        return Main::CONTENT($value);
      }

      public static function setDebugmode($value) {
        return Debug::DEBUGMODE($value);
      }

      public static function setHandler($name, ...$value) {
        $result = null;

        if (0 === count($value)) {
          $result = Handlers::get($name);
        } else {
          $result = Handlers::set($name, $value[0]);
        }

        return $result;
      }

      public static function setHostname($value) {
        return Main::HOSTNAME($value);
      }

      public static function setLanguage($value) {
        return Translations::LANGUAGE($value);
      }

      public static function setLoglevel($value) {
        return Debug::LOGLEVEL($value);
      }

      public static function setLogtimeformat($value) {
        return Debug::TIMEFORMAT($value);
      }

      public static function setLogtarget($value) {
        return Debug::LOGTARGET($value);
      }

      public static function setMethod($value) {
        return Main::METHOD($value);
      }

      public static function setPageinfo($value) {
        return Main::PAGEINFO($value);
      }

      public static function setPagesize($value) {
        return Main::PAGESIZE($value);
      }

      public static function setPlugin($name, ...$value) {
        $result = null;

        if (0 === count($value)) {
          $result = Plugins::get($name);
        } else {
          $result = Plugins::set($name, $value[0]);
        }

        return $result;
      }

      public static function setPort($value) {
        return Main::PORT($value);
      }

      public static function setProtocol($value) {
        return Main::PROTOCOL($value);
      }

      public static function setRooturi($value) {
        return Main::ROOTURI($value);
      }

      public static function setSitename($value) {
        return Main::SITENAME($value);
      }

      public static function setSiteslogan($value) {
        return Main::SITESLOGAN($value);
      }

      public static function setTheme($name, ...$value) {
        $result = null;

        if (0 === count($value)) {
          $result = Themes::get($name);
        } else {
          $result = Themes::set($name, $value[0]);
        }

        return $result;
      }

      public static function setThemename($value) {
        $result = Themes::THEMENAME($value);
      }

      public static function setTimezone($value) {
        return Main::TIMEZONE($value);
      }

      public static function setUri($value) {
        return Main::URI($value);
      }

    }
  }

