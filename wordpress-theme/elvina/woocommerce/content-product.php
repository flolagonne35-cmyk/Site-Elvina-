<?php
/**
 * Shop-loop product card override.
 *
 * Deliberately minimal compared to WooCommerce's default content-product.php:
 * no separate add-to-cart button in the grid (matches the original design —
 * cards link straight to the product page, same as the static prototype).
 *
 * @global WC_Product $product
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product || ! $product->is_visible() ) {
	return;
}

$elvina_stock    = elvina_get_total_stock( $product );
$elvina_badge    = elvina_stock_badge( $elvina_stock, $product );
$elvina_category = elvina_product_category_slug( $product );
?>
<li <?php wc_product_class( 'product-card', $product ); ?>>
	<a href="<?php echo esc_url( get_permalink() ); ?>" class="product-card-link">
		<div class="product-media product-media--<?php echo esc_attr( $elvina_category ); ?>">
			<?php echo elvina_category_icon( $elvina_category ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<span class="product-media-tag"><?php echo esc_html( elvina_category_label( $elvina_category ) ); ?></span>
		</div>
		<div class="product-info">
			<p class="product-name"><?php echo esc_html( $product->get_name() ); ?></p>
			<p class="product-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></p>
			<span class="badge <?php echo esc_attr( $elvina_badge['class'] ); ?>"><?php echo esc_html( $elvina_badge['label'] ); ?></span>
		</div>
	</a>
</li>
