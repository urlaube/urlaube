<?php

  /**
    These are the recommended constants of the urlau.be CMS core.

    This file contains the recommended constants of the urlau.be CMS core. These
    are provided to handler, plugin and theme developers so that they can decide
    on often-used placeholders.

    @package urlaube\urlaube
    @version 0.1a10
    @author  Yahe <hello@yahe.sh>
    @since   0.1a7
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  // STATIC CONSTANTS

  // recommended content fields
  define("AUTHOR",      "author");      // author of an entry
  define("CATEGORY",    "category");    // list of categories of an entry
  // CONTENT                            // text content of an entry
  define("DATE",        "date");        // publication date of an entry
  define("DESCRIPTION", "description"); // description of an entry
  define("PREVIEW",     "preview");     // preview image of an entry
  define("TITLE",       "title");       // title of an entry
  define("UPDATE",      "update");      // date of the last update of an entry
  // URI                                // uri of an entry

  // recommended theme configuration
  // AUTHOR                           // author of the website
  define("CANONICAL",  "canonical");  // canonical URL of the website
  // CHARSET                          // charset of the website
  define("COPYRIGHT",  "copyright");  // copyright disclaimer of the website
  // DESCRIPTION                      // description of the website
  define("FAVICON",    "favicon");    // favicon.ico URL of the website
  define("KEYWORDS",   "keywords");   // keywords of the website
  // LANGUAGE                         // language of the website
  define("LOGO",       "logo");       // logo URL of the website
  define("MENU",       "menu");       // menu entries of the website
  define("PAGENAME",   "pagename");   // name of the current page
  define("SITENAME",   "sitename");   // name of the website
  define("SITESLOGAN", "siteslogan"); // slogan of the website
  // TIMEFORMAT                       // formatting of date strings
  // TITLE                            // head title of the website

  // recommended widget fields
  // CONTENT // content of the widget
  // TITLE   // title of the widget

  // define theme trigger events
  // AFTER_* and BEFORE_* plugins just get triggered
  define("AFTER_BODY",     "after_body");     // should be called by a theme after the body is generated
  define("AFTER_FOOTER",   "after_footer");   // should be called by a theme after the footer is generated
  define("AFTER_HEAD",     "after_head");     // should be called by a theme after the head is generated
  define("AFTER_SIDEBAR",  "after_sidebar");  // should be called by a theme after the sidebar is generated
  define("BEFORE_BODY",    "before_body");    // should be called by a theme before the body is generated
  define("BEFORE_FOOTER",  "before_footer");  // should be called by a theme before the footer is generated
  define("BEFORE_HEAD",    "before_head");    // should be called by a theme before the head is generated
  define("BEFORE_SIDEBAR", "before_sidebar"); // should be called by a theme before the sidebar is generated

  // define handler priority values
  define("FIXURL",           -50); // used by user handlers before FixUrlHandler is called
  define("FIXURL_HANDLER",   -40); // FixUrlHandler
  define("ADDSLASH",         -30); // used by user handlers before AddSlashHandler is called
  define("ADDSLASH_SYSTEM",  -20); // used by system handlers before AddSlashHandler is called
  define("ADDSLASH_HANDLER", -10); // AddSlashHandler
  define("USER",               0); // used by user handlers before PageHandler is called
  define("PAGE_SYSTEM",       10); // used by system handlers before PageHandler is called
  define("PAGE_HANDLER",      20); // PageHandler
  define("ERROR",             30); // used by user handlers before ErrorHandler is called
  define("ERROR_HANDLER",     40); // ErrorHandler
