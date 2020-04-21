# Changelog

## 0.1a?? (??.??.????)
### Features
* slighty improved `router.php` for local debugging
* introduced `NOMARKDOWNPARAGRAPHS` to prevent the markdown plugin from creating `<p>` elements around HTML output
* improved `hide` plugin so that is renumbers the content array after modifying it
* updated [Parsedown](https://github.com/erusev/parsedown/) to version 1.7.4
* updated [Parsedown-Extra](https://github.com/erusev/parsedown-extra/) to version 0.8.1

## 0.1a12 (22.09.2019)
### Bugfixes
* changed execution of system handlers so that the `PageHandler` is called earlier
* introduced new handler priority `ERROR_SYSTEM` to let handlers run after `PageHandler`
* `FilePlugin::run()` should not check for a single file if `$recursive` argument is set

### Security
* updated [Parsedown](https://github.com/erusev/parsedown/) to version 1.7.3

## 0.1a11 (05.12.2018)
### Features
* added `try_define()` to allow the overwriting of system-defined constants
* rewrote system files to use `try_define()` instead of `define()`
* added the possibility to create the file `user.php` that is executed before anything else

## 0.1a10 (03.11.2018)
### Features
* added support for system themes
* changed order of module loading so that user modules have a higher priority than system modules
* introduced `ON_CONTENT` core content event to make plugins independent from the content source
* introduced `callcontent()` function that calls and filters the content plugins
* handlers don't have to call `FILTER_CONTENT` plugins anymore when they use `callcontent()`
* enhanced `FilePlugin` to react on the `ON_CONTENT` core content event
* renamed `widgets()` function to `callwidgets()`
* enhanced `RelocatePlugin` so that handlers don't have to know about the `RELOCATE*` fields
* introduced `HidePlugin` so that handlers don't have to know about the `HIDDEN*` fields
* use `$_SERVER["REQUEST_TIME_FLOAT"]` as the start time in `_logResourceUsage()`
* removed `USER_CACHE_PATH` and `USER_CONTENT_PATH` as the core is storage-agnostic now

## 0.1a9 (27.10.2018)
### Features
* added `querystring()` function to get a URL-encoded query string

### Bugfixes
* handlers shall only redirect to the corrected URI when they actually return content to not disrupt later handlers
* the handled URI doesn't contain the query string anymore
* modified the handlers so that redirect which correct the URI still retain the query string
* modified the archive handler so that only content with a set DATE field is displayed

## 0.1a8 (24.10.2018)
### Features
* added `UPDATE` field that contains the date of the last update of a content entry
* `FilePlugin::loadContent()` now checks that the given file is located in the user content path
* `FilePlugin::loadContent()` now sets a content field even when the value is empty, this way it can be checked whether the field has been set to an empty value (e.g. through `Content->isset()`)
* `FilePluggin::loadContent()` now tries to preset the `UPDATE` field
* `SitemapXmlHandler` now tries to use the `UPDATE` field when it's available
* added the plugin triggers events `GET_CACHE` and `SET_CACHE` as key/value methods to interact with an optional caching plugin
* added `getcache()` and `setcache()` to have a simpler interface for caching
* non-filtered arguments for plugins are now passed as an array to `Plugins::run()` to support call by reference arguments
* `Plugins::run()` now returns NULL when no callable plugins have been found for non-filtered calls
* added `CachePlugin` as a file-based serialize/unserialize caching plugin
* `Content` now implements the `Serializable` interface to be compatible with serialization/unserialization
* added `BaseHandler::prepareMetadata()` to allow handlers to do proper sanitization and thus to reduce duplicate content

### Bugfixes
* changed visibility of the helper function `FilePlugin::fileToUri()`
* `Logging::log()` now calls `file_put_content()` with `LOCK_EX` to support parallel writes
* fixed the default value of `Plugins::run()` for non-filter calls
* `path2uri()` now calls `realpath()` before trying to convert the path
* changed conversion functions like `path2uri()`, `uri2path()` and `relativeuri()` so that they return `NULL` on error
* rewrote `relativeuri()` to use `parse_url()` instead of something that was hacked together
* removed the amount of type-checking as it prevents the usage of functions in certain cases
* fixed `FeedHandler` because it didn't generate a feed for the home page
* fixed `FeedHandler` because it didn't generate a feed for pages with a single entry
* fixed `SitemapXmlHandler` because it didn't generate a sitemap for site with a single entry

## 0.1a7 (17.10.2018)
### Features
* introduced the `absoluteurl()` function to create the absolute URL from a relative URI
* extended the `relativeuri()` function to be able to handle absolute URLs
* introduced the `findcontent()` function to search for `Content` object in array
* introduced the `parseuri()` function to parse URIs and return the named subpatterns
* introduced the `preparecontent()` function to have a central feature to cleanup content (arrays)
* replaced the `array()` call with the `[]` shortcode
* changed the `Content` class to use case-insensitive string indices
* introduced `Content::clone()` to clone a Content object
* introduced `Content::indices()` to get a list of all available entries
* introduced `Content::merge()` to merge two Content objects
* introduced the `BaseConfig` class to reduce duplicate code
* changed `Handlers`, `Plugins` and `Themes` to be based on the `BaseConfig` class
* changed `Handlers`, `Plugins` and `Themes` to store entities as `Content` object
* changed `Main` to be based on the `BaseConfig` class and to store all values in a `Content` object
* extended the `value()` function to work with `BaseConfig` subclasses
* introduced the `BaseHandler` class to reduce duplicate code
* changed many system handlers to be based on the `BaseHandler` class
* removed the `HomeHandler` class as it is now equal to the `ArchiveHandler`
* combined all `Feed*Handler` classes into a single one
* removed the `Feed` class as it is only used within one handler now
* renamed the `Base` class to `BaseSingleton`
* renamed the `Debug` class to `Logging`
* renamed the `File` class to `FilePlugin`
* renamed the `Markdown` class to `MarkdownPlugin`
* renamed the `Relocate` class to `RelocatePlugin`
* renamed the `SearchGetHandler` class to `SearchHandler`
* renamed the `Sticky` class to `StickyPlugin`
* renamed the `findkeywords()` function to `haskeywords()`
* changed the `haskeywords()` function to also work with a single keyword instead of an array
* merged the `SearchHandler` class and the `SearchPostHandler` class
* moved derived constants from `constants.php` to `derived.php`
* moved static constants from `constants.php` to `static.php`
* renamed the `recommends.php` file to `recommended.php`
* introduced `FILTER_PAGINATE` to filter content before pagination takes place
* changed `MarkdownPlugin` to not provide a separate `apply()` method
* rewrote handlers and plugins to avoid using string literals for classes
* rewrote handlers to call `FILTER_CONTENT` handlers
* rewrote handlers to use `parseuri()`
* rewrote regexes to use `"\~...\~"` instead of `"@...@"`
* added `hiddenfromarchive`, `hiddenfromauthor`, `hiddenfromcategory`, `hiddenfromsearch` and `hiddenfromsitemap`
* checked all handlers to make sure that the metadata are set before plugins are called
* checked all handlers to make sure that the content is set before the theme is called

### Bugfixes
* removed the `if (!class_exists("<CLASSNAME>")) {` checks as duplicate class names should fail hard
* removed the `Config` class as configurations should take place with the specific classes that are involved
* removed the `DEACTIVATE_*` constants as this is now achieved through `FILTER_HANDLERS` plugins
* removed the `*_HANDLER` constants as `<CLASSNAME>::class` does the same thing
* removed the `defaults.php` file as the defaults are now located in `Main::configure()`
* removed the magic function feature from the `BaseSingleton` class
* changed `StickyPlugin` to react on `FILTER_PAGINATE` so that stickiness works across multiple pages
* changed `StickyPlugin` to only react when the `ArchiveHandler` or `FeedHandler` for the archive is active
* changed many system handlers to that `getContent()` doesn't have side-effects on PAGEMAX, PAGEMIN and PAGENUMBER
* renamed runtime functions of plugins and handlers from `handle()` to `run()`
* the home screen doesn't show entries without a set DATE value anymore (aka. pages)
* make sure that the sorting of handlers is based on the integer value of the priority
* fixed line breaks and trailing whitspace

## 0.1a6 (15.07.2018)
### Bugfixes
* the system and user paths derived in constans.php now use the realpath instead of just prepending a folder separator
* `_loadExtensions()` now uses the basename of `$file` instead of just appending the filename
* `Main::run()` now calls `clearstatcache(true)` to prevent PHP file caches from messing with file checks
* previously, when calling a Handler from within a Handler, a Plugin from within a Plugin or a Theme from within a Theme, the `$active` value would be set to `NULL` after the call, breaking the `getActive()` function; now the last active Handler/Plugin/Theme is stored and restored appropriately
* Handler/Plugin/Theme methods are now encapsulated in a try-finally block to prevent a single buggy Handler/Plugin/Theme from killing the whole CMS
* moved `callMethod()` and `checkMethod()` from `user.php` to `system.php` as they shouldn't be needed for user code
* renamed recommended fields so that all of them default to false (e.g. `nomarkdown` instead of `markdown` or `hiddenfromhome` instead of `home`) - previously the assumed default has been random

### Features
* when a Plugin returns an array this is now merged with the result array of `Plugins::run()` instead of adding it as a single element of the array (so-called array-flattening), this allows Plugins to return more than one result without the caller having to flatten the array itself
* array-flattening has been removed from the `widgets()` function
* the `PageHandler` is now executed earlier so that it can overrule the output of other handlers
* a static frontpage is now supported by creating the file `user/content/.md`
* renamed `redirect()` to `relocate()` and added the possibility to do redirects (307, 308) instead of just moves (301, 302)
* renamed the redirect plugin to relocate plugin
* renamed `Redirect` and `RedirectType` to `Relocate` and `RelocateType`
* removed handler- and plugin-specific functions from `user.php`
* most handlers now react on GET and POST so that fewer errors occur
* the FixUrl handler now handles "." and ".." in URLs which makes allowing dots in URLs more secure
* the page handler now supports dots in URLs
* the page handler now supports the `noslash` field that tells the handler that the page shall be handled before the AddSlash handler runs
* the page handler now supports the `notheme` field that tells the handler to print the content directly without calling the theme
* the page handler now supports the `contenttype` field to set the "Content-Type" header when `notheme` is encountered
* system handlers and plugins aren't numbered anymore because their execution order is now provided through their priority
* introduced a sticky plugin that reacts on the `sticky` field and resorts content so that sticky content comes first

## 0.1a5 (27.06.2018)
### Features
* added `widgets()` function that simplifies calling the widget plugins
* introduced redirect plugin that handles `Redirect` and `RedirectType` fields
* modified all relevent handlers to filter content elements that are redirects

## 0.1a4 (02.06.2018)
### Features
* rewrote the translation system which now is completely encapsulated in `Translate`
* added `fhtml()` that calls `html()` on all values and the formats the given string using `sprintf`
* renamed `gl()` to `t()` which now also supports formatting based on `sprintf()`
* added `tfhtml()` that translates all values and then calls `fhtml()`
* rewrote `999_error` to use the new translation system
* all system handlers and plugins now to extend the Base class to prevent their instantiation
* `020_markdown` now uses [Parsedown-Extra](https://github.com/erusev/parsedown-extra) in addition to [Parsedown](https://github.com/erusev/parsedown)
* rewrote `Themes::register()` so that its structure resembles `Handlers::register()` and `Plugins::register()`

## 0.1a3 (27.05.2018)
### Features
* added definitions for system handler names
* introduced `feeduri()` as shortcut to get the correct feed URI

## 0.1a2 (26.05.2018)
### Bugfixes
* renumbered system handlers
* the `ArchiveHandler` now requires at least the year to be set in the URI
* the `FixUrlHandler` now properly handles urlencoded special characters

### Features
* added `AuthorHandler` and `FeedAuthorHandler`
* renamed `SearchHandler` to `SearchGetHandler`
* added `SearchPostHandler` that properly redirect to the correct search URI

## 0.1a1 (23.05.2018)
### Bugfixes
* `010_feed` plugin doesn't call `Markdown::apply()` directly anymore to decouple it from `020_markdown`
* `020_markdown` plugin now checks for each array entry if it is an instance of `Content`

### Feature
* `Plugins::run()` now supports multiple arguments
* `Plugins::run()` now properly supports filter plugins
* added `FILTER_CONTENT` plugin event
* updated `020_markdown` plugin to use the new `FILTER_CONTENT` event
* `3*0_feed_*` handlers now call `FILTER_CONTENT` plugins as they don't use Themes::run() which does this automatically

## 0.1a0 (21.05.2018)
### Features
* initial version
