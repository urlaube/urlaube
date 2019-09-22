<?php

  /**
    These are the recommended constants of the urlau.be CMS core.

    This file contains the recommended constants of the urlau.be CMS core. These
    are provided to handler, plugin and theme developers so that they can decide
    on often-used placeholders.

    @package urlaube\urlaube
    @version 0.1a12
    @author  Yahe <hello@yahe.sh>
    @since   0.1a7
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  // STATIC CONSTANTS

  // recommended content fields
  try_define("AUTHOR",      "author");      // author of an entry
  try_define("CATEGORY",    "category");    // list of categories of an entry
  // CONTENT                                // text content of an entry
  try_define("DATE",        "date");        // publication date of an entry
  try_define("DESCRIPTION", "description"); // description of an entry
  try_define("PREVIEW",     "preview");     // preview image of an entry
  try_define("TITLE",       "title");       // title of an entry
  try_define("UPDATE",      "update");      // date of the last update of an entry
  // URI                                    // uri of an entry

  // recommended theme configuration
  // AUTHOR                               // author of the website
  try_define("CANONICAL",  "canonical");  // canonical URL of the website
  // CHARSET                              // charset of the website
  try_define("COPYRIGHT",  "copyright");  // copyright disclaimer of the website
  // DESCRIPTION                          // description of the website
  try_define("FAVICON",    "favicon");    // favicon.ico URL of the website
  try_define("KEYWORDS",   "keywords");   // keywords of the website
  // LANGUAGE                             // language of the website
  try_define("LOGO",       "logo");       // logo URL of the website
  try_define("MENU",       "menu");       // menu entries of the website
  try_define("PAGENAME",   "pagename");   // name of the current page
  try_define("SITENAME",   "sitename");   // name of the website
  try_define("SITESLOGAN", "siteslogan"); // slogan of the website
  // TIMEFORMAT                           // formatting of date strings
  // TITLE                                // head title of the website

  // recommended widget fields
  // CONTENT // content of the widget
  // TITLE   // title of the widget

  // define theme trigger events
  // AFTER_* and BEFORE_* plugins just get triggered
  try_define("AFTER_BODY",     "after_body");     // should be called by a theme after the body is generated
  try_define("AFTER_FOOTER",   "after_footer");   // should be called by a theme after the footer is generated
  try_define("AFTER_HEAD",     "after_head");     // should be called by a theme after the head is generated
  try_define("AFTER_SIDEBAR",  "after_sidebar");  // should be called by a theme after the sidebar is generated
  try_define("BEFORE_BODY",    "before_body");    // should be called by a theme before the body is generated
  try_define("BEFORE_FOOTER",  "before_footer");  // should be called by a theme before the footer is generated
  try_define("BEFORE_HEAD",    "before_head");    // should be called by a theme before the head is generated
  try_define("BEFORE_SIDEBAR", "before_sidebar"); // should be called by a theme before the sidebar is generated

  // define handler priority values
  try_define("FIXURL",           -50); // used by user handlers before FixUrlHandler is called
  try_define("FIXURL_HANDLER",   -40); // FixUrlHandler
  try_define("ADDSLASH",         -30); // used by user handlers before AddSlashHandler is called
  try_define("ADDSLASH_SYSTEM",  -20); // used by system handlers before AddSlashHandler is called
  try_define("ADDSLASH_HANDLER", -10); // AddSlashHandler
  try_define("USER",               0); // used by user handlers before PageHandler is called
  try_define("PAGE_SYSTEM",       10); // used by system handlers before PageHandler is called
  try_define("PAGE_HANDLER",      20); // PageHandler
  try_define("ERROR",             30); // used by user handlers before ErrorHandler is called
  try_define("ERROR_SYSTEM",      40); // used by system handlers before ErrorHandler is called
  try_define("ERROR_HANDLER",     50); // ErrorHandler
