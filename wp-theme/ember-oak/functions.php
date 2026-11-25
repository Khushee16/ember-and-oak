<?php
/**
 * Ember & Oak — theme setup
 *
 * - Theme support: title tag, featured images, HTML5, custom logo
 * - Primary nav menu
 * - Enqueues Bootstrap 5 (CDN), Google Fonts, theme stylesheet + main.js
 * - "Dish" custom post type + "Dish Category" taxonomy (editable menu)
 * - "Reservation" post type (private, stores bookings from the form)
 * - Reservation form handler (admin-post) with the same validation rules
 *   as the standalone reserve.php: name, phone, date (today..+60 days),
 *   time slot, party size 1-12.
 *
 * @package Ember_Oak
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EMBER_OAK_VERSION', '1.0.0' );

/* ---------- Theme setup ---------- */
function ember_oak_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array(
		'height'      => 60,
		'width'       => 220,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'ember-oak' ),
	) );
}
add_action( 'after_setup_theme', 'ember_oak_setup' );

/* ---------- Assets ---------- */
function ember_oak_enqueue() {
	// Google Fonts: Fraunces (display serif) + Inter (sans).
	wp_enqueue_style(
		'ember-oak-fonts',
		'https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500;1,9..144,600&family=Inter:wght@400;500;600;700&display=swap',
		array(),
		null
	);
	// Bootstrap 5.
	wp_enqueue_style( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', array(), '5.3.3' );
	wp_enqueue_script( 'bootstrap-bundle', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array(), '5.3.3', true );

	// Theme stylesheet + script.
	wp_enqueue_style( 'ember-oak-style', get_stylesheet_uri(), array( 'bootstrap', 'ember-oak-fonts' ), EMBER_OAK_VERSION );
	wp_enqueue_script( 'ember-oak-main', get_template_directory_uri() . '/assets/js/main.js', array(), EMBER_OAK_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'ember_oak_enqueue' );

/* ---------- Dish post type + category taxonomy (the editable menu) ---------- */
function ember_oak_register_dish_cpt() {
	register_post_type( 'dish', array(
		'labels' => array(
			'name'          => __( 'Dishes', 'ember-oak' ),
			'singular_name' => __( 'Dish', 'ember-oak' ),
			'add_new_item'  => __( 'Add New Dish', 'ember-oak' ),
			'edit_item'     => __( 'Edit Dish', 'ember-oak' ),
		),
		'public'       => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-food',
		'supports'     => array( 'title', 'editor', 'thumbnail' ),
		'rewrite'      => array( 'slug' => 'dish' ),
	) );

	register_taxonomy( 'dish_category', 'dish', array(
		'labels' => array(
			'name'          => __( 'Dish Categories', 'ember-oak' ),
			'singular_name' => __( 'Dish Category', 'ember-oak' ),
		),
		'hierarchical'      => true,
		'show_admin_column' => true,
		'show_in_rest'     => true,
		'rewrite'           => array( 'slug' => 'menu' ),
	) );
}
add_action( 'init', 'ember_oak_register_dish_cpt' );

/* Dish meta: price + optional tag (e.g. "Vegetarian", "For two"). */
function ember_oak_register_dish_meta() {
	register_post_meta( 'dish', '_ember_oak_price', array(
		'type'              => 'string',
		'single'            => true,
		'show_in_rest'      => true,
		'sanitize_callback' => 'sanitize_text_field',
		'auth_callback'     => function () { return current_user_can( 'edit_posts' ); },
	) );
	register_post_meta( 'dish', '_ember_oak_tag', array(
		'type'              => 'string',
		'single'            => true,
		'show_in_rest'      => true,
		'sanitize_callback' => 'sanitize_text_field',
		'auth_callback'     => function () { return current_user_can( 'edit_posts' ); },
	) );
}
add_action( 'init', 'ember_oak_register_dish_meta' );

function ember_oak_dish_meta_box() {
	add_meta_box( 'ember_oak_dish_details', __( 'Dish Details', 'ember-oak' ), 'ember_oak_dish_meta_box_html', 'dish', 'side' );
}
add_action( 'add_meta_boxes', 'ember_oak_dish_meta_box' );

function ember_oak_dish_meta_box_html( $post ) {
	wp_nonce_field( 'ember_oak_dish_meta', 'ember_oak_dish_nonce' );
	$price = get_post_meta( $post->ID, '_ember_oak_price', true );
	$tag   = get_post_meta( $post->ID, '_ember_oak_tag', true );
	?>
	<p>
		<label for="ember_oak_price"><strong><?php esc_html_e( 'Price ($)', 'ember-oak' ); ?></strong></label><br />
		<input type="text" id="ember_oak_price" name="ember_oak_price" value="<?php echo esc_attr( $price ); ?>" class="widefat" placeholder="24" />
	</p>
	<p>
		<label for="ember_oak_tag"><strong><?php esc_html_e( 'Tag (optional)', 'ember-oak' ); ?></strong></label><br />
		<input type="text" id="ember_oak_tag" name="ember_oak_tag" value="<?php echo esc_attr( $tag ); ?>" class="widefat" placeholder="Vegetarian" />
	</p>
	<?php
}

function ember_oak_save_dish_meta( $post_id ) {
	if ( ! isset( $_POST['ember_oak_dish_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['ember_oak_dish_nonce'] ), 'ember_oak_dish_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	update_post_meta( $post_id, '_ember_oak_price', isset( $_POST['ember_oak_price'] ) ? sanitize_text_field( wp_unslash( $_POST['ember_oak_price'] ) ) : '' );
	update_post_meta( $post_id, '_ember_oak_tag', isset( $_POST['ember_oak_tag'] ) ? sanitize_text_field( wp_unslash( $_POST['ember_oak_tag'] ) ) : '' );
}
add_action( 'save_post_dish', 'ember_oak_save_dish_meta' );

/* ---------- Reservation post type (private storage for bookings) ---------- */
function ember_oak_register_reservation_cpt() {
	register_post_type( 'reservation', array(
		'labels' => array(
			'name'          => __( 'Reservations', 'ember-oak' ),
			'singular_name' => __( 'Reservation', 'ember-oak' ),
		),
		'public'       => false,
		'show_ui'      => true,
		'menu_icon'    => 'dashicons-calendar-alt',
		'supports'     => array( 'title' ),
		'capabilities' => array(
			'create_posts' => 'do_not_allow', // bookings only come from the form
		),
		'map_meta_cap' => true,
	) );
}
add_action( 'init', 'ember_oak_register_reservation_cpt' );

/* ---------- Reservation form handling ---------- */
function ember_oak_time_slots() {
	return array( '5:00 PM', '5:30 PM', '6:00 PM', '6:30 PM', '7:00 PM', '7:30 PM', '8:00 PM', '8:30 PM', '9:00 PM' );
}

function ember_oak_handle_reservation() {
	if ( ! isset( $_POST['ember_oak_reserve_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['ember_oak_reserve_nonce'] ), 'ember_oak_reserve' ) ) {
		wp_die( esc_html__( 'Security check failed. Please go back and try again.', 'ember-oak' ) );
	}

	$redirect = isset( $_POST['ember_oak_redirect'] ) ? esc_url_raw( wp_unslash( $_POST['ember_oak_redirect'] ) ) : home_url( '/reservations/' );
	$errors   = array();

	$name  = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$phone = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$date  = isset( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : '';
	$time  = isset( $_POST['time'] ) ? sanitize_text_field( wp_unslash( $_POST['time'] ) ) : '';
	$party = isset( $_POST['party'] ) ? absint( $_POST['party'] ) : 0;
	$notes = isset( $_POST['notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['notes'] ) ) : '';

	if ( strlen( $name ) < 2 ) {
		$errors[] = __( 'Please enter your name.', 'ember-oak' );
	}
	$digits = preg_replace( '/\D/', '', $phone );
	if ( strlen( $digits ) < 10 || strlen( $digits ) > 15 ) {
		$errors[] = __( 'Please enter a valid phone number.', 'ember-oak' );
	}
	$today   = new DateTimeImmutable( 'today' );
	$maxdate = $today->modify( '+60 days' );
	$dateobj = DateTimeImmutable::createFromFormat( 'Y-m-d', $date );
	if ( ! $dateobj || $dateobj->format( 'Y-m-d' ) !== $date ) {
		$errors[] = __( 'Please choose a valid date.', 'ember-oak' );
	} elseif ( $dateobj < $today ) {
		$errors[] = __( "Reservations can't be in the past.", 'ember-oak' );
	} elseif ( $dateobj > $maxdate ) {
		$errors[] = __( 'Please book within the next 60 days.', 'ember-oak' );
	}
	if ( ! in_array( $time, ember_oak_time_slots(), true ) ) {
		$errors[] = __( 'Please pick a valid time slot.', 'ember-oak' );
	}
	if ( $party < 1 || $party > 12 ) {
		$errors[] = __( 'Party size must be between 1 and 12.', 'ember-oak' );
	}

	if ( ! empty( $errors ) ) {
		set_transient( 'ember_oak_reserve_errors_' . md5( $redirect ), $errors, 60 );
		wp_safe_redirect( add_query_arg( 'reserve', 'error', $redirect ) );
		exit;
	}

	$post_id = wp_insert_post( array(
		'post_type'   => 'reservation',
		'post_status' => 'publish',
		'post_title'  => sprintf( '%s — %s at %s (%s)', $name, $date, $time, $party ),
	) );
	if ( $post_id ) {
		update_post_meta( $post_id, '_ember_oak_phone', $digits );
		update_post_meta( $post_id, '_ember_oak_date', $date );
		update_post_meta( $post_id, '_ember_oak_time', $time );
		update_post_meta( $post_id, '_ember_oak_party', $party );
		update_post_meta( $post_id, '_ember_oak_notes', $notes );
	}

	wp_safe_redirect( add_query_arg( 'reserve', 'success', $redirect ) );
	exit;
}
add_action( 'admin_post_ember_oak_reserve', 'ember_oak_handle_reservation' );
add_action( 'admin_post_nopriv_ember_oak_reserve', 'ember_oak_handle_reservation' );

/* ---------- Fallback menu (when no menu is assigned in Appearance > Menus) ---------- */
function ember_oak_fallback_menu() {
	$pages = array(
		'home'         => home_url( '/' ),
		'menu'         => home_url( '/menu/' ),
		'reservations' => home_url( '/reservations/' ),
	);
	echo '<ul id="navLinks" class="nav-links">';
	echo '<li><a href="' . esc_url( $pages['home'] ) . '">' . esc_html__( 'Home', 'ember-oak' ) . '</a></li>';
	echo '<li><a href="' . esc_url( $pages['menu'] ) . '">' . esc_html__( 'Menu', 'ember-oak' ) . '</a></li>';
	echo '<li><a href="' . esc_url( $pages['reservations'] ) . '">' . esc_html__( 'Hours', 'ember-oak' ) . '</a></li>';
	echo '<li><a href="' . esc_url( $pages['reservations'] ) . '" class="btn btn-primary nav-cta">' . esc_html__( 'Reserve a table', 'ember-oak' ) . '</a></li>';
	echo '</ul>';
}
