<?php
/**
 * Main WooCommerce wrapper template — the minimal integration WooCommerce's
 * own theme developer handbook recommends. Every WooCommerce page (shop,
 * category, single product, cart, checkout, my account) that doesn't have a
 * more specific override renders through here.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="primary" class="wrap block woocommerce-main">
	<?php woocommerce_content(); ?>
</main>
<?php
get_footer();
