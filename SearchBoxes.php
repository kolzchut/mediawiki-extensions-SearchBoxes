<?php

/**
 * SearchBoxes extension
 *
 * @file
 * @ingroup Extensions
 *
 * @author Dror S. [FFS]
 * @copyright © 2015 Dror S. & Kol-Zchut Ltd.
 * Loosely based on Extension:InputBox.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */
# Not a valid entry point, skip unless MEDIAWIKI is defined
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 1 );
}

/* Configuration */

// Credits
$wgExtensionCredits['parserhook'][] = [
	'path'           => __FILE__,
	'name'           => 'SearchBoxes',
	'author' => [ 'Dror S. [FFS] ([http://www.kolzchut.org.il Kol-Zchut])' ],
	'url'            => 'https://github.com/kolzchut/mediawiki-extensions-SearchBoxes',
	'license-name'    => 'GPL-2.0+',
	'descriptionmsg' => 'searchboxes-desc',
	'version'        => '0.4.0'
];

// Internationalization
$wgMessagesDirs['SearchBoxes'] = __DIR__ . '/i18n';

// Register auto load for the special page class
$wgAutoloadClasses['SearchBoxesHooks'] = __DIR__ . '/SearchBoxes.hooks.php';
$wgAutoloadClasses['SearchBoxes'] = __DIR__ . '/SearchBoxes.classes.php';

// Register parser hook
$wgHooks['ParserFirstCallInit'][] = 'SearchBoxesHooks::onParserFirstCallInit';
$wgHooks['SpecialSearchSetupEngine'][] = 'SearchBoxesHooks::onSpecialSearchSetupEngine';

// Register ResourceLoader modules
$wgResourceModules['ext.searchboxes.white.styles'] = [
	'localBasePath' => __DIR__ . '/modules',
	'remoteExtPath' => 'WikiRights/SearchBoxes/modules',
	'styles' => 'ext.searchBoxes.white.less',
	'class' => 'HelenaResourceLoaderModule'
];

