<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package _s
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<?php _s_custom_code('_s_after_opening_head'); ?>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<?php wp_head(); ?>
	<?php _s_custom_code('_s_before_closing_head'); ?>
</head>

<body <?php body_class(); ?>>
<?php _s_custom_code('_s_after_opening_body'); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', '_s' ); ?></a>

    <header role="banner" id="masthead" class="site-header nav-wrap">
        <div class="container">
            <nav id="site-navigation" class="main-navigation navbar navbar-expand-lg navbar-light" role="navigation">
                <div class="site-branding navbar-brand">
					<?php the_custom_logo(); ?>
                </div>
                <span class="visually-hidden"><?php esc_html_e( 'Toggle navigation', '_s' ); ?></span>
                <button class="navbar-toggler brand" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-collapse-primary" aria-controls="navbar-collapse-primary" aria-expanded="false" aria-label="<?php esc_html_e( 'Toggle navigation', '_s' ); ?>">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div id="navbar-collapse-primary" class="collapse navbar-collapse">
					<?php wp_nav_menu( array(
						'theme_location' => 'menu-1',
						'menu_id' => 'primary-menu',
						'container' => null,
						'menu_class' => 'navbar-nav ms-auto',
						'depth' => 2,
						'fallback_cb' => 'wp_bootstrap_navwalker::fallback',
						'walker' => new wp_bootstrap_navwalker() ) );?>
                </div>
            </nav>
        </div>
    </header>

        <?php if ( function_exists( 'yoast_breadcrumb' ) ) { ?>
            <div class="container">
                <?php yoast_breadcrumb( '<div id="breadcrumbs" class="breadcrumbs">', '</div>' ); ?>
            </div>
        <?php } ?>

	<div id="content" class="site-content">
