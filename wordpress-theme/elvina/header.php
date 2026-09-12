<?php
/**
 * Site header, up to and including the opening of the page content area.
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/svg+xml" href="<?php echo esc_url( get_template_directory_uri() . '/assets/favicon.svg' ); ?>">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
	<div class="header-inner">
		<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
		<nav class="main-nav" id="main-nav">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"<?php echo is_front_page() ? ' class="is-active"' : ''; ?>><?php esc_html_e( 'Accueil', 'elvina' ); ?></a>
			<?php if ( function_exists( 'wc_get_page_id' ) ) : ?>
				<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>"<?php echo ( is_shop() || is_product_taxonomy() || is_product() ) ? ' class="is-active"' : ''; ?>><?php esc_html_e( 'Boutique', 'elvina' ); ?></a>
			<?php endif; ?>
			<?php $elvina_about = get_page_by_path( 'a-propos' ); ?>
			<?php if ( $elvina_about ) : ?>
				<a href="<?php echo esc_url( get_permalink( $elvina_about ) ); ?>"<?php echo is_page( 'a-propos' ) ? ' class="is-active"' : ''; ?>><?php esc_html_e( 'À propos', 'elvina' ); ?></a>
			<?php endif; ?>
			<?php $elvina_contact = get_page_by_path( 'contact' ); ?>
			<?php if ( $elvina_contact ) : ?>
				<a href="<?php echo esc_url( get_permalink( $elvina_contact ) ); ?>"<?php echo is_page( 'contact' ) ? ' class="is-active"' : ''; ?>><?php esc_html_e( 'Contact', 'elvina' ); ?></a>
			<?php endif; ?>
		</nav>
		<div class="header-actions">
			<?php if ( function_exists( 'WC' ) ) : ?>
				<a class="cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
					<?php esc_html_e( 'Panier', 'elvina' ); ?>
					<span class="cart-count"><?php echo absint( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?></span>
				</a>
			<?php endif; ?>
			<button class="nav-toggle" id="nav-toggle" type="button" aria-expanded="false" aria-controls="main-nav"><?php esc_html_e( 'Menu', 'elvina' ); ?></button>
		</div>
	</div>
</header>
