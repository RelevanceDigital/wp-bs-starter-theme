<?php
/**
 * Adds Gutenberg content blocks through Advance Custom Fields Plugin
 *
 * See the full list of parameters at: https://www.advancedcustomfields.com/resources/acf_register_block_type/
 *
 * @package _s
 */

/**
 * Custom block category
 */
/*
function _s_block_categories( $categories ) {
	return array_merge(
		array(
			array(
				'slug'  => '_s',
				'title' => __( '_s', '_s' ),
			),
		),
		$categories
	);
}
add_filter( 'block_categories', '_s_block_categories', 10, 2 );
*/

function _s_register_acf_block_types() {

	// Text widths
	/*
	acf_register_block_type( array(
		'name'            => 'text_custom_width',
		'title'           => __( 'Text Custom Width', '_s' ),
		'description'     => __( 'Adjustable Width Text.', '_s' ),
		'render_template' => 'template-parts/blocks/text-custom-width.php',
		'category'        => '_s',
		'icon'            => 'editor-paragraph',
		'keywords'        => array( 'text' ),
	) );
	*/

}

// Check if function exists and hook into setup.
if ( function_exists( 'acf_register_block_type' ) ) {
	add_action( 'acf/init', '_s_register_acf_block_types' );
}
