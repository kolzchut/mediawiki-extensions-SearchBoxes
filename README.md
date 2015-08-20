Extension:SearchBoxes for MediaWiki
===================================

Copyright (C) Dror S. & [Kol-Zchut Ltd](http://www.kolzchut.org.il).
Loosely based on [Extension:InputBox][inputboxurl] by Erik Möller.

This MediaWiki extension supplies two types of Bootstrap3-themed search forms:
* 'standard' - a simple inline search form
* 'mainpage' - simply an enlarged version of 'standard'


## Caveats
This extension is very much Kol-Zchut (WikiRights) centric right now. It assumes the presence of
Boostrap 3 as well as Skin:Helena (unpublished) along with its global LESS variables.


## Usage
This is a tag extension: <searchbox></searchbox>. The following options are available:

* 'type':        'standard' (default) / 'mainpage'
* 'width':       width in percentage (omit the '%' sign)
* 'placeholder': a placeholder text for the inputbox
* 'default':     default text in the input box (will hide the placeholder if specified)
* 'buttonlabel': text for the search button. 'Search' by default.
* 'labeltext':   label for the input field. 
* 'id':          an id attribute for the form. Currently unspecified behavior.
* 'category':    a category to filter by. Passed to the search form as "incategory:".
* 'dir':         'rtl' / 'ltr'. Allows to override the form's default direction.
* 'internal':    'yes' / 'no' (default) - whether to use 'Special:Search' even if Extension:WRGoogleSearch is on.
* 'mobile':      'yes' (default) / 'no' - whether to show the form in mobile resolutions (basically Bootstrap's
				 'hidden-xs')
				 
### Example
	<searchbox>
		placeholder=Find information about your rights
		buttonlabel=Search!
		category=rights
		internal=yes
		mobile=no
	</searchbox>

## Dependencies
Require MediaWiki >= 1.24.

## Installation
See the [regular installation instructions][mw-instructions] for MediaWiki extensions.
	


## Configuration
None at this point.



[inputboxurl]: https://www.mediawiki.org/wiki/Extension:InputBox
[mw-instructions]: https://www.mediawiki.org/wiki/Manual:Extensions#Installing_an_extension
