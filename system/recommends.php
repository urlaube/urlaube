<?php

  /**
    These are the recommended constants of the urlau.be CMS core.

    This file contains the recommended constants of the urlau.be CMS core. These are provided to handler, plugin and
    theme developers so that they can decide on often-used placeholders.

    @package urlaube\urlaube
    @version 0.1a4
    @author  Yahe <hello@yahe.sh>
    @since   0.1a0
  */

  // ===== DO NOT EDIT HERE =====

  // prevent script from getting called directly
  if (!defined("URLAUBE")) { die(""); }

  // STATIC CONSTANTS

  // default content file extension
  define("CONTENT_FILE_EXT", ".md");

  // recommended content fields
  define("AUTHOR",      "author");      // author of an entry
  define("CATEGORY",    "category");    // list of categories of an entry
  define("CONTENT",     "content");     // actual content of an entry
  define("DATE",        "date");        // publication date of an entry
  define("DESCRIPTION", "description"); // description of an entry
  define("FILE",        "file");        // filename of an entry
  define("HIDE",        "hide");        // hidden status of an entry
  define("HOME",        "home");        // hidden from home status of an entry
  define("MARKDOWN",    "markdown");    // disable markdown
  define("PREVIEW",     "preview");     // preview image of an entry
  define("STICKY",      "sticky");      // sticky status of an entry
  define("TITLE",       "title");       // title of an entry
  define("URI",         "uri");         // uri of an entry
  define("WIDGETS",     "widgets");     // disable widgets

  // AFTER_* and BEFORE_* plugins just get called
  define("AFTER_BODY",     "after_body");
  define("AFTER_FOOTER",   "after_footer");
  define("AFTER_HEAD",     "after_head");
  define("AFTER_SIDEBAR",  "after_sidebar");
  define("BEFORE_BODY",    "before_body");
  define("BEFORE_FOOTER",  "before_footer");
  define("BEFORE_HEAD",    "before_head");
  define("BEFORE_SIDEBAR", "before_sidebar");

  // FILTER_* plugins get content and shall filter it
  define("FILTER_WIDGETS", "filter_widgets");

  // ON_* plugins shall return content
  define("ON_CACHE",   "on_cache");   // try to read content from a caching plugin
  define("ON_WIDGETS", "on_widgets"); // try to read content from widgets plugins

  // recommended theme configuration
  // AUTHOR
  define("CANONICAL",  "canonical");  // canonical URL of the website
  define("CHARSET",    "charset");    // charset of the website
  define("COPYRIGHT",  "copyright");  // copyright disclaimer of the website
  define("FAVICON",    "favicon");    // favicon.ico URL of the website
  define("KEYWORDS",   "keywords");   // keywords of the website
  define("LANGUAGE",   "language");   // language of the website
  define("LOGO",       "logo");       // logo URL of the website
  define("MENU",       "menu");       // menu entries of the website
  define("PAGENAME",   "pagename");   // name of the current page
  define("TIMEFORMAT", "timeformat"); // formatting of date strings
  // TITLE

  // recommended widget fields
  // CONTENT
  // TITLE

  // define system handler names
  define("ADDSLASH_HANDLER",      "AddSlashHandler");
  define("ARCHIVE_HANDLER",       "ArchiveHandler");
  define("AUTHOR_HANDLER",        "AuthorHandler");
  define("CATEGORY_HANDLER",      "CategoryHandler");
  define("ERROR_HANDLER",         "ErrorHandler");
  define("FAVICON_ICO_HANDLER",   "FaviconIcoHandler");
  define("FEED_ARCHIVE_HANDLER",  "FeedArchiveHandler");
  define("FEED_AUTHOR_HANDLER",   "FeedAuthorHandler");
  define("FEED_CATEGORY_HANDLER", "FeedCategoryHandler");
  define("FEED_HOME_HANDLER",     "FeedHomeHandler");
  define("FEED_SEARCH_HANDLER",   "FeedSearchHandler");
  define("FIXURL_HANDLER",        "FixUrlHandler");
  define("HOME_HANDLER",          "HomeHandler");
  define("INDEX_PHP_HANDLER",     "IndexPhpHandler");
  define("PAGE_HANDLER",          "PageHandler");
  define("ROBOTS_TXT_HANDLER",    "RobotsTxtHandler");
  define("SEARCH_GET_HANDLER",    "SearchGetHandler");
  define("SEARCH_POST_HANDLER",   "SearchPostHandler");
  define("SITEMAP_XML_HANDLER",   "SitemapXmlHandler");

  // define configuration to deactivate handlers
  define("DEACTIVATE_ADDSLASH",    "deactivate_addslash");
  define("DEACTIVATE_ARCHIVE",     "deactivate_archive");
  define("DEACTIVATE_AUTHOR",      "deactivate_author");
  define("DEACTIVATE_CATEGORY",    "deactivate_category");
  define("DEACTIVATE_ERROR",       "deactivate_error");
  define("DEACTIVATE_FAVICON_ICO", "deactivate_favicon_ico");
  define("DEACTIVATE_FEED",        "deactivate_feed");
  define("DEACTIVATE_FIXURL",      "deactivate_fixurl");
  define("DEACTIVATE_HOME",        "deactivate_home");
  define("DEACTIVATE_INDEX_PHP",   "deactivate_index_php");
  define("DEACTIVATE_PAGE",        "deactivate_page");
  define("DEACTIVATE_ROBOTS_TXT",  "deactivate_robots_txt");
  define("DEACTIVATE_SEARCH",      "deactivate_search");
  define("DEACTIVATE_SITEMAP_XML", "deactivate_sitemap_xml");

  // define handler's pageinfo fields
  // AUTHOR
  // CATEGORY
  define("DAY",    "day");
  define("MONTH",  "month");
  define("NAME",   "name");
  define("PAGE",   "page");
  define("SEARCH", "search");
  define("YEAR",   "year");

  // define handler priority values
  define("BEFORE_FIXURL",   -40);
  define("FIXURL",          -30);
  define("BEFORE_ADDSLASH", -20);
  define("ADDSLASH",        -10);
  define("USER",              0);
  define("SYSTEM",           10);
  define("BEFORE_ERROR",     20);
  define("ERROR",            30);

