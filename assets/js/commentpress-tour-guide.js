/**
 * CommentPress Tour Guide Javascript.
 *
 * Implements the Intro.js tour.
 *
 * @since 0.1
 */

/**
 * Create global namespace.
 *
 * @since 0.1
 */
var CommentPressTourGuide = CommentPressTourGuide || {};

/**
 * Create settings class.
 *
 * Unused at present, but kept as a useful template.
 *
 * @since 0.1
 */
CommentPressTourGuide.settings = new function() {

	// Store object refs.
	var me = this,
		$ = jQuery.noConflict();

	// Init localisation array.
	me.localisation = [];

	// override if we have our localisation object.
	if ( 'undefined' !== typeof CommentPressTourGuideSettings ) {
		me.localisation = CommentPressTourGuideSettings.localisation;
	}

	/**
	 * Getter for localisation.
	 *
	 * @param {String} identifier The localisation string to retrieve.
	 */
	this.get_localisation = function( identifier ) {
		return me.localisation[identifier];
	};

	// Init steps array.
	me.steps = [];

	// override if we have our data object.
	if ( 'undefined' !== typeof CommentPressTourGuideSettings ) {
		me.steps = CommentPressTourGuideSettings.steps;
	}

	/**
	 * Getter for steps.
	 *
	 * @return {Array} steps The steps array.
	 */
	this.get_steps = function() {
		return me.steps;
	};

};

/**
 * Create tour class.
 *
 * @since 0.1
 */
CommentPressTourGuide.tour = new function() {

	// Store object refs.
	var me = this,
		$ = jQuery.noConflict();

	// Init Intro.js holder.
	me.tour_guide = {};

	/**
	 * Initialise.
	 *
	 * This method should only be called once.
	 *
	 * @since 0.1
	 */
	this.init = function() {

	};

	/**
	 * Do setup when jQuery reports that the DOM is ready.
	 *
	 * This method should only be called once.
	 *
	 * @since 0.1
	 */
	this.dom_ready = function() {

		// Set up tour.
		me.setup_tour();

		// Set up button.
		me.setup_button();

		// Start tour.
		me.start_tour();

	};

	/**
	 * Create the tour start button.
	 *
	 * @since 0.1
	 */
	this.setup_button = function() {

		// Construct a simple button.
		var markup = '<button type="button" class="commentpress_start_tour">' +
						CommentPressTourGuide.settings.get_localisation( 'button' ) +
					 '</button>';

		// Add to header.
		$('#page_title').after( markup );

		/**
		 * Add a click event listener to start the tour.
		 *
		 * @param {Object} event The event object.
		 */
		$('#header').on( 'click', '.commentpress_start_tour', function( event ) {
			if ( event.preventDefault ) { event.preventDefault(); }
			me.start_tour();
		});

	};

	/**
	 * Set up the tour.
	 *
	 * @since 0.1
	 */
	this.setup_tour = function() {

		// Init Intro.js.
		me.tour_guide = introJs();

		// Assign steps.
		me.tour_guide.setOptions({
			steps: CommentPressTourGuide.settings.get_steps()
		});

		// Set callbacks.
		me.tour_guide.onbeforechange( me.onbeforechange );
		me.tour_guide.onchange( me.onchange );
		me.tour_guide.onafterchange( me.onafterchange );

	};

	/**
	 * Start the tour.
	 *
	 * @since 0.1.3
	 */
	this.start_tour = function() {

		// Start tour.
		CommentPress.common.content.quick_scroll_page( '#container', 0 );
		window.scrollTo(0,0);
		me.tour_guide.start();

	};

	/**
	 * Callback for onbeforechange event.
	 *
	 * @since 0.1
	 *
	 * @param {Object} element The target element.
	 */
	this.onbeforechange = function( element ) {

		var $el = $(element);

		// Act on Navigate button highlight.
		if ( $el.hasClass( 'navigation-button' ) ) {

			// Begin by resetting page scroll.
			CommentPress.common.content.quick_scroll_page( '#container', 0 );

			// If the "contents" column is visible.
			if ( $('body').hasClass( 'active-nav' ) ) {

				// Switch view as we're coming from step 1.
				$('#switcher .navigation-button').click();


			}

		}

		// Act on Contents menu highlight.
		if ( $el.attr( 'id' ) == 'navigation' ) {

			// Reset page scroll.
			CommentPress.common.content.quick_scroll_page( '#container', 0 );

			// Hide search.
			if ( $('.search_wrapper').is(':visible') ) {
				$('.search_wrapper').hide();
			} else {

				// Switch view as we're coming from step 1.
				$('#switcher .navigation-button').click();

			}

		}

		// Act on search highlight.
		if ( $el.hasClass( 'search_heading' ) ) {

			// Hide special pages.
			if ( $('.special_pages_wrapper').is(':visible') ) {
				$('.special_pages_wrapper').hide();
			}

			// Show search.
			if ( $('.search_wrapper').is(':hidden') ) {
				$('.search_wrapper').show();
			}

		}

		// Act on special pages highlight.
		if ( $el.hasClass( 'special_pages_heading' ) ) {

			// Hide search
			if ( $('.search_wrapper').is(':visible') ) {
				$('.search_wrapper').hide();
			}

			// Show special pages.
			if ( $('.search_wrapper').is(':hidden') ) {
				$('.special_pages_wrapper').show();
			}

		}

		// Act on chapter highlight.
		if ( $el.hasClass( 'page_item' ) ) {

			// Activate nav if not already active.
			if ( ! $('body').hasClass( 'active-nav' ) ) {
				$('#switcher .navigation-button').click();
			}

			// Clear highlights.
			CommentPress.texthighlighter.textblocks.highlights_clear_for_comment();

			// Hide special pages.
			if ( $('.special_pages_wrapper').is(':visible') ) {
				$('.special_pages_wrapper').hide();
			}

		}

		// Act on paragraph highlight.
		if ( $el.hasClass( 'textblock' ) ) {
			$el.click();
		}

		// Act on paragraph highlight.
		if ( $el.hasClass( 'textblock' ) && 6 == me.tour_guide.getStep() ) {
			CommentPress.texthighlighter.textblocks.selection_recall_for_comment( '3056' );
		}

		// Act on para marker highlight.
		if ( $el.hasClass( 'textblock_permalink' ) ) {
			$el.click();
			CommentPress.texthighlighter.textblocks.highlights_clear_for_comment();
		}

		// Act on icon highlight.
		if ( $el.hasClass( 'commenticonbox' ) ) {
			$el.click();
		}

		// The main column needs highlighting when bubble moves to it.
		if ( $el.hasClass( 'textblock' ) || $el.hasClass( 'commenticonbox' ) ) {
			$('#switcher .content-button').click();
		}

		/*
		// Act on sidebar highlight.
		if ( $el.attr( 'id' ) == 'sidebar' ) {
			$('#switcher .sidebar-button').click();
		}
		*/

		// Act on comments button highlight.
		if ( $el.hasClass( 'comments-button' ) ) {
			$('#switcher .comments-button').click();
		}

		// Act on comment highlight.
		if ( $el.attr( 'id' ) == 'li-comment-3056' ) {
			$('#switcher .sidebar-button').click();
			$('#comments_header a').click();
			$('#activity_sidebar').hide();
			$el.parents('div.paragraph_wrapper').show();
			$('#li-comment-3056 .comment_permalink').click();
			/*
			CommentPress.common.comments.scroll_comments(
				$('#para_heading-pTmttroepwsyworhbsaiWBCaAvccdcoapowtIsbvajtyfiRTYatfatrrh'), 0
			);
			*/
		}

		// Act on JSTOR highlight.
		if ( $el.hasClass( 'commentpress_jstor_trigger' ) ) {
			$('#comments_header a').click();
			$('#activity_sidebar').hide();
			CommentPress.common.comments.scroll_comments(
				$('h3.bpgsites_group_filter_heading'), 0
			);
			$el.click();
		}

		// Act on activity button highlight.
		if ( $el.hasClass( 'activity-button' ) ) {
			$('#switcher .activity-button').click();
		}

	};

	/**
	 * Callback for onchange event.
	 *
	 * @since 0.1
	 *
	 * @param {Object} element The target element.
	 */
	this.onchange = function( element ) {

		var $el = $(element);

		// Act on toc menu highlight.
		if ( $el.attr( 'id' ) == 'navigation' ) {
			//$('#switcher .navigation-item .navigation-button').click();
		}

	};

	/**
	 * Callback for onafterchange event.
	 *
	 * @since 0.1
	 *
	 * @param {Object} element The target element.
	 */
	this.onafterchange = function( element ) {

		// Refresh UI to make bounding box fit.
		me.refresh_ui( element );

	};

	/**
	 * Callback for refresh event.
	 *
	 * @since 0.1
	 *
	 * @param {Object} element The target element.
	 */
	this.refresh_ui = function( element ) {

		//window.scrollTo(0,0);

		setTimeout(function() {
			me.refresh( element );
		}, 200);

	};

	/**
	 * Callback for refresh event.
	 *
	 * @since 0.1
	 *
	 * @param {Object} element The target element.
	 */
	this.refresh = function( element ) {

		me.tour_guide.refresh();

		var $el = $(element);

		// Act on TOC column highlights.
		if (
			$el.hasClass( 'page_item' ) ||
			$el.hasClass( 'search_heading' ) ||
			$el.hasClass( 'special_pages_heading' )
		) {
			$('.introjs-helperLayer').css('opacity', '0');
		}

		// Act on comment highlight.
		if (
			$el.attr( 'id' ) == 'comments_header' ||
			$el.attr( 'id' ) == 'activity_header' ||
			$el.attr( 'id' ) == 'li-comment-3056'
		) {
			$('.introjs-helperLayer.introjs-fixedTooltip').css('opacity', '0');
		}

	};

};

// Do immediate actions.
CommentPressTourGuide.tour.init();

/**
 * Define what happens when the page is ready.
 *
 * @since 0.1
 */
jQuery(document).ready( function($) {

	// Document ready.
	CommentPressTourGuide.tour.dom_ready();

});
