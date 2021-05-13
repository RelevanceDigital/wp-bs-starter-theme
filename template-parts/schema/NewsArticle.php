<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "NewsArticle",
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "<?php echo get_the_permalink(); ?>"
  },
  "headline": "<?php echo get_the_title(); ?>",
  <?php if(has_post_thumbnail()) :
		$thumb_arr = wp_prepare_attachment_for_js( get_post_thumbnail_id()) ;?>
  "image": {
    "@type": "ImageObject",
    "url": "<?php echo $thumb_arr['url']; ?>",
    "height": "<?php echo $thumb_arr['height']; ?>",
    "width": "<?php echo $thumb_arr['width']; ?>"
  },
  <?php endif; ?>
  "datePublished": "<?php echo get_the_date( 'c' ); ?>",
  "dateModified": "<?php echo get_the_modified_date( 'c' ); ?>",
  "author": {
    "@type": "Person",
    "name": "<?php echo esc_html( get_the_author() ); ?>"
  },
  <?php /*
   "publisher": {
    "@type": "Organization",
    "name": "Google",
    "logo": {
      "@type": "ImageObject",
      "url": "https://google.com/logo.jpg",
      "width": "600",
      "height": "60"
    }
}, */ ?>
  "description": "<?php echo get_the_excerpt(); ?>"
}
</script>
