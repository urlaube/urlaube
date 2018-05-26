# Changelog

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

