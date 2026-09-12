<?php
/**
 * WooCommerce-specific behaviour: stock badges, category helpers, shop loop tweaks.
 *
 * The badge thresholds mirror the static prototype's logic exactly:
 * 0 = rupture, 1 = derniere piece, 2-3 = stock limite, 4+ = en stock.
 */

defined( 'ABSPATH' ) || exit;

function elvina_woocommerce_products_per_page() {
	return 24;
}
add_filter( 'loop_shop_per_page', 'elvina_woocommerce_products_per_page', 20 );

function elvina_woocommerce_loop_columns() {
	return 4;
}
add_filter( 'loop_shop_columns', 'elvina_woocommerce_loop_columns' );

/**
 * Total stock for a product: sums variation stock for variable products
 * (each size/finish is tracked separately), or reads the simple product's
 * own stock. Returns null when stock isn't tracked, so callers fall back
 * to the plain in-stock/out-of-stock status instead of a number.
 */
function elvina_get_total_stock( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return null;
	}

	if ( ! $product->is_type( 'variable' ) ) {
		return $product->managing_stock() ? (int) $product->get_stock_quantity() : null;
	}

	$total       = 0;
	$has_managed = false;

	foreach ( $product->get_children() as $variation_id ) {
		$variation = wc_get_product( $variation_id );
		if ( ! $variation || ! $variation->exists() ) {
			continue;
		}
		if ( $variation->managing_stock() ) {
			$has_managed = true;
			$total      += (int) $variation->get_stock_quantity();
		}
	}

	return $has_managed ? $total : null;
}

function elvina_stock_badge( $stock_qty, $product ) {
	if ( null === $stock_qty ) {
		return $product->is_in_stock()
			? array(
				'label' => __( 'En stock', 'elvina' ),
				'class' => 'badge--in',
			)
			: array(
				'label' => __( 'Rupture de stock', 'elvina' ),
				'class' => 'badge--out',
			);
	}

	if ( $stock_qty <= 0 ) {
		return array(
			'label' => __( 'Rupture de stock', 'elvina' ),
			'class' => 'badge--out',
		);
	}
	if ( 1 === $stock_qty ) {
		return array(
			'label' => __( 'Dernière pièce', 'elvina' ),
			'class' => 'badge--last',
		);
	}
	if ( $stock_qty <= 3 ) {
		return array(
			'label' => __( 'Stock limité', 'elvina' ),
			'class' => 'badge--low',
		);
	}
	return array(
		'label' => __( 'En stock', 'elvina' ),
		'class' => 'badge--in',
	);
}

function elvina_product_category_slug( $product ) {
	$terms = get_the_terms( $product->get_id(), 'product_cat' );
	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return 'mode';
	}
	foreach ( $terms as $term ) {
		if ( in_array( $term->slug, array( 'mode', 'bijoux' ), true ) ) {
			return $term->slug;
		}
	}
	return $terms[0]->slug;
}

function elvina_category_label( $slug ) {
	return 'bijoux' === $slug ? __( 'Bijoux', 'elvina' ) : __( 'Mode', 'elvina' );
}

function elvina_category_icon( $slug ) {
	$icons = array(
		'mode'   => '<svg viewBox="0 0 48 48" width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><path d="M24 8a3 3 0 1 1 3 3" stroke-linecap="round"/><path d="M24 11v4" stroke-linecap="round"/><path d="M24 15 8 26l3 3 4-2 1 13h16l1-13 4 2 3-3-16-11z" stroke-linejoin="round"/></svg>',
		'bijoux' => '<svg viewBox="0 0 48 48" width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round" aria-hidden="true"><path d="M14 18h20l6 8-16 14-16-14z"/><path d="M14 18l4-8h12l4 8"/><path d="M24 18v22M18 18l6 22 6-22"/></svg>',
	);
	return isset( $icons[ $slug ] ) ? $icons[ $slug ] : $icons['mode'];
}

/**
 * Replace WooCommerce's plain "In stock" text with our styled badge,
 * both in the shop loop (via content-product.php) and on the single
 * product page once a variation is selected.
 */
function elvina_stock_html( $html, $product ) {
	if ( $product->is_type( 'variation' ) ) {
		$qty = $product->managing_stock() ? (int) $product->get_stock_quantity() : null;
	} else {
		$qty = elvina_get_total_stock( $product );
	}
	$badge = elvina_stock_badge( $qty, $product );
	return '<span class="badge ' . esc_attr( $badge['class'] ) . '">' . esc_html( $badge['label'] ) . '</span>';
}
add_filter( 'woocommerce_get_stock_html', 'elvina_stock_html', 10, 2 );

/**
 * Mode / Bijoux filter tabs above the shop and category archives.
 */
function elvina_shop_filter_tabs() {
	$mode_term    = get_term_by( 'slug', 'mode', 'product_cat' );
	$bijoux_term  = get_term_by( 'slug', 'bijoux', 'product_cat' );
	$queried      = get_queried_object();
	$current_slug = ( $queried instanceof WP_Term ) ? $queried->slug : 'all';
	?>
	<div class="filter-tabs">
		<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="filter-tab<?php echo 'all' === $current_slug ? ' is-active' : ''; ?>"><?php esc_html_e( 'Tout', 'elvina' ); ?></a>
		<?php if ( $mode_term && ! is_wp_error( $mode_term ) ) : ?>
			<a href="<?php echo esc_url( get_term_link( $mode_term ) ); ?>" class="filter-tab<?php echo 'mode' === $current_slug ? ' is-active' : ''; ?>"><?php esc_html_e( 'Mode', 'elvina' ); ?></a>
		<?php endif; ?>
		<?php if ( $bijoux_term && ! is_wp_error( $bijoux_term ) ) : ?>
			<a href="<?php echo esc_url( get_term_link( $bijoux_term ) ); ?>" class="filter-tab<?php echo 'bijoux' === $current_slug ? ' is-active' : ''; ?>"><?php esc_html_e( 'Bijoux', 'elvina' ); ?></a>
		<?php endif; ?>
	</div>
	<?php
}
add_action( 'woocommerce_before_shop_loop', 'elvina_shop_filter_tabs', 5 );
