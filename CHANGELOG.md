# v1.3.2
## 09/07/2015

2. [](#improved)
  * Added blueprints for Grav Admin plugin
  * Corrected some variable names in languages.yaml [#5](https://github.com/Sommerregen/grav-plugin-archive-plus/pull/5)
3. [](#bugfix)
  * Fixed not working with Grav's Admin Panel [#4](https://github.com/Sommerregen/grav-plugin-archive-plus/issues/4)

# v1.3.1
## 08/17/2015

3. [](#bugfix)
  * Fixed broken path translation

# v1.3.0
## 08/08/2015

1. [](#new)
  * Added admin configurations **(requires Grav 0.9.34+)**
2. [](#improved)
  * Updated `README.md`
3. [](#bugfix)
  * Fixed Czech translation

# v1.2.0
## 07/25/2015

1. [](#new)
  * Added multi-language support **(requires Grav 0.9.33+)**
  * Added `Show more` button below blogger-like hierarchical menu, when list of items is truncated
2. [](#improved)
  * Refactored code
  * Updated `README.md`
  * Improved speed (plugin now caches the results)
  * Slight style changes in the blogger-like hierarchical menu
3. [](#bugfix)
  * Fixed improper path to item if nested [#2](https://github.com/Sommerregen/grav-plugin-archive-plus/issues/2)

# v1.1.1
## 05/10/2015

2. [](#improved)
  * Better expandable blogger-like hierarchical menu
  * PSR fixes

# v1.1.0
## 02/21/2015

1. [](#new)
  * Implemented new `param_sep` variable **(requires Grav 0.9.18+)**
2. [](#improved)
  * Refactored code, fixed INSTALL.md

# v1.0.2
## 02/05/2015

1. [](#new)
  * Added new option `limit.items` to hide items [#1](https://github.com/Sommerregen/grav-plugin-archive-plus/pull/1)
2. [](#improved)
  * Updated documentation

# v1.0.1
## 01/22/2015

1. [](#bugfix)
  * Fixed issue with ignored `enabled: false` option

# v1.0.0
## 01/19/2015

1. [](#new)
  * Added a blogger like hierarchical (year/month/post) archive menu
  * Fork of *Archives* plugin v1.2.0 by _Grav Team_ (see https://github.com/getgrav/grav-plugin-archives)
  * ChangeLog started...
2. [](#improved)
  * Improved readability of code
  * Added more configuration options to Admin Panel
  * Automatically add taxonomy type (`archive`) for months and years
