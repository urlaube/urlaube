# Urlaube CMS
The Urlaube CMS is a flat-file content management system. It has been developed with modularity in mind. The core consists of the files contained in `./system/` while all user-provided files are located in `./user/`.

## Config
Config SHALL be located in the `./user/config/config.php` file.

## Content
Content SHALL be located in the `./user/content/` directory and MAY be located in subdirectories. Each content file has to end with the extension `.md` otherwise it is not considered to be a content file. Content files consist of header lines in the form of `<Key>: <Value>` which are used to provide further information about the content to handlers, plugins and themes. The actual content of the file starts after a blank line and ends at the end of the file.

### Example
An example content file might look like this:

```
Category: myCategory examplePages
Date:     21.05.2018
Title:    Example Page

This is an example page.
```

## Core
The core are the minimal files that make up the CMS. The following files belong to the core:

* `./index.php` is the main entrypoint into the CMS, no other source file SHOULD be directly callable
* `./router.php` is the router file that can be used to test Urlaube locally by calling `php -S localhost:8080 ./router.php`
* `./system/derived.php` contains constants that are derived from other values
* `./system/init.php` is the place where all core files are included
* `./system/recommended.php` contains constants that SHOULD be used by handlers, plugins and themes for interoperability
* `./system/static.php` contains constants that are static
* `./system/system.php` constains core functions that SHOULD NOT be used by handlers, plugins and themes
* `./system/user.php` constains core functions that MAY be used by handlers, plugins and themes
* `./system/core/BaseConfig.class.php` contains a base class with getters and setters for dynamic values
* `./system/core/BaseHandler.class.php` contains a standard handler implementation
* `./system/core/BaseSingleton.class.php` contains a simple singleton base class
* `./system/core/Content.class.php` contains the content class that is used to represent content files
* `./system/core/Handler.interface.php` contains the handler interface that SHOULD be used by all handler developers
* `./system/core/Handlers.class.php` contains the handler management class
* `./system/core/Logging.class.php` contains a simple logging class
* `./system/core/Main.class.php` contains the main class that controls the workflow of the core
* `./system/core/Plugin.interface.php` contains the plugin interface that SHOULD be used by all plugin developers
* `./system/core/Plugins.class.php` contains the plugin management class
* `./system/core/Theme.interface.php` contains the theme interface that SHOULD be used by all theme developers
* `./system/core/Themes.class.php` contains the theme management class
* `./system/core/Translate.class.php` containts a simple translation engine class

## Handlers
Handlers extend the core and react on certain URLs by matching a handler's regular expression against the relative URI that has been called by the user.

### Registration
Handlers can be registered in the core by calling the following method:

```
Handlers::register($entity, $function, $regex, $methods = [GET], $priority = 0);
```

The register method has the following parameters:

* `$entity` is either `null`, a class name or an object
* `$function` is either the name of a function (when `$entity` is `null`) or the name of a method of `$entity`
* `$regex` is the regular expression that is matched against the URI relative to the configured root URI
* `$methods` is either a string or an array of strings denoting the HTTP methods the handler shall react on
* `$priority` is the priority value of the handler to allow it to be called earlier

The following priority values SHOULD be used for now:

* `FIXURL` is the earliest stage when the URI has not even been normalized
* `ADDSLASH` is the stage when the URI has been normalized but the trailing slash has not been added (useful for handling URIs denoting file names)
* `USER` is the stage when most handlers run after the URI has been fully normalized
* `ERROR` is the stage before the system's error handler collects all unhandled URIs

### Behaviour
When a handler has successfully handled a URI, it MUST return `true` to abort the further handling of the URI by other handlers.

### System Handlers
At the moment the Urlaube CMS consists of the following system handlers that are located in `./system/handlers/`:

* `fixurl` normalizes the URI to discard garbage
* `faviconico` handles accesses to the `favicon.ico` file by returning a `204 No Content`
* `indexphp` handles accesses to the `index.php` file by redirecting to the configured root URI
* `robotstxt` handles accesses to the `robots.txt` file by generating a minimal file
* `sitemapxml` handles access to the `sitemap.xml` file by generating a minimal file
* `addslash` adds a trailing slash to the URI
* `archive` provides a paginated archive feature based on the content's `Date` field
* `author` provides a paginated author feature based on the content's `Author` field
* `category` provides a paginated category feature based on the content's `Category` field
* `feed` provides an RSS feed for the archive, author, category and search feature
* `search` provides a paginated search feature
* `page` handles access to single pages by converting the URI to the corresponding content file path
* `error` handles all URIs that have not been handled by another handler

### User Handlers
Users MAY add their own handlers by putting them in the `./user/handlers/` directory.

### Configuration
User handlers MAY need additional configuration. To do this the handler management class provides the following method:

```
Handlers::set($name, $value);
```

The method has the following parameters:

* `$name` is the name of the configuration value
* `$value` is the contents of the configuration value

## Plugins
Plugins extend the core and react on certain events.

### Registration
Plugins MAY be registered in the core by calling the following method:

```
Plugins::register($entity, $function, $event);
```

The register method has the following parameters:

* `$entity` is either `null`, a class name or an object
* `$function` is either the name of a function (when `$entity` is `null`) or the name of a method of `$entity`
* `$event` is the event name the plugin shall react on

The following core trigger events are available for now:

* `BEFORE_MAIN` is called before the core is run
* `BEFORE_HANDLER` is called before the handlers are run
* `BEFORE_THEME` is called before the theme is run
* `AFTER_THEME` is called after the theme has finished running
* `AFTER_HANDLER` is called after the handlers have finished running
* `AFTER_MAIN` is called after the core has finished running

The following theme trigger events SHOULD be used for now:

* `BEFORE_HEAD` should be called by a theme before the head is generated
* `AFTER_HEAD` should be called by a theme after the head is generated
* `BEFORE_BODY` should be called by a theme before the body is generated
* `BEFORE_SIDEBAR` should be called by a theme before the sidebar is generated
* `AFTER_SIDEBAR` should be called by a theme after the sidebar is generated
* `AFTER_BODY` should be called by a theme after the body is generated
* `BEFORE_FOOTER` should be called by a theme before the footer is generated
* `AFTER_FOOTER` should be called by a theme after the footer is generated

The following core filter events are available for now:

* `FILTER_CONTENT` is called after the `ON_CONTENT` plugins have been called in `callcontent()`
* `FILTER_HANDLERS` is called after the handlers have been registered
* `FILTER_OUTPUT` is called before `Main::run()` exits
* `FILTER_PAGINATE` is called before pagination is applied in `paginate()`
* `FILTER_PLUGINS` is called after the plugins have been registered
* `FILTER_THEMES` is called after the themes have been registered
* `FILTER_WIDGETS` is called after the `ON_WIDGETS` plugins in `callwidgets()`

The following core content events are available for now:

* `ON_CONTENT` is called in `callcontent()`
* `ON_WIDGETS` is called in `callwidgets()`

The following cache events are available for now:

* `GET_CACHE` is called in `getcache()`
* `SET_CACHE` is called in `setcache()`

### Behaviour

* **Trigger plugins** just get executed and don't have to provide a certain behaviour.
* **Filter plugins** are provided with a content argument and SHOULD return content.
* **Content plugins** SHOULD return content.

### System Plugins
At the moment the Urlaube CMS consists of the following system plugins that are located in `./system/plugins/`:

* `cache` is used to provide a file-based caching feature that uses serialize/unserialize
* `file` is used by system handlers to load content files
* `hide` is used to provide a feature to hide content from certain pages
* `markdown` is used to provide markdown support which can be disables through the `nomarkdown` field
* `relocate` is used to provide a relocation feature through the `Relocate` and `RelocateType` fields
* `sticky` is used to provide a stickiness feature through the `Sticky` field

### User Plugins
Users MAY add their own plugins by putting them in the `./user/plugins/` directory.

### Configuration
User plugins MAY need additional configuration. To do this the plugin management class provides the following method:

```
Plugins::set($name, $value);
```

The method has the following parameters:

* `$name` is the name of the configuration value
* `$value` is the contents of the configuration value

## Themes
Themes extend the core and are called by a handler.

### Registration
Themes can be registered in the core by calling the following method:

```
Themes::register($entity, $function, $name);
```

The register method has the following parameters:

* `$entity` is either `null`, a class name or an object
* `$function` is either the name of a function (when `$entity` is `null`) or the name of a method of `$entity`
* `$name` is the name of the theme to be used in the config file

### Activation
To use a theme its name SHOULD be configured in the config file located at `./user/config/config.php`:

```
Main::set(THEMENAME, "<NAME OF THE THEME>");
```

### User Themes
Users MUST add their own theme by putting it in the `./user/themes/` directory.

### Configuration
User themes MAY need additional configuration. To do this the theme management class provides the following method:

```
Themes::set($name, $value);
```

The method has the following parameters:

* `$name` is the name of the configuration value
* `$value` is the contents of the configuration value

## Translation
The `Translate` class provides a translation feature based on simple JSON files.

### Registration
New translations can be registered by calling the following method:

```
Translate::register($folder, $name = null);
```

The register method has the following parameters:

* `$folder` is the path to a folder that contains files named after potential LANGUAGE values (e.g. `de_de`)
* `$name` is an optional namespace for the registered translation, if no name is given, the translations are loaded into the global space

### Example
An example translation file might look like this:

```
{
  "This"           : "Das",
  "is"             : "ist",
  "an"             : "eine",
  "example file"   : "Testdatei",
  "My name is %s." : "Mein Name ist %s."
}
```

### Usage
To translate a basic string, the following method can be used:

```
Translate::get($string, $name = null)
```

The following parameters are allowed:

* `$string` is the string that shall be translated, this string is searched in the JSON files within the registered folders
* `$name` is the namespace in which the translation shall be searched for, this is helpful to separate translations of different extensions

To translate a string containing placeholders as supported by PHP's `sprintf()` function the following method can be used:

```
Translate::format($string, $name = null, ...$values)
```

The following parameters are allowed:

* `$string` is the string that shall be translated, this string is searched in the JSON files within the registered folders
* `$name` is the namespace in which the translation shall be searched for, this is helpful to separate translations of different extensions
* `$values` are **one** or more additional values that shall be used to replace the placeholders in the translated string

There is a shortcode function in `./system/user.php` to shorten the code necessary to get a translation:

```
t($string, $name = null, ...$values)
```

The following parameters are allowed:

* `$string` is the string that shall be translated, this string is searched in the JSON files within the registered folders
* `$name` is the namespace in which the translation shall be searched for, this is helpful to separate translations of different extensions
* `$values` if left out then `Translate::get()` is called, if at least one addition $values parameter is set, then `Translate::format()` is called

## Installation
To install the Urlaube CMS you can clone the corresponding git repository:

```
git clone https://github.com/urlaube/urlaube
```

The Urlaube CMS uses a single entrypoint scheme. A `.htaccess` is already provided for your convenience. To use the Urlaube CMS in combination with NGINX you have to implement the single entrypoint yourself:

```
if (!-f $request_filename) {
  rewrite ^.*$ /index.php last;
}
```

## Configuration
The configuration takes place in the configuration file located at `./user/config/config.php`.

The following configuration values are currently supported:

* `Main::set(CACHE, false);` is the activation or deactivation of the cache
* `Main::set(CACHEAGE, 60*60);` is the number of seconds a cached value is considered to be fresh by default
* `Main::set(CHARSET, "UTF-8");` is the charset used by the system
* `Main::set(CONTENTTYPE, "text/html");` is the default content type set by the system
* `Main::set(DEBUGMODE, false);` actives printing of warning and error messages  
(**Important:** You SHOULD set this to `false` in production.)
* `Main::set(HOSTNAME, _getDefaultHostname());` is the hostname as taken from the URL  
(**Important:** You SHOULD configure this value as the default is considered to be insure. The default is taken from `$_SERVER["SERVER_NAME"]`, `$_SERVER["HTTP_HOST"]` or `$_SERVER["SERVER_ADDR"]` and is `localhost` as a fallback.)
* `Main::set(LANGUAGE, "de_DE");` is the language used by the system
* `Main::set(LOGLEVEL, Logging::NONE);` is the minimum level of log entries that get printed  
(**Important:** You SHOULD set this to `Logging::NONE` in production or set a filename for `LOGTARGET`.)
* `Main::set(LOGTARGET, Logging::OUTPUT);` is the target of log entries (either `Logging::OUTPUT` for direct output or a filename)
* `Main::set(METHOD, _getDefaultMethod());` is the HTTP method used to call the system  
(The default is derived from `$_SERVER["REQUEST_METHOD"]`.)
* `Main::set(PAGESIZE, 5);` is the number of entries per page displayed during pagination
* `Main::set(PORT, _getDefaultPort());` is the port number as taken from the URL  
(**Important:** You SHOULD configure this value as the default is considered to be insecure. The default is taken from `$_SERVER["SERVER_PORT"]`.)
* `Main::set(PROTOCOL, _getDefaultProtocol());` is the protocol as taken from the URL  
(**Important:** You SHOULD configure this value as the default is considered to be unreliable. The default is derived from `$_SERVER["HTTPS"]`.)
* `Main::set(RESPONSECODE, "200");` is the default HTTP response code set by the system
* `Main::set(ROOTURI, _getDefaultRootUri());` is the root URI the system is reachable at  
(**Important:** You SHOULD configure this value as the default is considered to be unreliable. The default is derived from `$_SERVER["SCRIPT_NAME"]`.)
* `Main::set(THEMENAME, null);` is the name of the active theme
* `Main::set(TIMEFORMAT, "c");` is the time format used for log entries
* `Main::set(TIMEZONE, "Europe/Berlin");` is the time zones used by the system
* `Main::set(URI, _getDefaultUri());` is the URI as taken from the URL  
(The default is derived from `$_SERVER["REQUEST_URI"]`.)

### Log Levels
The following log levels SHALL be used:

* `Logging::NONE` - do not log
* `Logging::DEBUG` - something might help when debugging
* `Logging::INFO` - something might be interesting
* `Logging::WARN` - something shouldn't be done
* `Logging::ERROR` - something went wrong

## Runtime Status
The following values are set during runtime to provide status information to plugins and themes:

* `Main::set(CONTENT, null);` is the content provided to plugins and themes
* `Main::set(METADATA, null);` are the metadata provided to plugins and themes
* `Main::set(PAGE, 1);` is the page number that is displayed during pagination
* `Main::set(PAGECOUNT, 1);` is the maximum number of pages available during pagination
