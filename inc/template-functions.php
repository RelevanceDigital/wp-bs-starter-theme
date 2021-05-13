<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package _s
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 *
 * @return array
 *
 * function _s_body_classes( $classes ) {
 * // Adds a class of hfeed to non-singular pages.
 * if ( ! is_singular() ) {
 * $classes[] = 'hfeed';
 * }
 *
 * return $classes;
 * }
 * add_filter( 'body_class', '_s_body_classes' );
 */

/**
 * Remove hentry class
 */
function _s_remove_hentry( $classes ) {
	$classes = array_diff( $classes, array( 'hentry' ) );

	return $classes;
}

add_filter( 'post_class', '_s_remove_hentry' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function _s_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}

add_action( 'wp_head', '_s_pingback_header' );

/**
 * Change the default text after an excerpt
 */
function _s_excerpt_more( $more ) {
	return '...';
}

add_filter( 'excerpt_more', '_s_excerpt_more' );

/**
 * Limit the excerpt length
 */
function _s_excerpt_length( $length ) {
	return 25;
}

add_filter( 'excerpt_length', '_s_excerpt_length' );

/**
 * Checks if a menu exists and has items
 *
 * @param false $menu
 *
 * @return bool
 */
function _s_menu_has_items( $menu = false ) {
	if ( ! $menu ) {
		return false;
	}

	$menu_locations = get_nav_menu_locations();

	if ( ! array_key_exists( $menu, $menu_locations ) ) {
		return false;
	}

	$menu_items = wp_get_nav_menu_items( $menu_locations[ $menu ] );

	return ( empty( $menu_items ) ) ? false : true;
}

/**
 * Function to convert img tags to make them lazyload
 */
function _s_replace_image_lazy( $content ) {

	if ( ! $content ) {
		return '';
	}

	// Start the dom object
	$dom                     = new DOMDocument();
	$dom->recover            = true;
	$dom->substituteEntities = true;

	// Feed the content to the dom object
	@$dom->loadHTML( mb_convert_encoding( wpautop($content), 'HTML-ENTITIES', 'UTF-8' ) );

	foreach ( $dom->getElementsByTagName( 'img' ) as $img ) {

		$src   = $img->getAttribute( 'src' );
		$class = $img->getAttribute( 'class' );
		$class = $class . ' lazyload';

        // Add any missing alts
        if (!$img->getAttribute( 'alt' )){
            $img->setAttribute( 'alt', '' );
        }

		// Swap them
		$img->removeAttribute( 'src' );
		$img->setAttribute( 'data-src', $src );
		$img->setAttribute( 'class', $class );
	}

	return $dom->saveHTML();
}

/**
 * Add custom image sizes to the returned image array
 * https://wordpress.stackexchange.com/questions/110060/retrieve-custom-image-sizes-from-media-uploader-javascript-object
 *
 * @param $response
 * @param $attachment
 * @param $meta
 *
 * @return mixed
 */
function _s_image_sizes_js( $response, $attachment, $meta ) {

	$size_array = array( 'medium_large' );

	foreach ( $size_array as $size ):

		if ( isset( $meta['sizes'][ $size ] ) ) {
			$attachment_url = wp_get_attachment_url( $attachment->ID );
			$base_url       = str_replace( wp_basename( $attachment_url ), '', $attachment_url );
			$size_meta      = $meta['sizes'][ $size ];

			$response['sizes'][ $size ] = array(
				'height'      => $size_meta['height'],
				'width'       => $size_meta['width'],
				'url'         => $base_url . $size_meta['file'],
				'orientation' => $size_meta['height'] > $size_meta['width'] ? 'portrait' : 'landscape',
			);
		}

	endforeach;

	return $response;
}

add_filter( 'wp_prepare_attachment_for_js', '_s_image_sizes_js', 10, 3 );

/**
 * Bootstrap comment form
 */
function _s_comment_form( $args ) {
	$args['comment_field'] = '<div class="mb-3 comment-form-comment">
  <label for="comment">' . _x( 'Comment', 'noun' ) . '</label>
  <textarea class="form-control" id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea>
  </div>';
	$args['class_submit']  = 'btn btn-primary'; // since WP 4.1

	return $args;
}

add_filter( 'comment_form_defaults', '_s_comment_form' );

function _s_comment_form_fields( $fields ) {

	$commenter = wp_get_current_commenter();
	$req      = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );
	$html5    = current_theme_supports( 'html5', 'comment-form' ) ? 1 : 0;
	$consent  = empty( $commenter['comment_author_email'] ) ? '' : ' checked="checked"';

	$fields['author'] = '<div class="mb-3 comment-form-author">' . '<label for="author">' . __( 'Name' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
	                    '<input class="form-control" id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></div>';
	$fields['email']  = '<div class="mb-3 comment-form-email"><label for="email">' . __( 'Email' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
	                    '<input class="form-control" id="email" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></div>';
	$fields['url']    = '<div class="mb-3 comment-form-url"><label for="url">' . __( 'Website' ) . '</label> ' .
	                    '<input class="form-control" id="url" name="url" ' . ( $html5 ? 'type="url"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></div>';

	$fields['cookies'] = '<div class=" mb-3 comment-form-cookies-consent form-check"><input id="wp-comment-cookies-consent" class="form-check-input" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . $consent . ' />' .
	                     '<label class="form-check-label" for="wp-comment-cookies-consent">' . __( 'Save my name, email, and website in this browser for the next time I comment.' ) . '</label></div>';

	return $fields;
}

add_filter( 'comment_form_default_fields', '_s_comment_form_fields' );

/**
 * Tiny MCE Editor changes
 */
/*
function _s_mce_buttons( $buttons ) {
	array_unshift( $buttons, 'fontselect' ); // Add Font Select
	array_unshift( $buttons, 'fontsizeselect' ); // Add Font Size Select

	return $buttons;
}
add_filter( 'mce_buttons_2', '_s_mce_buttons' );

function _s_mce( $settings ) {
	$settings['fontsize_formats'] = ".8rem .875rem 1rem 1.25rem 1.5rem 1.75rem 2rem 3.5rem 4.5rem 5.5rem 6rem";
	$settings['font_formats'] = 'Lato=Lato,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,sans-serif;Libre Baskerville=Libre Baskerville,Georgia,Times New Roman,Times,serif;Monospace=SFMono-Regular,Menlo,Monaco,Consolas,Liberation Mono,Courier New,monospace;';

	return $settings;
}
add_filter( 'tiny_mce_before_init', '_s_mce' );
*/

/**
 * Stuff to remove default code
 */

//Remove amp fonts
add_action( 'amp_post_template_head', function () {
	remove_action( 'amp_post_template_head', 'amp_post_template_add_fonts' );
}, 9 );

//Remove the generator tag
remove_action( 'wp_head', 'wp_generator' );

//Remove the frontend admin bar while in development
//add_filter('show_admin_bar', '__return_false');

//Remove shortlinks from head
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );

//remove manifest link
//http://wpsmackdown.com/wordpress-cleanup-wp-head/
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );

//Remove emoji frontend files added in 4.2
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
//Remove s.w.org prefetch link
add_filter( 'emoji_svg_url', '__return_false' );

//Disable the json api and remove the head link
//add_filter('rest_enabled', '__return_false');
//add_filter( 'rest_jsonp_enabled', '__return_false' );
//remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );

//oEmbed stuff
//Remove the REST API endpoint.
//remove_action( 'rest_api_init', 'wp_oembed_register_route' );
//Turn off oEmbed auto discovery.
//Don't filter oEmbed results.
//remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
//Remove oEmbed discovery links.
//remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
//Remove oEmbed-specific JavaScript from the front-end and back-end.
//remove_action( 'wp_head', 'wp_oembed_add_host_js' );

/**
 * Third party extensions that do annoying things
 */

//Remove All Yoast HTML Comments
//https://gist.github.com/paulcollett/4c81c4f6eb85334ba076
add_filter( 'wpseo_debug_markers', '__return_false' );

//Move the yoast seo stuff to the bottom of the admin pages
function _s_yoast_to_bottom() {
	return 'low';
}
add_filter( 'wpseo_metabox_prio', '_s_yoast_to_bottom' );

//cf7
//add_filter( 'wpcf7_load_js', '__return_false' );
//add_filter( 'wpcf7_load_css', '__return_false' );


//Jetpack
// First, make sure Jetpack doesn't concatenate all its CSS
/*
add_filter( 'jetpack_implode_frontend_css', '__return_false' );
*/
// Then, remove each CSS file, one at a time
/*
function jeherve_remove_all_jp_css() {
	wp_deregister_style( 'AtD_style' ); // After the Deadline
	wp_deregister_style( 'jetpack_likes' ); // Likes
	wp_deregister_style( 'jetpack_related-posts' ); //Related Posts
	wp_deregister_style( 'jetpack-carousel' ); // Carousel
	wp_deregister_style( 'grunion.css' ); // Grunion contact form
	wp_deregister_style( 'the-neverending-homepage' ); // Infinite Scroll
	wp_deregister_style( 'infinity-twentyten' ); // Infinite Scroll - Twentyten Theme
	wp_deregister_style( 'infinity-twentyeleven' ); // Infinite Scroll - Twentyeleven Theme
	wp_deregister_style( 'infinity-twentytwelve' ); // Infinite Scroll - Twentytwelve Theme
	wp_deregister_style( 'noticons' ); // Notes
	wp_deregister_style( 'post-by-email' ); // Post by Email
	wp_deregister_style( 'publicize' ); // Publicize
	wp_deregister_style( 'sharedaddy' ); // Sharedaddy
	wp_deregister_style( 'sharing' ); // Sharedaddy Sharing
	wp_deregister_style( 'stats_reports_css' ); // Stats
	wp_deregister_style( 'jetpack-widgets' ); // Widgets
	wp_deregister_style( 'jetpack-slideshow' ); // Slideshows
	wp_deregister_style( 'presentations' ); // Presentation shortcode
	wp_deregister_style( 'jetpack-subscriptions' ); // Subscriptions
	wp_deregister_style( 'tiled-gallery' ); // Tiled Galleries
	wp_deregister_style( 'widget-conditions' ); // Widget Visibility
	wp_deregister_style( 'jetpack_display_posts_widget' ); // Display Posts Widget
	wp_deregister_style( 'gravatar-profile-widget' ); // Gravatar Widget
	wp_deregister_style( 'widget-grid-and-list' ); // Top Posts widget
	wp_deregister_style( 'jetpack-widgets' ); // Widgets
}
add_action('wp_print_styles', 'jeherve_remove_all_jp_css' );
*/
