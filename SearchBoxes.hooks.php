<?php
/**
 * Hooks for SearchBoxes extension
 *
 * @file
 * @ingroup Extensions
 */

class SearchBoxesHooks {

	public static function onParserFirstCallInit( Parser &$parser ) {
		// Register the hook with the parser
		$parser->setHook( 'searchbox', array( 'SearchBoxesHooks', 'render' ) );

		// Continue
		return true;
	}

	// Render the input box
	public static function render( $input, $args, Parser $parser ) {
		// Create InputBox
		$searchBox = new SearchBoxes( $parser );

		// Configure InputBox
		$searchBox->extractOptions( $parser->replaceVariables( $input ) );

		// Return output
		return $searchBox->render();
	}

	/**
	 * Filter by category; since there's no separate param for that,
	 * we pick that up here and munge "incategory" and the search term together
	 *
	 * @param $search SpecialSearch
	 * @param $profile
	 * @param $engine SearchEngine
	 *
	 * @return bool
	 */
	public static function onSpecialSearchSetupEngine( $search, $profile, $engine ) {
		$out = $search->getOutput();
		$request = $search->getRequest();
		$params = $request->getValues();

		if ( isset( $params['category'] ) && $params['category'] !== '' ) {
			$params['search'] .= ' incategory:"' . $params['category'] . '"';
			unset( $params['category'] );

			$out->redirect( $search->getPageTitle()->getFullURL( $params ) );
		}

		return true;

	}


}
