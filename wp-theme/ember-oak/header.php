<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header" id="siteHeader">
	<nav class="nav-inner" aria-label="<?php esc_attr_e( 'Main navigation', 'ember-oak' ); ?>">
		<?php if ( has_custom_logo() ) : ?>
			<?php the_custom_logo(); ?>
		<?php else : ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo">Ember <span>&amp;</span> Oak</a>
		<?php endif; ?>
		<button id="hamburger" class="hamburger" aria-label="<?php esc_attr_e( 'Open menu', 'ember-oak' ); ?>" aria-expanded="false" aria-controls="navLinks">
			<span></span><span></span><span></span>
		</button>
		<?php
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'menu_id'        => 'navLinks',
			'menu_class'     => 'nav-links',
			'container'      => false,
			'fallback_cb'    => 'ember_oak_fallback_menu',
		) );
		?>
	</nav>
</header>

</main>
