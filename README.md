A Bootstrap 5 WordPress Theme Designed for Use With ACF Blocks
===

A basic Bootstrap 5 Wordpress theme based on [underscores](https://underscores.me/).  
Intended for use with the WordPress block editor (Gutenberg) and blocks created with [Advance Custom FIelds](https://www.advancedcustomfields.com/)

- Underscores layouts are bootstrapped.
- Core output is filtered to use bootstrap classes (Comment/search form etc).
- Nav walker for bootstrapped menu included.
- Simple nav walker (For single level menus without excessive markup).
- Comment walker for bootstrapped comments included.
- Uses the Customizer for theme options.
- Includes example file for a Gutenberg block created with Advanced Custom Fields.
- Includes gulp file to watch/compile/compress scss, js and images.
- Uses [browserSync](https://browsersync.io/) to reload browsers after file changes.
- Includes [lazysizes](https://github.com/aFarkas/lazysizes) for lazyloading srcset images (object-fit plugin enabled as an example).

## Requirements

 - node/npm
 - Gulp-cli installed globally: `npm install --global gulp-cli`
 

Getting Started
---------------

**Installation**

1. Edit `gulpfile.js` and update the browserSync proxy location to your localhost domain.
2. Run `npm install` to install dependencies.
3. Run `gulp install` to copy library scss files into the main scss folder and rename theme strings.
4. Run `gulp` to generate compiled assets and watch folders.

Note: After running `gulp install` you will be prompted for a theme name. This should be short as it is used for function prefixes and language strings.  
Any capitals will be converted to lowercase and any spaces replaced with a dash.

**Enable Errors**

Whilst in development, it's best practice to enable error messages so that issues can be fixed as they arise.

Edit the wp-config.php file in the root of your site and modify the debug lines to:


    //define('WP_DEBUG', false);
    
    define('WP_DEBUG', true);
    define('WP_DEBUG_LOG', true);
    define('WP_DEBUG_DISPLAY', true);
    @ini_set('display_errors', 1);


## Styles

Running `gulp install` copies the main bootstrap.scss file from `node_modules` into the scss folder. It replaces the paths to imported files and imports `_variables.scss` to override default variables.

The original variables file is also copied into the `scss` folder as `_variables-reference.scss`.

`style.scss` imports `bootstrap.scss` and then /custom/_custom.scss. **_custom.scss is the file you should use to include your own scss files as it is also imported by the editor stylesheet to work within the admin area.**

When running `gulp`, any changes to files in the `scss` folder will regenerate the compiled css files in `assets/css` (compressed) and `css` (nested).

To add your own scss files, add them to the `scss` folder without a `_` prefix.

All styles are enqueued in `functions.php`.

## JavaScript

Adding or editing files in the `js` folder whilst running `gulp` will compile them to the `assets/js` folder.

Bootstrap JavaScript is loaded from a cdn.

All scripts are enqueued in `functions.php`.

## Images

Any images added to the `images` folder whilst running `gulp` are automatically compressed and added to the `assets/images` folder.

2 compression types are available. If the default `imagescompress` reduces the quality of your images you can use `imagesreduced` by changing the `watch` task in `gulpfile.js`.

## Custom Functions

Custom functions are added in `inc/template-functions.php`.  
Functions that product frontend output are added to `template-tags.php`.

These files contain modified versions of Underscores functions as well as new ones:

- Custom lazy sizes function to generate lazyloaded srcset tags which don't include the default square thumbnail images.
- Overrides for comment form styling.
- Various filters and actions to remove some standard output from the head of the page.
- Overrides for some common plugins to clean up their output.

### Using The Lazy Image Function

`<?php echo _s_lazy_image( $img, $default = null, $classes = null, $fit = null, $disable_lazy = false ); ?>`

$img - Either the ID of an attachment or an image array from ACF or produced with the function `wp_prepare_attachment_for_js()`.  
$default - The default image size to use (Optional).  
$classes - Additional css classes.  
$fit - Use either `'cover'` or `'contain'` (Requires the lazysizes object-fit plugin enabled by default), or leave blank for a standard image.
$disable_lazy - can be set to true to product a srcset img tag that isn't lazyloaded (For anything above the fold).

## ACF Blocks

Advanced Custom Fields blocks are registered in /inc/acf-blocks.php  
This file contains a basic example to register a block and add it to a custom block category.  
There is a link at the top of the file to the documentation for all options available.

The basic process to add a new block is:

1. Register a new block in acf-blocks.php
2. Create a new set of custom fields and assign them to the block.
3. The `render_template` file location added when registering the block is the file that creates the output. Use PHP and HTML to create the output.

Notes: 

- In the admin editor, images using the lazy load function have lazyloading automatically disabled so they can be viewed in the editor.
- If you need a unique identifier for any JavaScript, use `$block['id']` to get the ID of the block.
- To cut down on database queries, use `get_fields()`  to get an array of all fields rather than calling each one individually with `get_field( 'field_name' )`
- The main content ares **must** be wrapped in a `container` for standard blocks to not be the full width of the screen.
- To make a block full-width and break out of the main container, give it the class `alignfull` (From /scss/custom/_gutenberg.scss)
- If you use `alignfull` then you can remove the default alignment options for the block with the supports attribute:

```
'supports' => array(
    'align' => false,
),
```

## Contributing

Any issues and pull requests for bugs are welcome.
