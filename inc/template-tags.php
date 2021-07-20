<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package _s
 */

if ( ! function_exists( '_s_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 */
	function _s_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s" itemprop="datePublished">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published updated" datetime="%1$s" itemprop="datePublished">%2$s</time><time class="entry-date modified screen-reader-text" datetime="%3$s" itemprop="dateModified">%4$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = sprintf(
			/* translators: %s: post date. */
			esc_html_x( 'Posted on %s', 'post date', '_s' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);

		echo '<span class="posted-on">' . $posted_on . '</span>'; // WPCS: XSS OK.

	}
endif;

if ( ! function_exists( '_s_posted_by' ) ) :
	/**
	 * Prints HTML with meta information for the current author.
	 */
	function _s_posted_by() {
		$byline = sprintf(
			/* translators: %s: post author. */
			esc_html_x( 'by %s', 'post author', '_s' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);

		echo '<span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.

	}
endif;

if ( ! function_exists( '_s_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function _s_entry_footer() {
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( esc_html__( ', ', '_s' ) );
			if ( $categories_list ) {
				/* translators: 1: list of categories. */
				printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', '_s' ) . '</span>', $categories_list ); // WPCS: XSS OK.
			}

			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', '_s' ) );
			if ( $tags_list ) {
				/* translators: 1: list of tags. */
				printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', '_s' ) . '</span>', $tags_list ); // WPCS: XSS OK.
			}
		}

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			comments_popup_link(
				sprintf(
					wp_kses(
						/* translators: %s: post title */
						__( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', '_s' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					get_the_title()
				)
			);
			echo '</span>';
		}

	}
endif;

if ( ! function_exists( '_s_post_thumbnail' ) ) :
	/**
	 * Displays an optional post thumbnail.
	 *
	 * Wraps the post thumbnail in an anchor element on index views, or a div
	 * element when on single views.
	 */
	function _s_post_thumbnail() {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}

		$img_id = get_post_thumbnail_id();

		if ( is_singular() ) :
			?>

            <div class="entry-image">
                <div class="imagecontainer">
						<?php echo _s_lazy_image($img_id, 'medium_large', 'img-fluid'); ?>
                </div>
            </div>

		<?php else : ?>

            <div class="entry-image">
                <div class="imagecontainer">
                    <a class="post-thumbnail" href="<?php the_permalink() ?>">
						<?php echo _s_lazy_image($img_id, 'medium_large', 'img-fluid'); ?>
                    </a>
                </div>
            </div>

		<?php
		endif; // End is_singular().
	}
endif;

if ( ! function_exists( '_s_pagination_links' ) ) :
/**
 * Numbered pagination
 */
function _s_pagination_links() {
	global $wp_query;

	$total_pages = $wp_query->max_num_pages;

	if ($total_pages > 1){
		$current_page = max(1, get_query_var('paged'));

		echo paginate_links(array(
			'base' => get_pagenum_link(1) . '%_%',
			'format' => 'page/%#%',
			'type'      => 'list',
			'current' => $current_page,
			'total' => $total_pages,
		));
	}
}
endif;

if ( ! function_exists( '_s_lazy_image' ) ) :
	/**
	 * Return a responsive image tag without the cropped images from a wp image array
	 */
	function _s_lazy_image( $img_arr, $default = null, $classes = null, $fit = null, $disable_lazy = false ) {

		if ( is_int( $img_arr ) ) {
			$img_arr = wp_prepare_attachment_for_js( $img_arr );
		}

		if ( ! is_array( $img_arr ) ) {
			return '';
		}
		//Get a list of available image sizes
		$sizes = get_intermediate_image_sizes();
		//Remove thumbnail and medium which are always first
		unset( $sizes[0], $sizes[1] );

		if ( is_admin() || $disable_lazy ) {
			$src    = 'src="';
			$srcset = 'srcset="';
		} else {
			$src    = 'data-src="';
			$srcset = 'data-srcset="';
		}

		$tag = '<img ';
		if ( isset( $default ) && isset( $img_arr['sizes'][ $default . '-width' ] ) ) {
			$tag .= $src . $img_arr['sizes'][ $default ] . '" ' . "\n";
		} elseif ( isset( $default ) && isset( $img_arr['sizes'][ $default ]['url'] ) ) {
			$tag .= $src . $img_arr['sizes'][ $default ]['url'] . '" ' . "\n";
		} else {
			$tag .= $src . $img_arr['url'] . '" ' . "\n";
		}

		if ( ! is_admin() && ! $disable_lazy ) {
			//Add a blank image on pageload
			$tag .= 'srcset="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" ' . "\n";
		}

		//Now loop through the available sizes and add them with their widths, default first
		if ( isset( $default ) && isset( $img_arr['sizes'][ $default . '-width' ] ) ) {
			$tag .= $srcset . $img_arr['sizes'][ $default ] . ' ' . $img_arr['sizes'][ $default . '-width' ] . 'w ' . $img_arr['sizes'][ $default . '-height' ] . 'h,' . "\n";
		} elseif ( isset( $default ) && isset( $img_arr['sizes'][ $default ]['width'] ) ) {
			$tag .= $srcset . $img_arr['sizes'][ $default ]['url'] . ' ' . $img_arr['sizes'][ $default ]['width'] . 'w ' . $img_arr['sizes'][ $default ]['height'] . 'h,' . "\n";
		} else {
			$tag .= $srcset . $img_arr['url'] . ' ' . $img_arr['width'] . 'w ' . $img_arr['height'] . 'h, ' . "\n";
		}
		foreach ( $sizes as $key => $size ) {
			//We only want to add a size if it's smaller than the original image
			if ( isset( $img_arr['sizes'][ $size . '-width' ] ) && $img_arr['sizes'][ $size . '-width' ] < $img_arr['width'] ) {
				$tag .= $img_arr['sizes'][ $size ] . ' ' . $img_arr['sizes'][ $size . '-width' ] . 'w, ' . "\n";
			} elseif ( isset( $img_arr['sizes'][ $size ]['width'] ) && $img_arr['sizes'][ $size ]['width'] < $img_arr['width'] ) {
				$tag .= $img_arr['sizes'][ $size ]['url'] . ' ' . $img_arr['sizes'][ $size ]['width'] . 'w, ' . "\n";
			}
		}
		//Trim off the last comma and close the quote
		$tag = rtrim( $tag, ",\n " ) . '" ' . "\n";
		//We want the plugin in auto mode so will hardcode this bit
		$tag .= 'data-sizes="auto" ' . "\n";
		//If object-fit is set we need a data att to support ie
		if ( isset( $fit ) && ( $fit === 'cover' || $fit === 'contain' ) ) {
			$tag     .= 'data-parent-fit="' . $fit . '"' . "\n";
			$classes = $classes . ' imagecontainer-img-' . $fit;
		}
		//Add the classes
		$tag .= $classes ? 'class="lazyload ' . $classes . '"' . "\n" : 'class="lazyload"' . "\n";
		//Add the alt
		$tag .= 'alt="' . $img_arr['alt'] . '"' . "\n";
		//Close the tag
		$tag .= ' />';

		return $tag;

	}
endif;

if ( ! function_exists( '_s_custom_code' ) ) :
	/**
	 * Echo custom code from the customizer
	 */
	function _s_custom_code( $location = false ) {

		if ( $location === false ) {
			return false;
		}

		$code = get_theme_mod( $location );

		if ( $code ) {
			echo $code;
		}
	}
endif;

if ( ! function_exists( '_s_copyright' ) ) :
	/**
	 * Echo copyright from the customizer
	 */
	function _s_copyright() {

		$copyright = get_theme_mod('_s_copyright');

		if ( $copyright ) {
			echo '<p class="mb-md-0">' . str_replace( '{year}', date( 'Y' ), $copyright ) . '</p>';
		}
	}
endif;
