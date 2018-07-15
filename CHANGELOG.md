# Changelog

## 0.1a6 (15.07.2018)
### Bugfixes
* the system and user paths derived in constans.php now use the realpath instead of just prepending a folder separator
* _loadExtensions() now uses the basename of $file instead of just appending the filename
* Main::run now calls clearstatcache(true) to prevent PHP file caches from messing with file checks
* previously, when calling a Handler from within a Handler, a Plugin from within a Plugin or a Theme from within a Theme, the $active value would be set to NULL after the call, breaking the getActive() function; now the last active Handler/Plugin/Theme is stored and restored appropriately
* Handler/Plugin/Theme methods are now encapsulated in a try-finally block to prevent a single buggy Handler/Plugin/Theme from killing the whole CMS
* moved `callMethod()` and `checkMethod()` from `user.php` to `system.php` as they shouldn't be needed for user code
* renamed recommended fields so that all of them default to false (e.g. `nomarkdown` instead of `markdown` or `hiddenfromhome` instead of `home`) - previously the assumed default has been random
### Features
* when a Plugin returns an array this is now merged with the result array of Plugins::run() instead of adding it as a single element of the array (so-called array-flattening), this allows Plugins to return more than one result without the caller having to flatten the array itself
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
* the page handler now supports the `contenttype` field to set the "Content-Type" header when `notheme` is encountere
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

