<?php
/**
 * Fallback template, required for a valid WordPress theme.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main class="wrap" style="padding-block: clamp(32px, 6vw, 56px);">
	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class(); ?>>
				<h1><?php the_title(); ?></h1>
				<div class="prose"><?php the_content(); ?></div>
			</article>
			<?php
		endwhile;
		?>
	<?php else : ?>
		<p><?php esc_html_e( 'Rien à afficher ici pour le moment.', 'elvina' ); ?></p>
	<?php endif; ?>
</main>
<?php
get_footer();
