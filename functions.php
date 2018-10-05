<?php
/**
 * Fixed functions and definitions
 *
 * @package Fixed
 * @since Fixed 1.0
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * @since Fixed 1.0
 */
if ( ! isset( $content_width ) )
	$content_width = 690; /* pixels */


if ( ! function_exists( 'fixed_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 * @since Fixed 1.0
 */
function fixed_setup() {

	// Admin functionality
	if ( is_admin() ) {

		// Getting Started page
		require_once( get_template_directory() . '/includes/admin/updater/theme-updater.php' );

		// Meta boxes
		require_once( get_template_directory() . '/includes/admin/metabox/metabox.php' );

		// TGM Activation Class
		require_once( get_template_directory() . '/includes/admin/tgm/tgm-activation.php' );

		// Editor styles
		require_once( get_template_directory() . '/includes/editor/add-styles.php' );
		add_editor_style();
	}

	// Customizer settings
	require_once( get_template_directory() . '/customizer.php' );

	// Add posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Enable support for Post Thumbnails
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 150, 150, true ); // Default Thumb
	add_image_size( 'large-image', 9999, 9999, false );

	// Add support for Post Formats
	add_theme_support( 'post-formats', array( 'quote', 'status', 'gallery' ) );

	// Custom Background Support
	add_theme_support( 'custom-background' );

	// Gallery support
	add_theme_support( 'array_themes_gallery_support' );

	// Add support for legacy widgets
	add_theme_support( 'array_toolkit_legacy_widgets' );

	// Register nav menus
	register_nav_menus( array(
		'main'   => __( 'Main Menu', 'fixed' )
	) );

	// Make theme available for translation
	load_theme_textdomain( 'fixed', get_template_directory() . '/languages' );

	// Add Google fonts to editor styles
	add_editor_style( array( 'editor-style.css', fixed_fonts_url() ) );

}
endif; // fixed_setup
add_action( 'after_setup_theme', 'fixed_setup' );


/* Load Scripts and Styles */
function fixed_scripts_styles() {

	// Get theme version
	$version = wp_get_theme()->Version;

	//Fixed Stylesheet
	wp_enqueue_style( 'fixed-style', get_stylesheet_uri() );

	//Font Awesome CSS
	wp_enqueue_style( 'font-awesome-css', get_template_directory_uri() . '/includes/fonts/fontawesome/font-awesome.min.css', array(), '4.0.3', 'screen' );

	//Media Queries CSS
	wp_enqueue_style( 'fixed-media-queries-css', get_template_directory_uri() . '/media-queries.css', array(), $version, 'screen' );

	//Flexslider
	wp_enqueue_style( 'fixed-flexslider-css', get_template_directory_uri() . '/includes/styles/flexslider.css', array(), '2.1', 'screen' );

	//Load fonts from Google
	wp_enqueue_style( 'fixed-fonts', fixed_fonts_url(), array(), null );

	//Custom JS
	wp_enqueue_script( 'custom-js', get_template_directory_uri() . '/includes/js/custom/custom.js', array( 'jquery' ), $version, true );
	wp_localize_script( 'custom-js', 'custom_js_vars', array(
			'infinite_scroll'       => get_option( 'fixed_theme_customizer_infinite' ),
			'infinite_scroll_image' => get_template_directory_uri()
		)
	);

	//HoverIntent JS
	wp_enqueue_script( 'hoverIntent' );

	//FitVid
	wp_enqueue_script( 'fixed-fitvid-js', get_template_directory_uri() . '/includes/js/fitvid/jquery.fitvids.js', array( 'jquery' ), '1.0.3', true );

	//Flexslider
	wp_enqueue_script( 'fixed-flexslider-js', get_template_directory_uri() . '/includes/js/flexslider/jquery.flexslider-min.js', array( 'jquery' ), '2.1', true );

	//View.js
	wp_enqueue_script( 'fixed-view-js', get_template_directory_uri() . '/includes/js/view/view.min.js?auto', array( 'jquery' ), '1.02', true );

	//Infinite Scroll
	if ( 'disabled' != get_option( 'fixed_theme_customizer_infinite' ) ) {
		wp_enqueue_script( 'infinite-js', get_template_directory_uri() . '/includes/js/infinitescroll/jquery.infinitescroll.min.js', array( 'jquery' ), '2.0', true );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

}
add_action( 'wp_enqueue_scripts', 'fixed_scripts_styles' );

/**
 * Return the Google font stylesheet URL
 */
function fixed_fonts_url() {

	$fonts_url = '';

	/* Translators: If there are characters in your language that are not
	 * supported by Open Sans, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$opensans = _x( 'on', 'Open Sans font: on or off', 'fixed' );

	/* Translators: If there are characters in your language that are not
	 * supported by Abel, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$abel = _x( 'on', 'Abel font: on or off', 'fixed' );

	if ( 'off' !== $opensans || 'off' !== $abel ) {
		$font_families = array();

		if ( 'off' !== $opensans )
			$font_families[] = 'Open+Sans:400italic,700italic,400,700';

		if ( 'off' !== $abel )
			$font_families[] = 'Abel';

		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);
		$fonts_url = add_query_arg( $query_args, "//fonts.googleapis.com/css" );
	}

	return $fonts_url;
}

/**
 * Enqueue Google fonts style to admin for editor styles
 */
function fixed_admin_fonts( $hook_suffix ) {
	wp_enqueue_style( 'fixed-fonts', fixed_fonts_url(), array(), null );
}
add_action( 'admin_enqueue_scripts', 'fixed_admin_fonts' );

/**
 * Adds Customizer CSS to Header
 */
function fixed_customizer_css() {
	?>
	<style type="text/css">
		a {
			color: <?php echo get_theme_mod( 'fixed_theme_customizer_accent', '#999' ); ?>;
		}

		<?php echo get_theme_mod( 'fixed_theme_customizer_css', '' ); ?>
	</style>
	<?php
}
add_action( 'wp_head', 'fixed_customizer_css' );

/**
 * Deprecated page navigation
 *
 * @deprecated 3.0 Replaced by fixed_page_nav()
 */
function fixed_page_has_nav() {

        _deprecated_function( __FUNCTION__, '3.0', 'fixed_page_nav()' );
        return false;
}

/**
 * Displays post pagination links
 *
 * @since 3.0
 */
function fixed_page_nav() {
	// Return early if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	} ?>

	<div class="post-nav <?php if ( get_option( 'fixed_theme_customizer_infinite' ) == 'enabled' ) { echo 'infinite'; } ?>">
		<div class="post-nav-inside">
			<div class="post-nav-left"><?php previous_posts_link( __( '<i class="fa fa-arrow-left"></i> Newer Posts', 'fixed' ) ) ?></div>
			<div class="post-nav-right"><?php next_posts_link( __( 'Older Posts <i class="fa fa-arrow-right"></i>', 'fixed' ) ) ?></div>
		</div>
	</div>
	<?php
}

/**
 * Remove admin bar CSS in favor of our own
 */
function fixed_remove_adminbar_css() {
	remove_action( 'wp_head', '_admin_bar_bump_cb' );
}
add_action( 'get_header', 'fixed_remove_adminbar_css' );

/**
 * Retreive featured author posts
 */
function fixed_author_posts() {
	global $post, $authordata;

	// Grab the featured author from the customizer
	$featured_author = get_theme_mod( 'fixed_theme_customizer_user' );

	$authors_posts = get_posts( array( 'author' => $featured_author, 'showposts' => 5 ) );

	$output = '<ul>';
		foreach ( $authors_posts as $post ) {
			setup_postdata( $post );
			$output .= '<li><span>' . get_the_date() . '</span><a href="' . esc_url( get_permalink() ) . '">' . get_the_title() . '</a></li>';
		}
		wp_reset_postdata();
	$output .= '</ul>';

	return $output;
}

/**
 * Registers Widget Areas
 *
 * @since 1.0
 */
function fixed_register_sidebars() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'fixed' ),
		'id'            => 'sidebar',
		'description'   => __( 'Widgets in this area will be shown in the sidebar.', 'fixed' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>'
	));
}
add_action( 'widgets_init', 'fixed_register_sidebars' );

/**
 * Custom Comment Output
 */
function fixed_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class('clearfix'); ?> id="li-comment-<?php comment_ID() ?>">

		<div class="comment-block" id="comment-<?php comment_ID(); ?>">
			<div class="comment-info">
				<div class="comment-author vcard clearfix">
					<?php echo get_avatar( $comment->comment_author_email, 75 ); ?>

					<div class="comment-meta commentmetadata">
						<?php printf(__('<cite class="fn">%s</cite>', 'fixed'), get_comment_author_link()) ?>
						<div style="clear:both;"></div>
						<a class="comment-time" href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf(__('%1$s - %2$s', 'fixed'), get_comment_date('m/d/Y'),  get_comment_time()) ?></a><?php edit_comment_link(__('(Edit)', 'fixed'),'  ','') ?>
					</div>
				</div>
			<div class="clearfix"></div>
			</div>

			<div class="comment-text">
				<?php comment_text() ?>
				<p class="reply">
					<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ) ?>
				</p>
			</div>

			<?php if ( $comment->comment_approved == '0' ) : ?>
				<em class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.', 'fixed') ?></em>
			<?php endif; ?>
		</div>
<?php
}

function fixed_cancel_comment_reply_button( $html, $link, $text ) {
	$style = isset($_GET['replytocom']) ? '' : ' style="display:none;"';
	$button = '<div id="cancel-comment-reply-link"' . $style . '>';
	return $button . '<i class="fa fa-times-circle"></i> </div>';
}
add_action( 'cancel_comment_reply_link', 'fixed_cancel_comment_reply_button', 10, 3 );

/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string The filtered title.
 */
function fixed_wp_title( $title, $sep ) {
		if ( is_feed() ) {
				return $title;
		}

		global $page, $paged;

		// Add the blog name
		$title .= get_bloginfo( 'name', 'display' );

		// Add the blog description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) ) {
				$title .= " $sep $site_description";
		}

		// Add a page number if necessary:
		if ( $paged >= 2 || $page >= 2 ) {
				$title .= " $sep " . sprintf( __( 'Page %s', 'fixed' ), max( $paged, $page ) );
		}

		return $title;
}
add_filter( 'wp_title', 'fixed_wp_title', 10, 2 );
