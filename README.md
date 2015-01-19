# [Grav Archive Plus Plugin][project]

> `Archive Plus` is an enhanced version of the [Grav Archives plugin](https://github.com/getgrav/grav-plugin-archives) with more configuration options and the ability to show a blogger like hierarchical archive menu for links grouped by month and/or year.

## About

`Archive Plus` shares many features with the [`Grav Archives plugin`](https://github.com/getgrav/grav-plugin-archives) e.g. it automatically appends a `year` and `month_year` taxonomy to all pages and provides a `partials\archive_plus.html.twig` template which you can include in a blog sidebar to render links into a year/month/post format useful for blogs, but enhances them in an intuitive way.

Currently it

 - adds a blogger like hierarchical (year/month/post) archive menu
 - and a per site configuration

See how the `Archive Plus` will look like on your site:

![Screenshot Archive Plus](assets/screenshot_1.png "Screenshot")

Some more screenshots of the functionality can be found in the [assets folder](assets/).

## Installation

Installing the `Archive Plus` plugin can be done in one of two ways. The GPM (Grav Package Manager) installation method enables you to quickly and easily install the plugin with a simple terminal command, while the manual method enables you to do so via a zip file.

### GPM Installation (Preferred)

The simplest way to install this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm) through your system's Terminal (also called the command line). From the root of your Grav install type:

    bin/gpm install archive_plus

This will install the `Archive Plus` plugin into your `/user/plugins` directory within Grav. Its files can be found under `/your/site/grav/user/plugins/archive_plus`.

### Manual Installation

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `archive_plus`. You can find these files either on [GitHub](https://github.com/sommerregen/grav-plugin-archive-plus) or via [GetGrav.org](http://getgrav.org/downloads/plugins#archive_plus).

You should now have all the plugin files under

    /your/site/grav/user/plugins/archive_plus

>> NOTE: This plugin is a modular component for Grav which requires [Grav](http://github.com/getgrav/grav), the [Error](https://github.com/getgrav/grav-plugin-error) and [Problems](https://github.com/getgrav/grav-plugin-problems) plugins, and a theme to be installed in order to operate.

## Usage

The `Archive Plus` plugin comes with some sensible default configuration, that are pretty self explanatory:

### Config Defaults

```
enabled: true                 # Set to false to disable this plugin completely
built_in_css: true            # Use built-in CSS of the plugin
date_display_format: 'F Y'    # Date format to display e.g. January 2014
show:
    counter: true             # Toggle to show number of items before the link
    year: true                # Toggle to show year
    month: true               # Toggle to show month
limit:
    year: 2                   # Limit to show only the last n years
    month: 12                 # Limit to show only the last n months
    items: 40                 # Limit to show only the last n items
order:
    by: date                  # Ordering of items
    dir: desc                 # Ordering of items (asc or desc)
filter_combinator: and        # The filter combinator to use to combine several filters
filters:                      # Filter to select which items should be shown
    category: blog
```

If you need to change any value, then the best process is to copy the [archive_plus.yaml](archive_plus.yaml) file into your `users/config/plugins/` folder (create it if it doesn't exist), and then modify there. This will override the default settings.

### Template Override

Something you might want to do is to override the look and feel of the archives, and with Grav it is super easy.

Copy the template file [templates/partials/archive_plus.html.twig](templates/partials/archive_plus.html.twig) into the `templates/partials` folder of your custom theme, and that is it.

```
/your/site/grav/user/themes/custom-theme/templates/partials/archive_plus.html.twig
```

You can now edit the override and tweak it however you prefer.

>> Note: Don't touch or edit the template file [templates/partials/archives.html.twig](templates/partials/archives.html.twig) in the `templates/partials` folder! This file serves as a compatibility fallback for those themes which expect the Archives plugin to be installed.

## Updating

As development for `Archive Plus` continues, new versions may become available that add additional features and functionality, improve compatibility with newer Grav releases, and generally provide a better user experience. Updating `Archive Plus` is easy, and can be done through Grav's GPM system, as well as manually.

### GPM Update (Preferred)

The simplest way to update this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm). You can do this with this by navigating to the root directory of your Grav install using your system's Terminal (also called command line) and typing the following:

    bin/gpm update archive_plus

This command will check your Grav install to see if your `Archive Plus` plugin is due for an update. If a newer release is found, you will be asked whether or not you wish to update. To continue, type `y` and hit enter. The plugin will automatically update and clear Grav's cache.

#### Manual Update

Manually updating `Archive Plus` is pretty simple. Here is what you will need to do to get this done:

* Delete the `your/site/user/plugins/archive_plus` directory.
* Downalod the new version of the Archive Plus plugin from either [GitHub](https://github.com/sommerregen/grav-plugin-archive-plus) or [GetGrav.org](http://getgrav.org/downloads/plugins#archive_plus).
* Unzip the zip file in `your/site/user/plugins` and rename the resulting folder to `archive_plus`.
* Clear the Grav cache. The simplest way to do this is by going to the root Grav directory in terminal and typing `bin/grav clear-cache`.

>> Note: Any changes you have made to any of the files listed under this directory will also be removed and replaced by the new set. Any files located elsewhere (for example a YAML settings file placed in `user/config/plugins`) will remain intact.

## Contributing

You can contribute at any time! Before opening any issue, please search for existing issues and review the [guidelines for contributing](CONTRIBUTING.md).

After that please note:

* If you find a bug or would like to make a feature request or suggest an improvement, [please open a new issue][issues]. If you have any interesting ideas for additions to the syntax please do suggest them as well!
* Feature requests are more likely to get attention if you include a clearly described use case.
* If you wish to submit a pull request, please make again sure that your request match the [guidelines for contributing](CONTRIBUTING.md) and that you keep track of adding unit tests for any new or changed functionality.

### Support and donations

If you like my project, feel free to support me, since donations will keep this project alive. You can [![Flattr](https://api.flattr.com/button/flattr-badge-large.png)][flattr] me or send me some bitcoins to **1HQdy5aBzNKNvqspiLvcmzigCq7doGfLM4** whenever you want. I herewith say 'thank you' for all your support you can give me :-)

## License

Copyright (c) 2015 [Benjamin Regler][github]. See also the list of [contributors] who participated in this project. A lot of credits also go to [Andy Miller](https://github.com/getgrav/) who wrote the Archives plugin, which this project is based on.

Licensed for use under the terms of the [MIT license][mit-license] (see [LICENSE](LICENSE)).


[github]: https://github.com/sommerregen/ "GitHub account from Benjamin Regler"
[mit-license]: http://www.opensource.org/licenses/mit-license.php "MIT license"

[flattr]: https://flattr.com/submit/auto?user_id=Sommerregen&url=https://github.com/sommerregen/grav-plugin-archive-plus "Flatter my GitHub project"

[project]: https://github.com/sommerregen/grav-plugin-archive-plus
[issues]: https://github.com/sommerregen/grav-plugin-archive-plus/issues "GitHub Issues for Grav Archive Plus"
[contributors]: https://github.com/sommerregen/grav-plugin-archive-plus/blob/master/contributors "List of contributors to the project"
