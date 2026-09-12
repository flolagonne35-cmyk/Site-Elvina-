<?php
/**
 * Template for the page whose slug is "contact".
 */

defined( 'ABSPATH' ) || exit;

get_header();

$elvina_shop_url = function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/' );
$elvina_status    = isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
?>
<main class="wrap block">
	<p class="eyebrow"><?php esc_html_e( 'Une question', 'elvina' ); ?></p>
	<h2 style="margin-bottom:1.6rem;"><?php esc_html_e( 'Contact', 'elvina' ); ?></h2>

	<div class="contact-grid">
		<div>
			<?php if ( 'sent' === $elvina_status ) : ?>
				<p class="form-note" role="status"><?php esc_html_e( 'Merci, votre message a bien été envoyé.', 'elvina' ); ?></p>
			<?php elseif ( 'error' === $elvina_status ) : ?>
				<p class="form-note" role="status"><?php esc_html_e( 'Un champ est manquant ou invalide — merci de réessayer.', 'elvina' ); ?></p>
			<?php endif; ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="elvina_contact">
				<?php wp_nonce_field( 'elvina_contact', 'elvina_contact_nonce' ); ?>
				<div class="field">
					<label for="name"><?php esc_html_e( 'Nom', 'elvina' ); ?></label>
					<input type="text" id="name" name="name" required>
				</div>
				<div class="field">
					<label for="email"><?php esc_html_e( 'E-mail', 'elvina' ); ?></label>
					<input type="email" id="email" name="email" required>
				</div>
				<div class="field">
					<label for="message"><?php esc_html_e( 'Message', 'elvina' ); ?></label>
					<textarea id="message" name="message" required></textarea>
				</div>
				<button type="submit" class="btn"><?php esc_html_e( 'Envoyer', 'elvina' ); ?></button>
			</form>
		</div>

		<aside class="info-card">
			<dl>
				<dt><?php esc_html_e( 'Boutique physique', 'elvina' ); ?></dt>
				<dd><?php esc_html_e( 'Ouverture prochaine — adresse annoncée ici', 'elvina' ); ?></dd>
				<dt><?php esc_html_e( 'Horaires', 'elvina' ); ?></dt>
				<dd><?php esc_html_e( 'À venir', 'elvina' ); ?></dd>
				<dt><?php esc_html_e( 'Boutique en ligne', 'elvina' ); ?></dt>
				<dd><?php esc_html_e( 'Ouverte en continu — ', 'elvina' ); ?><a href="<?php echo esc_url( $elvina_shop_url ); ?>"><?php esc_html_e( 'voir la collection', 'elvina' ); ?></a></dd>
			</dl>
		</aside>
	</div>
</main>
<?php get_footer(); ?>
