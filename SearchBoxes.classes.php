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

	private $validElementSizes = array( 'small', 'normal', 'large');
	private $validExtraClassesRegEx = '/^col-(?:xs|sm|md|lg)-(?:10|11|12|[1-9])$/';


	private $mParser;
	private $mType = '';
	private $mDefaultText = '';
	private $mInline = 'no';
	private $mButtonLabel = '';
	private $mHiddenButtonLabel = 'no';
	private $mPlaceholderText = '';
	private $mLabelText = '';
	private $mID = '';
	private $mCategory = '';
	private $mDir = '';
	private $mInternal = 'no';
	private $mMobile = 'yes';
	private $mFancyButton = 'no';
	private $mElementSize = '';
	private $mWidthClasses = '';


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
		$classes = array();

		if ( isset( $this->mLabelText ) && strlen( trim( $this->mLabelText ) ) ) {
			$this->mLabelText = $this->mParser->recursiveTagParse( $this->mLabelText );
		} else {
			// No user supplied label, so apply a default but show it only to screen-readers
			$this->mLabelText = wfMessage( 'search' );
			$classes          = array( 'sr-only' );
		}

		$htmlLabel = Html::element(
			'label',
			array(
				'for' => $inputID,
				'class' => implode( ' ', $classes )
			),
			$this->mLabelText
		);


		$classes = array( 'searchForm', 'hidden-print' );

		if ( $this->mInline !== 'yes') {
			$classes[] = 'row';
		}

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

		$classes = array( 'input-group' );
		if ( $type === 'mainpage' || $this->mElementSize === 'large' ) {
			$classes[] = 'input-group-lg';
		} elseif ( $this->mElementSize === 'small' ) {
			$classes[] = 'input-group-sm';
		}
		if ( is_array( $this->mWidthClasses ) ) {
			$classes = array_merge( $classes, $this->mWidthClasses );
		};

		$htmlOut .= Html::openElement( 'div',
			array(
				'class' => implode( ' ', $classes ),
			)
		);
		$htmlOut .= $htmlLabel;

		$classes = array( 'form-control', 'mw-searchInput' );

		if ( $this->mInternal === 'yes' ) {
			$classes[] = 'internalSearch';
		}

		$htmlOut .= Html::element(
			'input',
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

		$classes = array( 'btn', 'searchBtn' );

		if ( $this->mFancyButton === 'yes' || $type === 'mainpage' ) {
			$classes[] = 'btn-default';
		};

		$htmlOut .= Html::openElement( 'button',
			array(
				'type' => 'submit',
				'name' => 'go',
				'title' => $this->mButtonLabel,
				'class' => implode( ' ', $classes )
			)
		);
		$htmlOut .= Html::openElement( 'span',
		    array(
		        'class' => 'btn-text' . ( $this->mHiddenButtonLabel === 'yes' ? ' sr-only' : '' ),
		    )
		);
		$htmlOut .= $this->mButtonLabel . '&nbsp;';
		$htmlOut .= Html::closeElement( 'span' ); // button text

		$htmlOut .= Html::element( 'span',
			array(
				'class' => 'icon icon-search',
			)
		);
		$htmlOut .= Html::closeElement( 'button' );	// button
		$htmlOut .= Xml::closeElement( 'span' ); // input-group-btn

		$htmlOut .= Xml::closeElement( 'div' );
		$htmlOut .= Xml::closeElement( 'form' );

		if ( $type === 'mainpage' ) {
			$this->mParser->getOutput()->addModuleStyles( 'ext.searchboxes.mainpage.styles' );
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
			'default' => 'mDefaultText',
			'placeholder' => 'mPlaceholderText',
			'buttonlabel' => 'mButtonLabel',
			'fancybutton' => 'mFancyButton',
			'labeltext' => 'mLabelText',
			'id' => 'mID',
			'category' => 'mCategory',
			'dir' => 'mDir',
			'internal' => 'mInternal',
			'mobile' => 'mMobile',
			'elementsize' => 'mElementSize',
			'hiddenbuttonlabel' => 'mHiddenButtonLabel',
			'widthclasses' => 'mWidthClasses'
		);
		foreach ( $options as $name => $var ) {
			if ( isset( $values[$name] ) ) {
				$this->$var = $values[$name];
			}
		}

		// Validate css classes
		$classes = explode( ' ', $this->mWidthClasses );
		$this->mWidthClasses = array();
		foreach ( $classes as $class ) {
			$valid = preg_match( $this->validExtraClassesRegEx, $class );
			if ( $valid === 1 ) {
				$this->mWidthClasses[] = $class;
			}
		}

		if ( !in_array( $this->mElementSize, $this->validElementSizes ) ) {
			$this->mElementSize = 'normal';
		}
	}


}
