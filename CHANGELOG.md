Revision history for Extension:SearchBoxes
==========================================

All notable changes to this project will be documented in this file.
This project adheres (or attempts to adhere) to [Semantic Versioning](http://semver.org/).

## [0.4.0] - 2018-02-26
- Allow to (sort-of) use the getSearchForm() function directly, without using a parser hook.
  This allow it to be used for skin:Helena's navbar search.

## [0.3.0] - 2018-02-20
- Change style to fit new homepage design

## [0.2.0] - 2015-08-20
- New options: 'fancybutton', 'elementsize', 'inline', 'hiddenbuttonlabel'
- remove option 'size' in favor of the new 'widthclasses', which can use any of bootstrap's grid
  options: col-(xs|sm|md|lg)-1..12.
- Remove unused option 'break'

## [0.1.0] - 2015-08-20
Initial release. Two types of search forms:

  - 'standard' - a simple inline search form
  - 'mainpage' - simply an enlarged version of 'standard'
