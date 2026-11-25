<?php
/**
 * Fallback template — used for any page/post without a more specific
 * template (blog posts, archives, search results, etc.).
 *
 * @package Ember_Oak
 */

get_header();
?>

<section class="page-content">
	<div class="container narrow">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<h1><?php the_title(); ?></h1>
				<?php the_content(); ?>
				<?php
				if ( is_single() ) {
					comments_template();
				}
				?>
			<?php endwhile; ?>
		<?php else : ?>
			<h1><?php esc_html_e( 'Nothing found', 'ember-oak' ); ?></h1>
			<p><?php esc_html_e( 'Sorry, there is nothing here yet.', 'ember-oak' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
