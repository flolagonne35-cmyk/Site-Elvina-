<?php
/**
 * Home page: hero, category tiles, featured products, opening notes.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$elvina_shop_url    = function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/' );
$elvina_about_page  = get_page_by_path( 'a-propos' );
$elvina_mode_term   = get_term_by( 'slug', 'mode', 'product_cat' );
$elvina_bijoux_term = get_term_by( 'slug', 'bijoux', 'product_cat' );
?>

<main>
	<section class="hero wrap">
		<div class="hero-grid">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'Boutique physique & en ligne', 'elvina' ); ?></p>
				<h1><?php bloginfo( 'name' ); ?></h1>
				<p class="hero-lede"><?php esc_html_e( "Une sélection resserrée de mode et de bijoux, choisie pièce par pièce. Ce que vous voyez ici est exactement ce qu'il reste en boutique — pas plus, pas moins.", 'elvina' ); ?></p>
				<div class="hero-actions">
					<a class="btn" href="<?php echo esc_url( $elvina_shop_url ); ?>"><?php esc_html_e( 'Voir la boutique', 'elvina' ); ?></a>
					<?php if ( $elvina_about_page ) : ?>
						<a class="btn btn--outline" href="<?php echo esc_url( get_permalink( $elvina_about_page ) ); ?>"><?php esc_html_e( 'Notre histoire', 'elvina' ); ?></a>
					<?php endif; ?>
				</div>
			</div>
			<div class="hero-plate" aria-hidden="true"><?php echo elvina_category_icon( 'bijoux' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		</div>
	</section>

	<section class="block wrap">
		<div class="tiles">
			<?php if ( $elvina_mode_term && ! is_wp_error( $elvina_mode_term ) ) : ?>
				<a class="tile tile--mode" href="<?php echo esc_url( get_term_link( $elvina_mode_term ) ); ?>">
					<?php echo elvina_category_icon( 'mode' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<h3><?php esc_html_e( 'Mode', 'elvina' ); ?></h3>
					<span><?php esc_html_e( 'Prêt-à-porter en petites séries', 'elvina' ); ?></span>
				</a>
			<?php endif; ?>
			<?php if ( $elvina_bijoux_term && ! is_wp_error( $elvina_bijoux_term ) ) : ?>
				<a class="tile tile--bijoux" href="<?php echo esc_url( get_term_link( $elvina_bijoux_term ) ); ?>">
					<?php echo elvina_category_icon( 'bijoux' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<h3><?php esc_html_e( 'Bijoux', 'elvina' ); ?></h3>
					<span><?php esc_html_e( 'Fins, portés au quotidien', 'elvina' ); ?></span>
				</a>
			<?php endif; ?>
		</div>
	</section>

	<?php if ( function_exists( 'wc_get_products' ) ) : ?>
	<section class="block wrap">
		<div class="section-head">
			<h2><?php esc_html_e( 'Sélection du moment', 'elvina' ); ?></h2>
			<a href="<?php echo esc_url( $elvina_shop_url ); ?>"><?php esc_html_e( 'Toute la boutique →', 'elvina' ); ?></a>
		</div>
		<?php
		$elvina_featured = wc_get_products(
			array(
				'featured' => true,
				'limit'    => 4,
				'status'   => 'publish',
			)
		);
		if ( $elvina_featured ) :
			echo '<ul class="products">';
			foreach ( $elvina_featured as $elvina_featured_product ) {
				$GLOBALS['product'] = $elvina_featured_product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
				wc_get_template_part( 'content', 'product' );
			}
			echo '</ul>';
		endif;
		?>
	</section>
	<?php endif; ?>

	<section class="block wrap">
		<div class="feature-pair">
			<div class="feature-card">
				<p class="eyebrow"><?php esc_html_e( 'En boutique', 'elvina' ); ?></p>
				<h3 style="font-size:1.2rem;margin-bottom:.4rem;"><?php esc_html_e( 'Ouverture prochaine', 'elvina' ); ?></h3>
				<p style="color:var(--muted);margin:0;"><?php esc_html_e( 'Adresse et horaires seront annoncés ici et sur la page Contact dès la date fixée.', 'elvina' ); ?></p>
			</div>
			<div class="feature-card">
				<p class="eyebrow"><?php esc_html_e( 'En ligne', 'elvina' ); ?></p>
				<h3 style="font-size:1.2rem;margin-bottom:.4rem;"><?php esc_html_e( 'Le même stock, à la pièce près', 'elvina' ); ?></h3>
				<p style="color:var(--muted);margin:0;"><?php esc_html_e( 'Un article vendu en boutique disparaît du site en quelques minutes — jamais de commande sur un article déjà parti.', 'elvina' ); ?></p>
			</div>
		</div>
	</section>
</main>

<?php get_footer(); ?>
