<?php
/**
 * Site footer.
 */

defined( 'ABSPATH' ) || exit;

$elvina_mode_term    = get_term_by( 'slug', 'mode', 'product_cat' );
$elvina_bijoux_term  = get_term_by( 'slug', 'bijoux', 'product_cat' );
$elvina_about_page   = get_page_by_path( 'a-propos' );
$elvina_contact_page = get_page_by_path( 'contact' );
?>
<footer class="site-footer">
	<div class="footer-inner">
		<div class="footer-brand">
			<p class="logo-sm"><?php bloginfo( 'name' ); ?></p>
			<p><?php esc_html_e( 'Mode & bijoux — boutique physique et en ligne, même stock.', 'elvina' ); ?></p>
		</div>
		<div class="footer-cols">
			<div>
				<p class="footer-h"><?php esc_html_e( 'Boutique', 'elvina' ); ?></p>
				<?php if ( function_exists( 'wc_get_page_id' ) ) : ?>
					<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>"><?php esc_html_e( 'Toute la collection', 'elvina' ); ?></a>
				<?php endif; ?>
				<?php if ( $elvina_mode_term && ! is_wp_error( $elvina_mode_term ) ) : ?>
					<a href="<?php echo esc_url( get_term_link( $elvina_mode_term ) ); ?>"><?php esc_html_e( 'Mode', 'elvina' ); ?></a>
				<?php endif; ?>
				<?php if ( $elvina_bijoux_term && ! is_wp_error( $elvina_bijoux_term ) ) : ?>
					<a href="<?php echo esc_url( get_term_link( $elvina_bijoux_term ) ); ?>"><?php esc_html_e( 'Bijoux', 'elvina' ); ?></a>
				<?php endif; ?>
			</div>
			<div>
				<p class="footer-h"><?php bloginfo( 'name' ); ?></p>
				<?php if ( $elvina_about_page ) : ?>
					<a href="<?php echo esc_url( get_permalink( $elvina_about_page ) ); ?>"><?php esc_html_e( 'Notre histoire', 'elvina' ); ?></a>
				<?php endif; ?>
				<?php if ( $elvina_contact_page ) : ?>
					<a href="<?php echo esc_url( get_permalink( $elvina_contact_page ) ); ?>"><?php esc_html_e( 'Contact', 'elvina' ); ?></a>
				<?php endif; ?>
			</div>
			<div>
				<p class="footer-h"><?php esc_html_e( 'Boutique physique', 'elvina' ); ?></p>
				<p><?php esc_html_e( 'Ouverture prochaine — adresse et horaires à venir.', 'elvina' ); ?></p>
			</div>
		</div>
	</div>
	<p class="footer-bottom">© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>.</p>
</footer>

<?php wp_footer(); ?>
</body>
</html>
