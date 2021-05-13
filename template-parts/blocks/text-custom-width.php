<?php
/**
 * This is an example block output.
 * First, register the block in /inc/acf-blocks.php
 * Register your fields in ACF and assign a field group to the block.
 * Then add the output here as usual.
 *
 * $post_id can be used for the ID of the current post.
 * $block['id'] can be used as a unique identifier for a block instance for ID/JavaScript.
 */
$block_width = get_field( '_s_block_width' );
$content     = get_field( '_s_content' );

if ( $content ) : ?>

    <div class="row block-text-custom-width justify-content-center">
        <div class="col-md-<?php echo $block_width; ?>">
			<?php echo $content; ?>
        </div>
    </div>

<?php endif;