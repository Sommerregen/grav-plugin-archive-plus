# [![Grav Archive Plus Plugin](assets/logo.png)][project]

[![Release](https://img.shields.io/github/release/sommerregen/grav-plugin-archive-plus.svg)][project] [![Issues](https://img.shields.io/github/issues/sommerregen/grav-plugin-archive-plus.svg)][issues] [![Dual license](https://img.shields.io/badge/dual%20license-MIT%2FGPL-blue.svg)](LICENSE "License") <span style="float:right;">[![Flattr](https://api.flattr.com/button/flattr-badge-large.png)][flattr] [![PayPal](https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif)][paypal]</span>

> `Archive Plus` is an enhanced version of the [Grav Archives plugin](https://github.com/getgrav/grav-plugin-archives) with more configuration options and the ability to show a blogger like hierarchical archive menu for links grouped by month and/or year.

##### Table of Contents:

* [About](#about)
* [Installation and Updates](#installation-and-updates)
* [Usage](#usage)
* [Contributing](#contributing)
* [Licencse](#license)

## About

`Archive Plus` shares many features with the [`Grav Archives plugin`](https://github.com/getgrav/grav-plugin-archives), e.g. it automatically appends a "year/month" taxonomy to all pages and provides a `partials\archive_plus.html.twig` template, which you can include in a blog sidebar to render links into a year/month/post format useful for blogs, but enhances them in an intuitive way.

Currently it

 - [x] adds a blogger like hierarchical (year/month/post) archive menu
 - [x] has (full) multi-language support **(requires Grav 0.9.33+)**
 - [x] integrates in Admin panel
 - [ ] provides a custom archive page (under development)
 - [x] and a per site configuration

See how the `Archive Plus` will look like on your site:

![Screenshot Archive Plus](assets/screenshot.gif "Archive Plus Preview")

## Installation and Updates

Installing or updating the `Archive Plus` plugin can be done in one of two ways. Using the GPM (Grav Package Manager) installation update method (i.e. `bin/gpm install archive_plus`) or manual install by downloading [this plugin](https://github.com/sommerregen/grav-plugin-archive-plus) and extracting all plugin files to

    user/plugins/archive_plus

For more informations, please check the [Installation and update guide](docs/INSTALL.md).

## Usage

The `Archive Plus` plugin comes with some sensible default configuration, that are pretty self explanatory:

### Config Defaults

```yaml
# Global plugin configurations

enabled: true                 # Set to false to disable this plugin completely
built_in_css: true            # Use built-in CSS of the plugin

# Default values for Archive Plus configuration.

show:
  counter: true               # Toggle to show number of items before the link
  year: true                  # Toggle to show year
  month: true                 # Toggle to show month
  items: true                 # Toggle to show items

limit:
  year: 2                     # Limit to show only the last n years
  month: 12                   # Limit to show only the last n months
  items: 40                   # Limit to show only the last n items

order:
  by: date                    # Ordering of items
  dir: desc                   # Ordering of items (asc or desc)

filter_combinator: and        # The filter combinator to use to combine several filters
filters:                      # Filter to select which items should be shown
  category: blog
```

If you need to change any value, then the best process is to copy the [archive_plus.yaml](archive_plus.yaml) file into your `users/config/plugins/` folder (create it if it doesn't exist), and then modify there. This will override the default settings.

### Template Override

Something you might want to do is to override the look and feel of the archives, and with Grav it is super easy.

Copy the template file [templates/partials/archive_plus.html.twig](templates/partials/archive_plus.html.twig) into the `templates/partials` folder of your custom theme, and that is it.

    user/themes/custom-theme/templates/partials/archive_plus.html.twig

You can now edit the override and tweak it however you prefer.

> **Note:** Don't touch or edit the template file [templates/partials/archives.html.twig](templates/partials/archives.html.twig) in the `templates/partials` folder! This file serves as a compatibility fallback for those themes which expect the Archives plugin to be installed.

## Contributing

You can contribute at any time! Before opening any issue, please search for existing issues and review the [guidelines for contributing](docs/CONTRIBUTING.md).

After that please note:

* If you find a bug or would like to make a feature request or suggest an improvement, [please open a new issue][issues]. If you have any interesting ideas for additions to the syntax please do suggest them as well!
* Feature requests are more likely to get attention if you include a clearly described use case.
* If you wish to submit a pull request, please make again sure that your request match the [guidelines for contributing](docs/CONTRIBUTING.md) and that you keep track of adding unit tests for any new or changed functionality.

### Support and donations

If you like my project, feel free to support me via [![Flattr](https://api.flattr.com/button/flattr-badge-large.png)][flattr] or by sending me some bitcoins to [**1HQdy5aBzNKNvqspiLvcmzigCq7doGfLM4**][bitcoin].

Thanks!

## License

Copyright (c) 2015 [Benjamin Regler][github]. See also the list of [contributors] who participated in this project. A lot of credits also goes to [Andy Miller](https://github.com/getgrav/) who wrote the [Archives plugin](https://github.com/getgrav/grav-plugin-archives) this project is based on.

[Dual-licensed](LICENSE) for use under the terms of the [MIT][mit-license] or [GPLv3][gpl-license] licenses.

![GNU license - Some rights reserved][gnu]


[github]: https://github.com/sommerregen/ "GitHub account from Benjamin Regler"
[gpl-license]: http://opensource.org/licenses/GPL-3.0 "GPLv3 license"
[mit-license]: http://www.opensource.org/licenses/mit-license.php "MIT license"

[flattr]: https://flattr.com/submit/auto?user_id=Sommerregen&url=https://github.com/sommerregen/grav-plugin-archive-plus "Flatter my GitHub project"
[paypal]: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SYFNP82USG3RN "Donate for my GitHub project using PayPal"
[bitcoin]: bitcoin:1HQdy5aBzNKNvqspiLvcmzigCq7doGfLM4?label=GitHub%20project "Donate for my GitHub project using BitCoin"
[gnu]: https://upload.wikimedia.org/wikipedia/commons/thumb/3/33/License_icon-gpl-88x31.svg/88px-License_icon-gpl-88x31.svg.png "GNU license - Some rights reserved"

[project]: https://github.com/sommerregen/grav-plugin-archive-plus
[issues]: https://github.com/sommerregen/grav-plugin-archive-plus/issues "GitHub Issues for Grav Archive Plus plugin"
[contributors]: https://github.com/sommerregen/grav-plugin-archive-plus/graphs/contributors "List of contributors of the project"
