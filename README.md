# Urlaube CMS
The Urlaube CMS is a flat-file content management system. It has been developed with modularity in mind. The core consists of the files contained in `./system/` while all user-provided files are located `./user/`.

## Config
Config SHALL be located in the `./user/config/config.php` file.

## Content
Content SHALL be located in the `./user/content/` directory and MAY be located in subdirectories. Each content file has to end with the extension `.md` otherwise it is not considered to be a content file. Content files consist of header lines in the form of `<Key>: <Value>` which are used to provide further information about the content to handlers, plugins and themes. The actual content of the file starts after a blank line and ends at the end of the file.

An example content file might look like this:
```
Title: Example Page
Category: myCategory examplePages
Date: 21.05.2018

This is an example page.
```

## Core
The core are the minimal files that make up the CMS. The following files belong to the core:
* `./index.php` is the main entrypoint into the CMS, no other source file SHOULD be directly callable
* `./router.php` is the router file that can be used to test Urlaube locally by calling `php -S localhost:8080 ./router.php`
* `./system/constants.php` contains constants that are used by the core, handlers, plugins and themes
* `./system/defaults.php` contains the definition of default values for the CMS
* `./system/init.php` is the place where all core files are included
* `./system/recommends.php` contains constants that SHOULD be used by handlers, plugins and themes for interoperability
* `./system/system.php` constains core functions that SHALL NOT be used by handlers, plugins and themes
* `./system/user.php` constains core functions that MAY be used by handlers, plugins and themes
* `./system/core/Base.class.php` contains the base class that provides a magic function feature
* `./system/core/Config.class.php` contains the config class that is used by the user to configure the CMS
* `./system/core/Content.class.php` contains the content class that is used to represent content files
* `./system/core/Debug.class.php` contains the debug class that is used to implement debugging
* `./system/core/Handler.interface.php` contains the handler interface that SHOULD be used by all handler developers
* `./system/core/Handlers.class.php` contains the handler management class
* `./system/core/Main.class.php` contains the main class that controls the workflow of the core
* `./system/core/Plugin.interface.php` contains the plugin interface that SHOULD be used by all plugin developers
* `./system/core/Plugins.class.php` contains the plugin management class
* `./system/core/Theme.interface.php` contains the theme interface that SHOULD be used by all handler developers
* `./system/core/Themes.class.php` contains the theme management class
* `./system/core/Translate.class.php` containts the translation engine class

## Handlers
Handlers extend the core and react on certain URLs by matching a handler's regular expression against the relative URI. Handlers can be registered in the core by calling the following method:
```
Handlers::register($entity, $function, $regex, $methods = array(GET), $priority = 0);
```

The register method has the following parameters:
* `$entity` is either `null`, a class name or an object
* `$function` is the either a name of a function (when `$entity` is `null`) or the name of a method of `$entity`
* `$regex` is a regular expression that is matched against the URI relative to the configured root URI
* `$methods` is either a string or an array of strings denoting HTTP methods the handler shall react on
* `$priority` is the priority value of the handler to allow it to be called earlier

The following priority values SHOULD be used for now:
* `BEFORE_FIXURL` is the earliest stage when the URI has not even been normalized
* `BEFORE_ADDSLASH` is the stage when the URI has been normalized but the trailing slash has not been added (useful for handling URIs denoting file names)
* `USER` is the stage when most handlers run after the URI has been fully normalized
* `BEFORE_ERROR` is the stage before the system's error handler collects all unhandled URIs

When a handler has successfully handled a URI, it MUST return `true` to abort the further handling of the URI by other others.

At the moment the Urlaube CMS consists of the following system handlers that are located in `./system/handlers/`:
* `fixurl` normalizes the URI to discard garbage
* `favicon_ico` handles accesses to the `favicon.ico` file by returning a `204 No Content`
* `index_php` handles accesses to the `index.php` file by redirecting to the configured root URI
* `robots_txt` handles accesses to the `robots.txt` file by generating a minimal file
* `sitemap_xml` handles access to the `sitemap.xml` file by generating a minimal file
* `addslash` adds a trailing slash to the URI
* `page` handles access to single pages by converting the URI to the corresponding content file path
* `archive` provides a paginated archive feature based on the content's `Date` field
* `feed_archive` provides an RSS feed for the archive feature
* `author` provides a paginated author feature based on the content's `Author` field
* `feed_author` provides an RSS feed for the author feature
* `category` provides a paginated category feature based on the content's `Category` field
* `feed_category` provides an RSS feed for the category feature
* `home` provides a paginated home feature like a blog roll
* `feed_home` provides an RSS feed for the home feature
* `search_get` provides a paginated search feature
* `search_post` provides the endpoint for search forms
* `feed_search` provides an RSS feed for the search feature
* `error` handles all URIs that have not returned content

Users MAY add their own handlers by putting them in the `./user/handlers/` directory.

## Plugins
Plugins extend the core and react on certain events. Plugins can be registered in the core by calling the following method:
```
Plugins::register($entity, $function, $event);
```

The register method has the following parameters:
* `$entity` is either `null`, a class name or an object
* `$function` is the either a name of a function (when `$entity` is `null`) or the name of a method of `$entity`
* `$event` is an event name the plugin shall react on

The following core event names SHALL be used for now:
* `BEFORE_MAIN` is called before the core is run
* `BEFORE_HANDLER` is called before the handlers are run
* `FILTER_CONTENT` is called when `Themes::run()` is executed
* `BEFORE_THEME` is called before the theme is run
* `AFTER_THEME` is called after the theme has finished running
* `AFTER_HANDLER` is called after the handlers have finished running
* `FILTER_OUTPUT` is called when the core has retrieved the output, the output is provided to the plugin through an argument
* `AFTER_MAIN` is called after the core has finished running

The following event names SHOULD be used by handlers, plugins or themes for now:
* `BEFORE_HEAD` should be called by a theme before the HTML head is generated
* `AFTER_HEAD` should be called by a theme after the HTML head is generated
* `BEFORE_BODY` should be called by a theme before the HTML body is generated
* `BEFORE_SIDEBAR` should be called by a theme before the sidebar in the HTML body is generated
* `ON_WIDGETS` should be called by a theme when the sidebar in the HTML body is generated
* `FILTER_WIDGETS` should be called by a theme when the output of a widget is returned, the output should be provided to the plugin through an argument
* `AFTER_SIDEBAR` should be called by a theme after the sidebar in the HTML body is generated
* `BEFORE_FOOTER` should be called by a theme before the footer in the HTML body is generated
* `AFTER_FOOTER` should be called by a theme after the footer in the HTML body is generated
* `AFTER_BODY` should be called by a theme after the HTML body is generated

At the moment the Urlaube CMS consists of the following system plugins that are located in `./system/plugins/`:
* `file` is used by system handlers to load content files
* `feed` is used by system feed handlers to generate RSS feeds from content objects
* `relocate` is used to provide the relocation feature through the `Relocate` and `RelocateType` fields
* `markdown` is used to provide the markdown down support which can be disables through the `nomarkdown` field
* `sticky` is used to prive the stickyness feature through the `Sticky` field

Users MAY add their own plugins by putting them in the `./user/plugins/` directory.

## Themes
Themes extend the core and are called by a handler. Themes can be registered in the core by calling the following method:
```
Themes::register($entity, $function, $name);
```

The register method has the following parameters:
* `$entity` is either `null`, a class name or an object
* `$function` is the either a name of a function (when `$entity` is `null`) or the name of a method of `$entity`
* `$name` is the name of the theme to be used in the config file

To use a theme its name has to be configured in the config file located at `./user/config/config.php`:
```
Config::THEMENAME("<NAME OF THE THEME>");
```

Users SHALL add their own themes by putting them in the `./user/themes/` directory.

## Translation
The `Translate` class provides a translation feature based on simple JSON files. New translation files can be registered by calling the following method:
```
Translate::register($folder, $name = null);
```

The register method has the following parameters:
* `$folder` is the path to a folder that contains files named after potential language values (e.g. `de_de`)
* `$name` is an optional namespace for the registered translation, if no name is given, the translations are loaded into the global space

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
* `$values` are **one** or more additional values that shall be used to replace the placeholders in the translated strings

There is a shortcode function in `./system/user.php` to shorten the code necessary to get a translation:
```
t($string, $name = null, ...$values)
```

The following parameters are allowed:
* `$string` is the string that shall be translated, this string is searched in the JSON files within the registered folders
* `$name` is the namespace in which the translation shall be searched for, this is helpful to separate translations of different extensions
* `$values` if left out then `Translate::get()` is called, if at least one addition $values parameter is set, the `Translate::format()` is called

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
The configuration takes place in the configuration file located at `./user/config/config.php`. The `Config` core class provides the configurable values that are described below

### `Config::CHARSET($value)`
This defines the character set used throughout the CMS. The default is `UFT-8`.

### `Config::DEBUGMODE($value)`
This defines if the debug mode shall be activated which enables additional debug output. The default is `false`.

### `Config::HOSTNAME($value)`
This defines the hostname that is assumed for the website. The default is taken from `$_SERVER["SERVER_NAME"]`, `$_SERVER["HTTP_HOST"]` or `$_SERVER["SERVER_ADDR"]` and is `localhost` as a fallback.

**Important:** You SHOULD configure this value as the default is considered to be insure.

### `Config::LANGUAGE($value)`
This defines the language of the website which influences the translation feature. The default is `de_DE`.

### `Config::LOGLEVEL($value)`
This defines the level of the debug messages which shall be printed. The default is `DEBUG_NONE`.

The following log levels SHALL be used:
* `DEBUG_NONE` - do not log
* `DEBUG_DEBUG` - something might help when debugging
* `DEBUG_INFO` - something might be interesting
* `DEBUG_WARN` - something shouldn't be done
* `DEBUG_ERROR` - something went wrong

### `Config::LOGTIMEFORMAT($value)`
This defines the time format used for debug messages. The default is `c`.

### `Config::LOGTARGET($value)`
This defines the target where debug messages shall be written to. To log into a file you SHOULD set a filename here. The default is `DEBUG_OUTPUT` which means to log into the standard output (the website).

### `Config::LOGTARGET($value)`
This defines the target where debug messages shall be written to. To log into a file you SHOULD set a filename here. The default is `DEBUG_OUTPUT` which means to log into the standard output (the website).

### `Config::METHOD($value)`
This defines the HTTP method that is assumed for the website. The default is derived from `$_SERVER["REQUEST_METHOD"]`.

### `Config::PAGESIZE($value)`
This defines the number of content entries per page. The default is `5`.

### `Config::PORT($value)`
This defines the port number that is assumed for the website. The default is taken from `$_SERVER["SERVER_PORT"]`.

### `Config::PROTOCOL($value)`
This defines the protocol that is assumed for the website. The default is derived from `$_SERVER["HTTPS"]`.

**Important:** You SHOULD configure this value as the default is considered to be unreliable.

### `Config::ROOTURI($value)`
This defines the root URI that is assumed for the website. The default is derived from `$_SERVER["SCRIPT_NAME"]`.

**Important:** You SHOULD configure this value as the default is considered to be unreliable.

### `Config::SITENAME($value)`
This defines the name of your website. This SHOULD be your textual logo. The default is `Your Website`.

**Important:** You SHOULD configure this value.

### `Config::SITESLOGAN($value)`
This defines the slogan of your website. The default is `Your Slogan`.

**Important:** You SHOULD configure this value.

### `Config::THEMENAME($value)`
This defines the name of the active theme. The default is the first theme in the `./user/themes/` directory.

**Important:** You SHOULD configure this value as the default is considered to be unreliable.

### `Config::TIMEZONE($value)`
This defines the timezone of your website. The default is `Europe/Berlin`.

**Important:** You SHOULD configure this value.

### `Config::URI($value)`
This defines the URI that is assumed for the website. The default is derived from `$_SERVER["REQUEST_URI"]`.

