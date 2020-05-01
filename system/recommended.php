<?php

  /**
    These are the recommended constants of the urlau.be CMS core.

    These are provided to handler, plugin and theme developers so that they can decide on often-used placeholders.

    @package urlaube/urlaube
    @version 0.2a0
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
  try_define("FIXURL",         -600); // used by user addons before FixUrlAddon is called
  try_define("FIXURL_ADDON",   -500); // FixUrlAddon
  try_define("ADDSLASH",       -400); // used by user addons before AddSlashAddon is called
  try_define("ADDSLASH_CACHE", -300); // used by system cache addon before AddSlashAddon is called
  try_define("ADDSLASH_SYSTEM",-200); // used by system addons before AddSlashAddon is called
  try_define("ADDSLASH_ADDON", -100); // AddSlashAddon
  try_define("USER",              0); // used by user addons before PageAddon is called
  try_define("PAGE_CACHE",      100); // used by system cache addon before PageAddon is called
  try_define("PAGE_SYSTEM",     200); // used by system addons before PageAddon is called
  try_define("PAGE_ADDON",      300); // PageAddon
  try_define("ERROR",           400); // used by user addons before ErrorAddon is called
  try_define("ERROR_SYSTEM",    500); // used by system addons before ErrorAddon is called
  try_define("ERROR_ADDON",     600); // ErrorAddon
