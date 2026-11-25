<?php
/**
 * Menu page template — lists every Dish grouped by Dish Category.
 * Template Name: Menu Page
 *
 * Assign to the "Menu" page (Pages > Menu > Page Attributes > Template).
 * Dishes are managed in the dashboard under Dishes; each dish has a price
 * and optional tag in the "Dish Details" box, and a category in
 * Dishes > Dish Categories.
 *
 * @package Ember_Oak
 */

get_header();
?>

<section class="page-hero">
	<div class="container">
		<p class="kicker reveal"><?php esc_html_e( 'The menu', 'ember-oak' ); ?></p>
		<h1 class="reveal"><?php esc_html_e( 'Cooked over oak,', 'ember-oak' ); ?><br /><em><?php esc_html_e( 'served with care', 'ember-oak' ); ?></em></h1>
		<p class="reveal reveal-delay-1"><?php esc_html_e( 'Our menu shifts with the seasons — here’s what’s on the pass this week.', 'ember-oak' ); ?></p>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php
		$categories = get_terms( array(
			'taxonomy'   => 'dish_category',
			'hide_empty' => true,
			'orderby'    => 'term_order',
		) );

		if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) :
			?>
			<div class="tabs" id="menuTabs" role="tablist" aria-label="<?php esc_attr_e( 'Menu categories', 'ember-oak' ); ?>">
				<?php foreach ( $categories as $i => $cat ) : ?>
					<button class="tab<?php echo 0 === $i ? ' active' : ''; ?>" type="button" role="tab" data-category="<?php echo esc_attr( $cat->slug ); ?>" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>">
						<?php echo esc_html( $cat->name ); ?>
					</button>
				<?php endforeach; ?>
			</div>

			<?php foreach ( $categories as $i => $cat ) : ?>
				<div class="menu-list menu-category" data-category="<?php echo esc_attr( $cat->slug ); ?>" <?php echo 0 !== $i ? 'hidden' : ''; ?>>
					<?php
					$dishes = new WP_Query( array(
						'post_type'      => 'dish',
						'posts_per_page' => -1,
						'orderby'        => 'menu_order title',
						'order'          => 'ASC',
						'tax_query'      => array(
							array(
								'taxonomy' => 'dish_category',
								'field'    => 'slug',
								'terms'    => $cat->slug,
							),
						),
					) );
					while ( $dishes->have_posts() ) :
						$dishes->the_post();
						$price = get_post_meta( get_the_ID(), '_ember_oak_price', true );
						$tag   = get_post_meta( get_the_ID(), '_ember_oak_tag', true );
						?>
						<div class="menu-item">
							<div class="menu-item-head">
								<h3><?php the_title(); ?><?php if ( $tag !== '' ) : ?><span class="tag"><?php echo esc_html( $tag ); ?></span><?php endif; ?></h3>
								<?php if ( $price !== '' ) : ?><span class="menu-price">$<?php echo esc_html( $price ); ?></span><?php endif; ?>
							</div>
							<p><?php echo esc_html( wp_strip_all_tags( get_the_content() ) ); ?></p>
						</div>
						<?php
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			<?php endforeach; ?>

		<?php else : ?>
			<div class="text-center">
				<p><?php esc_html_e( 'The menu is being written — add dishes in the dashboard under Dishes, with a category for each, and they will appear here automatically.', 'ember-oak' ); ?></p>
			</div>
		<?php endif; ?>

		<p class="menu-note"><?php esc_html_e( 'Please tell us about allergies — most dishes can be adapted. A 20% service charge is shared with the whole team.', 'ember-oak' ); ?></p>
		<div class="text-center mt-5">
			<a href="<?php echo esc_url( home_url( '/reservations/' ) ); ?>" class="btn btn-primary btn-lg"><?php esc_html_e( 'Book a table to try it', 'ember-oak' ); ?></a>
		</div>
	</div>
</section>

<?php
get_footer();
