<?php
/**
 * Front page template — hero, about, features, featured dishes, CTA.
 * The featured dishes are the three dishes flagged in the Dish post type
 * (or, if none exist yet, the sample dishes below so the page never looks
 * empty on a fresh install — delete the samples once real dishes exist).
 *
 * @package Ember_Oak
 */

get_header();
?>

<section class="hero">
	<div class="container hero-content">
		<p class="hero-kicker"><?php esc_html_e( 'Wood-fired kitchen · Portland, OR', 'ember-oak' ); ?></p>
		<h1><?php esc_html_e( 'Slow food,', 'ember-oak' ); ?><br /><?php esc_html_e( 'cooked over', 'ember-oak' ); ?> <em><?php esc_html_e( 'live fire', 'ember-oak' ); ?></em></h1>
		<p class="hero-sub"><?php esc_html_e( 'Seasonal Pacific Northwest ingredients, an oak-burning hearth, and a dining room built for lingering. Dinner Tuesday through Sunday.', 'ember-oak' ); ?></p>
		<div class="hero-ctas">
			<a href="<?php echo esc_url( home_url( '/reservations/' ) ); ?>" class="btn btn-primary btn-lg"><?php esc_html_e( 'Book a table', 'ember-oak' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/menu/' ) ); ?>" class="btn btn-outline-light btn-lg"><?php esc_html_e( 'View the menu', 'ember-oak' ); ?></a>
		</div>
		<div class="hero-meta">
			<div><strong><?php esc_html_e( 'Tue – Sun', 'ember-oak' ); ?></strong><?php esc_html_e( 'Dinner from 5 PM', 'ember-oak' ); ?></div>
			<div><strong>214 Alder St</strong><?php esc_html_e( 'Downtown Portland', 'ember-oak' ); ?></div>
			<div><strong>120 <?php esc_html_e( 'bottles', 'ember-oak' ); ?></strong><?php esc_html_e( 'Natural wine list', 'ember-oak' ); ?></div>
		</div>
	</div>
	<a href="#about" class="hero-scroll"><?php esc_html_e( 'Scroll', 'ember-oak' ); ?></a>
</section>

<section class="about" id="about">
	<div class="container about-grid">
		<div class="reveal">
			<p class="kicker"><?php esc_html_e( 'Our story', 'ember-oak' ); ?></p>
			<h2><?php esc_html_e( 'From our hearth to your table', 'ember-oak' ); ?></h2>
			<p class="lead"><?php esc_html_e( 'Everything at Ember & Oak touches oak fire before it touches your plate — flatbreads blistered in 90 seconds, vegetables caramelized in cast iron, and dry-aged cuts rested over embers. Our menu changes with the market, but the fire never goes out.', 'ember-oak' ); ?></p>
			<div class="about-img">
				<img src="https://picsum.photos/seed/ember-hearth/1000/620" alt="<?php esc_attr_e( 'Glowing oak embers in the open hearth', 'ember-oak' ); ?>" loading="lazy" />
			</div>
		</div>
		<ul class="about-points">
			<li class="reveal reveal-delay-1"><strong><?php esc_html_e( 'Open hearth', 'ember-oak' ); ?></strong><span><?php esc_html_e( 'Watch your dinner cook over Oregon white oak', 'ember-oak' ); ?></span></li>
			<li class="reveal reveal-delay-2"><strong><?php esc_html_e( 'Local first', 'ember-oak' ); ?></strong><span><?php esc_html_e( 'Most ingredients from within 150 miles of Portland', 'ember-oak' ); ?></span></li>
			<li class="reveal reveal-delay-3"><strong><?php esc_html_e( 'Natural wines', 'ember-oak' ); ?></strong><span><?php esc_html_e( 'A 120-bottle low-intervention list', 'ember-oak' ); ?></span></li>
		</ul>
	</div>
</section>

<section class="feature-strip">
	<div class="container">
		<div class="section-head reveal">
			<p class="kicker"><?php esc_html_e( 'Why Ember & Oak', 'ember-oak' ); ?></p>
			<h2><?php esc_html_e( 'A dining room built for lingering', 'ember-oak' ); ?></h2>
			<div class="rule"></div>
		</div>
		<div class="row g-4">
			<div class="col-md-4 reveal">
				<div class="feature-card">
					<div class="feature-icon" aria-hidden="true">&#128293;</div>
					<h3><?php esc_html_e( 'Live fire, always', 'ember-oak' ); ?></h3>
					<p><?php esc_html_e( 'No gas, no electric — just Oregon white oak, cast iron, and patience. The hearth is lit at noon and never goes out during service.', 'ember-oak' ); ?></p>
				</div>
			</div>
			<div class="col-md-4 reveal reveal-delay-1">
				<div class="feature-card">
					<div class="feature-icon" aria-hidden="true">&#127806;</div>
					<h3><?php esc_html_e( 'Market-driven menu', 'ember-oak' ); ?></h3>
					<p><?php esc_html_e( "The menu is rewritten every week around the morning market run. What's on the pass tonight was in the ground this morning.", 'ember-oak' ); ?></p>
				</div>
			</div>
			<div class="col-md-4 reveal reveal-delay-2">
				<div class="feature-card">
					<div class="feature-icon" aria-hidden="true">&#127863;</div>
					<h3><?php esc_html_e( 'Natural wine', 'ember-oak' ); ?></h3>
					<p><?php esc_html_e( 'A 120-bottle low-intervention list, poured by people who love talking about it — ask for a pairing with anything on the menu.', 'ember-oak' ); ?></p>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="section section-alt">
	<div class="container">
		<div class="section-head reveal">
			<p class="kicker"><?php esc_html_e( 'A taste of the menu', 'ember-oak' ); ?></p>
			<h2><?php esc_html_e( 'From the hearth tonight', 'ember-oak' ); ?></h2>
			<p><?php esc_html_e( 'A few favorites from the pass — the full menu changes weekly.', 'ember-oak' ); ?></p>
			<div class="rule"></div>
		</div>
		<div class="row g-4">
			<?php
			$featured = new WP_Query( array(
				'post_type'      => 'dish',
				'posts_per_page' => 3,
				'orderby'        => 'rand',
			) );
			if ( $featured->have_posts() ) :
				while ( $featured->have_posts() ) :
					$featured->the_post();
					$price = get_post_meta( get_the_ID(), '_ember_oak_price', true );
					?>
					<div class="col-md-4 reveal">
						<article class="dish-card">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'large' ); ?>
							<?php endif; ?>
							<div class="dish-card-body">
								<h3><?php the_title(); ?></h3>
								<p><?php echo esc_html( wp_trim_words( get_the_content(), 18 ) ); ?></p>
								<?php if ( $price !== '' ) : ?>
									<span class="menu-price">$<?php echo esc_html( $price ); ?></span>
								<?php endif; ?>
							</div>
						</article>
					</div>
					<?php
				endwhile;
				wp_reset_postdata();
			else :
				// Fresh install fallback — sample dishes until real ones are added.
				$samples = array(
					array( 'Oak-Fired Ribeye', '28-day dry-aged, ember onions, bone-marrow jus. For two.', '68', 'https://picsum.photos/seed/ember-ribeye/800/500' ),
					array( 'Margherita Classica', 'San Marzano, fior di latte, basil. 90 seconds in the hearth.', '19', 'https://picsum.photos/seed/ember-pizza/800/500' ),
					array( 'Cedar Plank Salmon', 'Miso glaze, charred scallions, sesame rice.', '34', 'https://picsum.photos/seed/ember-salmon/800/500' ),
				);
				foreach ( $samples as $i => $dish ) :
					?>
					<div class="col-md-4 reveal<?php echo $i ? ' reveal-delay-' . $i : ''; ?>">
						<article class="dish-card">
							<img src="<?php echo esc_url( $dish[3] ); ?>" alt="<?php echo esc_attr( $dish[0] ); ?>" loading="lazy" />
							<div class="dish-card-body">
								<h3><?php echo esc_html( $dish[0] ); ?></h3>
								<p><?php echo esc_html( $dish[1] ); ?></p>
								<span class="menu-price">$<?php echo esc_html( $dish[2] ); ?></span>
							</div>
						</article>
					</div>
					<?php
				endforeach;
			endif;
			?>
		</div>
		<div class="text-center mt-5 reveal">
			<a href="<?php echo esc_url( home_url( '/menu/' ) ); ?>" class="btn btn-primary btn-lg"><?php esc_html_e( 'See the full menu', 'ember-oak' ); ?></a>
		</div>
	</div>
</section>

<section class="section">
	<div class="container narrow text-center reveal">
		<p class="kicker"><?php esc_html_e( 'Reservations', 'ember-oak' ); ?></p>
		<h2 class="mb-3"><?php esc_html_e( 'Hungry? Save your seat.', 'ember-oak' ); ?></h2>
		<p class="text-muted mb-4"><?php esc_html_e( 'Tables are held for 15 minutes past reservation time. Parties of 7+ please call us directly at (503) 555-0184.', 'ember-oak' ); ?></p>
		<a href="<?php echo esc_url( home_url( '/reservations/' ) ); ?>" class="btn btn-primary btn-lg"><?php esc_html_e( 'Book a table', 'ember-oak' ); ?></a>
	</div>
</section>

<?php
get_footer();
