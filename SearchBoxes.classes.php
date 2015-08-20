<?php
/**
 * Classes for SearchBoxes extension
 *
 * @file
 * @ingroup Extensions
 */

// InputBox class
class SearchBoxes {

	/* Fields */

	private $mParser;
	private $mType = '';
	private $mWidth = 100;
	private $mBR = 'yes';
	private $mDefaultText = '';
	private $mButtonLabel = '';
	private $mPlaceholderText = '';
	private $mLabelText = '';
	private $mID = '';
	private $mCategory = '';
	private $mDir = '';
	private $mInternal = 'no';
	private $mMobile = 'yes';


	/* Functions */

	public function __construct( Parser $parser ) {
		$this->mParser = $parser;
		// Default value for dir taken from the page language (bug 37018)
		$this->mDir = $this->mParser->getTargetLanguage()->getDir();
		// Split caches by language, to make sure visitors do not see a cached
		// version in a random language (since labels are in the user language)
		$this->mParser->getOptions()->getUserLangObj();
	}

	public function render() {
		// Handle various types
		switch ( $this->mType ) {
			case 'mainpage':
				return $this->getSearchForm( 'mainpage' );
			case 'search':
			default:
				return $this->getSearchForm();

		}
	}


	/**
	 * Search form for our main page, bootstrap-themed
	 */
	public function getMainPageSearchForm() {
		return $this->getSearchForm( 'mainpage' );
	}

	/**
	 * Generate search form
	 *
	 * @param string $type
	 * @return string HTML
	 *
	 */
	public function getSearchForm( $type = 'standard' ) {
		// Use button label fallbacks
		if ( !$this->mButtonLabel ) {
			$this->mButtonLabel = wfMessage( 'search' )->text();
		}

		if ( $this->mID !== '' ) {
			$this->mID = Sanitizer::escapeId( $this->mID );
			$idArray = array( 'id' => $this->mID );
		} else {
			$idArray = array();
		}

		$inputID = 'searchInput' . ( $this->mID ?: rand() );

		$htmlLabel = '';
		if ( isset( $this->mLabelText ) && strlen( trim( $this->mLabelText ) ) ) {
			$this->mLabelText = $this->mParser->recursiveTagParse( $this->mLabelText );
			$htmlLabel = Html::openElement( 'label', array( 'for' => $inputID ) );
			$htmlLabel .= $this->mLabelText;
			$htmlLabel .= Html::closeElement( 'label' );
		}

		$classes = array( 'searchForm', 'hidden-print' );
		if ( $this->mMobile === 'no') {
			$classes[] = 'hidden-xs';
		}
		if ( $type === 'mainpage' ) {
			$classes[] = 'mainPageSearchForm';
		}


		$htmlOut = Html::openElement( 'form',
			array(
				'name' => 'searchForm' . $this->mID,
				'class' => implode( ' ', $classes ),
				'style' => 'width: ' . $this->mWidth . '%',
				'action' => SpecialPage::getTitleFor( 'Search' )->getLocalURL(),
			) + $idArray
		);

		if ( $this->mCategory != '' ) {
			$htmlOut .= Xml::element( 'input',
				array(
					'name' => 'category',
					'type' => 'hidden',
					'value' => $this->mCategory,
				)
			);
		}

		$htmlOut .= Html::openElement( 'div',
			array(
				'class' => 'input-group',
			)
		);
		$htmlOut .= $htmlLabel;

		$classes = array( 'form-control', 'mw-searchInput' );
		if ( $type === 'mainpage' ) {
			$classes[] = 'input-lg';
		}
		if ( $this->mInternal === 'yes' ) {
			$classes[] = 'internalSearch';
		}

		$htmlOut .= Html::element( 'input',
			array(
				'id' => $inputID,
				'type' => 'text',
				'name' => 'search',
				'dir' => $this->mDir,
				'value' => $this->mDefaultText,
				'class' => implode( ' ', $classes ),
				'placeholder' => $this->mPlaceholderText,
			)
		);

		$htmlOut .= Html::openElement( 'span',
			array(
				'class' => 'input-group-btn',
			)
		);

		$htmlOut .= Html::openElement( 'button',
			array(
				'type' => 'submit',
				'name' => 'go',
				'title' => $this->mButtonLabel,
				'class' => 'btn btn-default searchBtn ' . ( $type == 'mainpage' ? 'btn-lg ' : '' ),
			)
		);
		$htmlOut .= Html::openElement( 'span',
		    array(
		        'class' => 'btn-text',
		    )
		);
		$htmlOut .= $this->mButtonLabel . '&nbsp;';
		$htmlOut .= Html::closeElement( 'span' ); // button text

		$htmlOut .= Html::element( 'span',
			array(
				'class' => 'icon-search',
			)
		);
		$htmlOut .= Html::closeElement( 'button' );	// button
		$htmlOut .= Xml::closeElement( 'span' ); // input-group-btn

		$htmlOut .= Xml::closeElement( 'div' );
		$htmlOut .= Xml::closeElement( 'form' );

		if ( $type === 'mainpage' ) {
			$this->mParser->getOutput()->addModules( 'ext.searchboxes.mainpage' );
		}


		// Return HTML
		return $htmlOut;
	}


	/**
	 * Extract options from a blob of text
	 *
	 * @param string $text Tag contents
	 */
	public function extractOptions( $text ) {
		// Parse all possible options
		$values = array();
		foreach ( explode( "\n", $text ) as $line ) {
			if ( strpos( $line, '=' ) === false )
				continue;
			list( $name, $value ) = explode( '=', $line, 2 );
			$values[ strtolower( trim( $name ) ) ] = Sanitizer::decodeCharReferences( trim( $value ) );
		}

		// Validate the dir value.
		if ( isset( $values['dir'] ) && !in_array( $values['dir'], array( 'ltr', 'rtl' ) ) ) {
			unset( $values['dir'] );
		}

		// Build list of options, with local member names
		$options = array(
			'type' => 'mType',
			'width' => 'mWidth',
			'break' => 'mBR',
			'default' => 'mDefaultText',
			'placeholder' => 'mPlaceholderText',
			'buttonlabel' => 'mButtonLabel',
			'labeltext' => 'mLabelText',
			'id' => 'mID',
			'category' => 'mCategory',
			'dir' => 'mDir',
			'internal' => 'mInternal',
			'mobile' => 'mMobile'
		);
		foreach ( $options as $name => $var ) {
			if ( isset( $values[$name] ) ) {
				$this->$var = $values[$name];
			}
		}

		// Insert a line break if configured to do so
		$this->mBR = ( strtolower( $this->mBR ) == "no" ) ? ' ' : '<br />';

		// Validate the width; make sure it's a valid, positive integer
		$this->mWidth = intval( $this->mWidth <= 0 ? 100 : $this->mWidth );

	}


}
