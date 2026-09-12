<?php
/**
 * Elvina theme setup.
 */

defined( 'ABSPATH' ) || exit;

define( 'ELVINA_VERSION', '1.1.0' );

function elvina_setup() {
	load_theme_textdomain( 'elvina', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);

	// WooCommerce integration (see https://developer.woocommerce.com/docs/theming/theme-development/theme-support/).
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus(
		array(
			'primary' => __( 'Menu principal', 'elvina' ),
		)
	);
}
add_action( 'after_setup_theme', 'elvina_setup' );

function elvina_scripts() {
	wp_enqueue_style(
		'elvina-fonts',
		'https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400;0,6..96,500;0,6..96,600;0,6..96,700;1,6..96,400&family=Jost:ital,wght@0,400;0,500;0,600;1,400&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'elvina-style', get_stylesheet_uri(), array( 'elvina-fonts' ), ELVINA_VERSION );
	wp_enqueue_script( 'elvina-nav', get_template_directory_uri() . '/assets/nav.js', array(), ELVINA_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'elvina_scripts' );

require get_template_directory() . '/inc/woocommerce.php';
require get_template_directory() . '/inc/contact-form.php';
require get_template_directory() . '/inc/pos.php';
