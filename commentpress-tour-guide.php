<?php
/**
 * CommentPress Tour Guide
 *
 * Plugin Name:       CommentPress Tour Guide
 * Description:       Creates a tour of CommentPress using Intro.js.
 * Plugin URI:        https://github.com/digital-thoreau/commentpress-tour-guide
 * GitHub Plugin URI: https://github.com/digital-thoreau/commentpress-tour-guide
 * Version:           0.2.0a
 * Author:            Christian Wach
 * Author URI:        https://haystack.co.uk
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Requires at least: 5.7
 * Requires PHP:      7.4
 * Text Domain:       commentpress-tour-guide
 * Domain Path:       /languages
 *
 * @package CommentPress_Tour_Guide
 * @link    https://github.com/digital-thoreau/commentpress-tour-guide
 * @license GPL v2 or later
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Set our version here.
define( 'COMMENTPRESS_TOUR_GUIDE_VERSION', '0.2.0a' );

// Store reference to this file.
if ( ! defined( 'COMMENTPRESS_TOUR_GUIDE_FILE' ) ) {
	define( 'COMMENTPRESS_TOUR_GUIDE_FILE', __FILE__ );
}

// Store URL to this plugin's directory.
if ( ! defined( 'COMMENTPRESS_TOUR_GUIDE_URL' ) ) {
	define( 'COMMENTPRESS_TOUR_GUIDE_URL', plugin_dir_url( COMMENTPRESS_TOUR_GUIDE_FILE ) );
}
// Store PATH to this plugin's directory.
if ( ! defined( 'COMMENTPRESS_TOUR_GUIDE_PATH' ) ) {
	define( 'COMMENTPRESS_TOUR_GUIDE_PATH', plugin_dir_path( COMMENTPRESS_TOUR_GUIDE_FILE ) );
}

/**
 * CommentPress Tour Guide Class.
 *
 * A class that encapsulates plugin functionality.
 *
 * @since 0.1
 */
class CommentPress_Tour_Guide {

	/**
	 * The ID of the Help Page.
	 *
	 * @since 0.1.4
	 * @access public
	 * @var integer
	 */
	public $post_id = 7;

	/**
	 * Minfied scripts identifer.
	 *
	 * @since 0.1
	 * @access public
	 * @var string
	 */
	public $minified = '.min';

	/**
	 * Constructor.
	 *
	 * @since 0.1
	 */
	public function __construct() {

		// Use uncompressed scripts when debugging.
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true ) {
			$this->minified = '';
		}

		// Register hooks.
		$this->register_hooks();

	}

	/**
	 * Register hooks.
	 *
	 * @since 0.1
	 */
	public function register_hooks() {

		// Enable translation.
		add_action( 'init', [ $this, 'enable_translation' ] );

		// Register public styles.
		add_action( 'wp_enqueue_scripts', [ $this, 'front_end_enqueue_styles' ], 20000 );

		// Register public scripts.
		add_action( 'wp_enqueue_scripts', [ $this, 'front_end_enqueue_scripts' ], 20000 );

		// Add link in admin bar.
		add_action( 'wp_before_admin_bar_render', [ $this, 'admin_bar_tweaks' ], 1000 );

	}

	/**
	 * Load translation files.
	 *
	 * A good reference on how to implement translation in WordPress:
	 *
	 * @see http://ottopress.com/2012/internationalization-youre-probably-doing-it-wrong/
	 *
	 * @since 0.1
	 */
	public function enable_translation() {

		// Load translations if present.
		// phpcs:ignore WordPress.WP.DeprecatedParameters.Load_plugin_textdomainParam2Found
		load_plugin_textdomain(
			// Unique name.
			'commentpress-tour-guide',
			// Deprecated argument.
			'',
			// Relative path to translation files.
			dirname( plugin_basename( COMMENTPRESS_TOUR_GUIDE_FILE ) ) . '/languages/'
		);

	}

	/**
	 * Show "help" in menu.
	 *
	 * @since 0.1.1
	 */
	public function admin_bar_tweaks() {

		// No need to show on the help page itself.
		global $post;
		if ( is_object( $post ) && $this->post_id === (int) $post->ID ) {
			return;
		}

		// Access object.
		global $wp_admin_bar;

		// Create Menu Item.
		$args = [
			'id'    => 'walden-help',
			'title' => __( 'Take a Tour', 'commentpress-tour-guide' ),
			'href'  => '/walden/how-to-read/',
			// 'parent' => 'top-secondary',
		];

		// Add Menu Item.
		$wp_admin_bar->add_menu( $args );

	}

	/**
	 * Restrict appearance of this plugin.
	 *
	 * @since 0.1.1
	 *
	 * @return bool True if allowed to view, false otherwise
	 */
	public function can_view() {

		/*
		// Restrict to admins only.
		if ( ! is_super_admin() ) {
			return false;
		}
		*/

		// Only load on "How to Read" page.
		global $post;
		if ( ! is_object( $post ) || $this->post_id !== (int) $post->ID ) {
			return false;
		}

		// --<
		return true;

	}

	/**
	 * Add our front-end stylesheets.
	 *
	 * Currently using the 2.0.0 version of IntroJS. The included files are the
	 * from the latest stable release.
	 *
	 * @see https://introjs.com/
	 *
	 * @since 0.1
	 */
	public function front_end_enqueue_styles() {

		// If allowed.
		if ( $this->can_view() ) {

			// Enqueue IntroJS stylesheet.
			wp_enqueue_style(
				'commentpress_introjs_css',
				COMMENTPRESS_TOUR_GUIDE_URL . 'assets/css/introjs' . $this->minified . '.css',
				false,
				COMMENTPRESS_TOUR_GUIDE_VERSION, // Version.
				'all' // Media.
			);

		}

		// Enqueue our stylesheet.
		wp_enqueue_style(
			'commentpress_introjs_custom_css',
			COMMENTPRESS_TOUR_GUIDE_URL . 'assets/css/commentpress-tour-guide.css',
			false,
			COMMENTPRESS_TOUR_GUIDE_VERSION, // Version.
			'all' // Media.
		);

	}

	/**
	 * Add our front-end Javascripts.
	 *
	 * Currently using the 2.0.0 version of IntroJS. The included files are the
	 * from the latest stable release.
	 *
	 * @see https://introjs.com/
	 *
	 * @since 0.1
	 */
	public function front_end_enqueue_scripts() {

		// Bail if not allowed.
		if ( ! $this->can_view() ) {
			return;
		}

		// Enqueue IntroJS script.
		wp_enqueue_script(
			'commentpress_introjs_js',
			COMMENTPRESS_TOUR_GUIDE_URL . 'assets/js/intro' . $this->minified . '.js',
			[ 'jquery' ],
			COMMENTPRESS_TOUR_GUIDE_VERSION,
			true // In footer.
		);

		// Enqueue our custom Javascript.
		wp_enqueue_script(
			'commentpress_introjs_custom_js',
			COMMENTPRESS_TOUR_GUIDE_URL . 'assets/js/commentpress-tour-guide.js',
			[ 'commentpress_introjs_js' ],
			COMMENTPRESS_TOUR_GUIDE_VERSION,
			true // In footer.
		);

		// Init localisation.
		$localisation = [
			'button' => __( 'Start tour', 'commentpress-tour-guide' ),
		];

		// Localisation array.
		$vars = [
			'localisation' => $localisation,
			'steps'        => $this->get_steps(),
		];

		// Localise the WordPress way.
		wp_localize_script(
			'commentpress_introjs_custom_js',
			'CommentPressTourGuideSettings',
			$vars
		);

	}

	/**
	 * Get steps for IntroJS.
	 *
	 * Currently using the 2.0.0 version of IntroJS.
	 *
	 * @see https://introjs.com/
	 *
	 * @since 0.1
	 *
	 * @return array $steps The populated array of steps for IntroJS.
	 */
	public function get_steps() {

		// Init Intro.js steps.
		$steps = [];

		$steps[] = [
			'element' => '#switcher .navigation-button',
			'intro'   => __( 'Click or tap the "Navigate" button to reveal the "Contents" column.', 'commentpress-tour-guide' ),
		];

		$steps[] = [
			'element'  => '#navigation',
			'intro'    => __( 'The "Contents" column helps you move between sections of the site.', 'commentpress-tour-guide' ),
			'position' => 'right',
		];

		$steps[] = [
			'element'  => '#navigation h3.search_heading',
			'intro'    => __( 'Click or tap "Search" to reveal the search bar below.', 'commentpress-tour-guide' ),
			'position' => 'right',
		];

		$steps[] = [
			'element'  => '#navigation h3.special_pages_heading',
			'intro'    => __( 'Click or tap "Special Pages" to find links to pages such as "All Comments" or "Comments by Commenter".', 'commentpress-tour-guide' ),
			'position' => 'right',
		];

		$steps[] = [
			'element'  => '#navigation li.page_item.page-item-58',
			'intro'    => __( 'Click or tap a menu item to go to a chapter or page in the text.', 'commentpress-tour-guide' ),
			'position' => 'right',
		];

		$steps[] = [
			'element' => '.post p.textblock:first-of-type',
			'intro'   => __( 'Click or tap a paragraph to read comments that have been left on it.', 'commentpress-tour-guide' ),
		];

		$steps[] = [
			'element' => '#textblock-pToaciiWpttalLcfeEhbditompPtwrsptwaipcbe',
			'intro'   => __( 'Select some text and choose "Quote and Comment" to have the text added automatically to the top of your comment. The paragraph text will appear highlighted when your comment is saved.', 'commentpress-tour-guide' ),
		];

		$steps[] = [
			'element'  => '.post p.textblock:first-of-type .para_marker a',
			'intro'    => __( 'This is a paragraph permalink. Click or tap it to show a shareable link in your browser’s location bar.', 'commentpress-tour-guide' ),
			'position' => 'right',
		];

		$steps[] = [
			'element'  => '.post p.textblock:first-of-type .commenticonbox',
			'intro'    => __( 'The comment bubble shows the number of comments on this paragraph. Click or tap it to bring the comment form into view.', 'commentpress-tour-guide' ),
			'position' => 'left',
		];

		$steps[] = [
			'element' => '#switcher .comments-button',
			'intro'   => __( 'Click or tap the "Comments" button to read or reply to comments on the text.', 'commentpress-tour-guide' ),
		];

		$steps[] = [
			'element'  => '#li-comment-3056',
			'intro'    => __( 'This is a comment. Click or tap the date to show a shareable link to this comment in your browser’s location bar.', 'commentpress-tour-guide' ),
			'position' => 'left',
		];

		$steps[] = [
			'element'  => '#sidebar p[data-jstor-textsig="pToaciiWpttalLcfeEhbditompPtwrsptwaipcbe"]',
			'intro'    => __( 'Click or tap "Find references in JSTOR articles" to retrieve summaries of articles in the JSTOR archives that relate to a particular paragraph.', 'commentpress-tour-guide' ),
			'position' => 'left',
		];

		$steps[] = [
			'element' => '#switcher .activity-button',
			'intro'   => __( 'Click or tap the "Activity" button to see specific kinds of activity, such as recent comments on this page.', 'commentpress-tour-guide' ),
		];

		// --<
		return $steps;

	}

}

/**
 * Bootstraps plugin if not yet loaded and returns reference.
 *
 * @since 0.1.4
 *
 * @return CommentPress_Tour_Guide $plugin The plugin reference.
 */
function commentpress_tour_guide() {

	// Maybe bootstrap plugin.
	static $plugin;
	if ( ! isset( $plugin ) ) {
		$plugin = new CommentPress_Tour_Guide();
	}

	// Return reference.
	return $plugin;

}

// Bootstrap immediately.
commentpress_tour_guide();

/*
 * Uninstall uses the 'uninstall.php' method.
 *
 * @see https://developer.wordpress.org/reference/functions/register_uninstall_hook/
 */
