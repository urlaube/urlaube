# Changelog

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

