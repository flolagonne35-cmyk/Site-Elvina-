<?php
/**
 * Template for the page whose slug is "a-propos" (WordPress' page-{slug}.php
 * template hierarchy picks this up automatically once that page exists).
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main class="wrap block">
	<p class="eyebrow"><?php esc_html_e( 'Notre histoire', 'elvina' ); ?></p>
	<h1 style="font-size:clamp(2rem,5vw,2.8rem);max-width:16ch;"><?php esc_html_e( 'Une seule vitrine, pensée à deux endroits', 'elvina' ); ?></h1>

	<div class="story-grid" style="margin-top:2rem;">
		<div>
			<p class="prose"><?php esc_html_e( "Elvina est née d'une idée simple : proposer une sélection resserrée de mode et de bijoux, choisie pièce par pièce, sans le bruit des grandes enseignes. Un magasin où l'on prend le temps, et où chaque référence a été vue et validée avant d'arriver en rayon.", 'elvina' ); ?></p>
			<p class="prose"><?php esc_html_e( "Dès le départ, la boutique a été pensée pour exister à deux endroits à la fois : entre les mains, en magasin, et sur ce site. Plutôt que de gérer deux stocks séparés — et le risque de vendre deux fois le même article — Elvina fait le choix d'un stock unique, partagé en temps réel entre la caisse et le site.", 'elvina' ); ?></p>
			<blockquote class="pull-quote">« <?php esc_html_e( 'Ce que vous voyez sur cette page, c’est ce qui reste sur le portant.', 'elvina' ); ?> »</blockquote>
			<p class="prose"><?php esc_html_e( "Concrètement, cela veut dire qu'une pièce vendue en boutique disparaît du site en quelques minutes, et qu'une commande en ligne se répercute aussitôt sur ce qu'il reste en magasin. Pas de mauvaise surprise, ni pour vous, ni pour nous.", 'elvina' ); ?></p>
		</div>
		<div class="feature-pair" style="grid-template-columns:1fr;margin-top:0;">
			<div class="feature-card">
				<p class="eyebrow"><?php esc_html_e( 'En boutique', 'elvina' ); ?></p>
				<h3 style="font-size:1.15rem;margin-bottom:.4rem;"><?php esc_html_e( 'Ouverture prochaine', 'elvina' ); ?></h3>
				<p style="color:var(--muted);margin:0;"><?php esc_html_e( 'Adresse et horaires seront annoncés ici dès la date fixée — suivez la page Contact.', 'elvina' ); ?></p>
			</div>
			<div class="feature-card">
				<p class="eyebrow"><?php esc_html_e( 'Notre sélection', 'elvina' ); ?></p>
				<h3 style="font-size:1.15rem;margin-bottom:.4rem;"><?php esc_html_e( 'Peu de pièces, choisies avec soin', 'elvina' ); ?></h3>
				<p style="color:var(--muted);margin:0;"><?php esc_html_e( 'Mode et bijoux se complètent volontairement : une garde-robe courte, mais qui se porte bien ensemble.', 'elvina' ); ?></p>
			</div>
			<div class="feature-card">
				<p class="eyebrow"><?php esc_html_e( 'Nos engagements', 'elvina' ); ?></p>
				<h3 style="font-size:1.15rem;margin-bottom:.4rem;"><?php esc_html_e( 'Stock honnête', 'elvina' ); ?></h3>
				<p style="color:var(--muted);margin:0;"><?php esc_html_e( 'Un article affiché « en stock » est réellement disponible, en boutique comme en ligne.', 'elvina' ); ?></p>
			</div>
		</div>
	</div>
</main>
<?php get_footer(); ?>
