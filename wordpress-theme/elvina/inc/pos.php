<?php
/**
 * "Caisse comptoir" — a minimal in-house point-of-sale screen for staff to
 * record an in-store sale against the exact same WooCommerce stock the
 * website reads from.
 *
 * This is NOT a payment processor: staff keep taking payment on their
 * existing card terminal. Recording a sale here creates a real, completed
 * WooCommerce order (so it shows in WooCommerce > Commandes and in
 * Analytics like any other sale) and reduces stock through WooCommerce's
 * own wc_reduce_stock_levels(), the same path a real online order takes.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

add_action( 'admin_menu', 'elvina_pos_register_page' );
function elvina_pos_register_page() {
	add_submenu_page(
		'woocommerce',
		__( 'Caisse comptoir', 'elvina' ),
		__( 'Caisse comptoir', 'elvina' ),
		'manage_woocommerce',
		'elvina-pos',
		'elvina_pos_render_page'
	);
}

add_action( 'admin_enqueue_scripts', 'elvina_pos_enqueue' );
function elvina_pos_enqueue( $hook ) {
	if ( 'woocommerce_page_elvina-pos' !== $hook ) {
		return;
	}

	wp_enqueue_style( 'elvina-pos-admin', get_template_directory_uri() . '/assets/pos-admin.css', array(), ELVINA_VERSION );
	wp_enqueue_script( 'elvina-pos-admin', get_template_directory_uri() . '/assets/pos-admin.js', array(), ELVINA_VERSION, true );

	wp_localize_script(
		'elvina-pos-admin',
		'ElvinaPOS',
		array(
			'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'elvina_pos_sell' ),
			'products' => elvina_pos_get_catalog(),
			'i18n'     => array(
				'sold'       => __( 'vendu — stock mis à jour.', 'elvina' ),
				'error'      => __( 'Une erreur est survenue, réessayez.', 'elvina' ),
				'noResults'  => __( 'Aucun article ne correspond.', 'elvina' ),
				'noSales'    => __( "Aucune vente pour l'instant.", 'elvina' ),
			),
		)
	);
}

/**
 * Serialize the live WooCommerce catalog for the admin screen: id,
 * category, name, and every variant with its real current stock.
 */
function elvina_pos_get_catalog() {
	$products = wc_get_products(
		array(
			'status' => 'publish',
			'limit'  => -1,
			'type'   => array( 'simple', 'variable' ),
		)
	);

	$catalog = array();

	foreach ( $products as $product ) {
		$entry = array(
			'id'       => $product->get_id(),
			'name'     => $product->get_name(),
			'category' => elvina_product_category_slug( $product ),
			'variants' => array(),
		);

		if ( $product->is_type( 'variable' ) ) {
			foreach ( $product->get_children() as $variation_id ) {
				$variation = wc_get_product( $variation_id );
				if ( ! $variation || ! $variation->exists() ) {
					continue;
				}
				$label = implode( ' / ', $variation->get_variation_attributes( false ) );
				$entry['variants'][] = array(
					'variationId' => $variation_id,
					'label'       => $label ? $label : __( 'Variante', 'elvina' ),
					'stock'       => $variation->managing_stock() ? (int) $variation->get_stock_quantity() : null,
					'price'       => (float) wc_get_price_to_display( $variation ),
				);
			}
		} else {
			$entry['variants'][] = array(
				'variationId' => 0,
				'label'       => __( 'Standard', 'elvina' ),
				'stock'       => $product->managing_stock() ? (int) $product->get_stock_quantity() : null,
				'price'       => (float) wc_get_price_to_display( $product ),
			);
		}

		$catalog[] = $entry;
	}

	return $catalog;
}

function elvina_pos_render_page() {
	?>
	<div class="wrap elvina-pos" id="elvina-pos-app">
		<h1><?php esc_html_e( 'Caisse comptoir', 'elvina' ); ?></h1>
		<p class="description">
			<?php esc_html_e( 'Enregistrez une vente faite en boutique : le stock se met à jour immédiatement, ici et sur le site — le paiement carte se prend toujours sur votre terminal habituel.', 'elvina' ); ?>
		</p>

		<input type="text" id="elvina-pos-search" class="elvina-pos-search" placeholder="<?php esc_attr_e( 'Chercher un article (nom)…', 'elvina' ); ?>" aria-label="<?php esc_attr_e( 'Chercher un article', 'elvina' ); ?>" autocomplete="off">

		<div class="elvina-pos-layout">
			<div class="elvina-pos-list" id="elvina-pos-list"></div>

			<aside class="elvina-pos-ticket">
				<h2><?php esc_html_e( 'Ventes du jour', 'elvina' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Ce journal s’efface si vous rechargez la page ; l’historique complet reste dans WooCommerce > Commandes.', 'elvina' ); ?></p>
				<ul class="elvina-pos-ticket-list" id="elvina-pos-ticket-list">
					<li class="elvina-pos-ticket-empty"><?php esc_html_e( "Aucune vente pour l'instant.", 'elvina' ); ?></li>
				</ul>
				<div class="elvina-pos-ticket-total">
					<span><?php esc_html_e( 'Total', 'elvina' ); ?></span>
					<span id="elvina-pos-ticket-total">0,00&nbsp;€</span>
				</div>
				<p class="elvina-pos-flash" id="elvina-pos-flash" role="status"></p>
			</aside>
		</div>
	</div>
	<?php
}

add_action( 'wp_ajax_elvina_pos_sell', 'elvina_pos_handle_sell' );
function elvina_pos_handle_sell() {
	check_ajax_referer( 'elvina_pos_sell', 'nonce' );

	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_send_json_error( array( 'message' => __( "Vous n'avez pas les droits nécessaires.", 'elvina' ) ), 403 );
	}

	$product_id   = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$variation_id = isset( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : 0;
	$qty          = isset( $_POST['qty'] ) ? max( 1, absint( $_POST['qty'] ) ) : 1;

	$sold_product = wc_get_product( $variation_id ? $variation_id : $product_id );
	if ( ! $sold_product || ! $sold_product->exists() ) {
		wp_send_json_error( array( 'message' => __( 'Article introuvable.', 'elvina' ) ), 404 );
	}

	if ( $sold_product->managing_stock() && $sold_product->get_stock_quantity() < $qty ) {
		wp_send_json_error(
			array(
				'message' => __( "Stock insuffisant — quelqu'un d'autre a peut-être déjà vendu cet article.", 'elvina' ),
				'stock'   => (int) $sold_product->get_stock_quantity(),
			),
			409
		);
	}

	$order = wc_create_order();
	$order->add_product( $sold_product, $qty );
	$order->set_created_via( 'elvina_pos' );
	$order->set_payment_method( 'elvina_pos' );
	$order->set_payment_method_title( __( 'Vente en boutique', 'elvina' ) );
	$order->calculate_totals();
	$order->update_status( 'completed', __( 'Vente enregistrée depuis la caisse comptoir Elvina.', 'elvina' ) );

	wc_reduce_stock_levels( $order->get_id() );

	$parent_product = wc_get_product( $product_id );
	$fresh_variant  = wc_get_product( $variation_id ? $variation_id : $product_id );

	wp_send_json_success(
		array(
			'orderId'        => $order->get_id(),
			'newStock'       => $fresh_variant->managing_stock() ? (int) $fresh_variant->get_stock_quantity() : null,
			'itemStockTotal' => $parent_product ? elvina_get_total_stock( $parent_product ) : null,
			'total'          => (float) $order->get_total(),
			'name'           => $sold_product->get_name(),
			'qty'            => $qty,
		)
	);
}
