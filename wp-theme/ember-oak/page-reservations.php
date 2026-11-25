<?php
/**
 * Reservations page template — booking form + opening hours.
 * Template Name: Reservations Page
 *
 * Assign to the "Reservations" page (Pages > Reservations > Page Attributes
 * > Template). Submissions are validated in functions.php and stored as
 * private "Reservation" posts (Dashboard > Reservations).
 *
 * @package Ember_Oak
 */

get_header();

$reserve_status = isset( $_GET['reserve'] ) ? sanitize_key( wp_unslash( $_GET['reserve'] ) ) : '';
$today          = gmdate( 'Y-m-d' );
$max_date       = gmdate( 'Y-m-d', strtotime( '+60 days' ) );
$slots          = ember_oak_time_slots();
?>

<section class="page-hero">
	<div class="container">
		<p class="kicker reveal"><?php esc_html_e( 'Reservations', 'ember-oak' ); ?></p>
		<h1 class="reveal"><?php esc_html_e( 'Book your', 'ember-oak' ); ?> <em><?php esc_html_e( 'table', 'ember-oak' ); ?></em></h1>
		<p class="reveal reveal-delay-1"><?php esc_html_e( 'Tables are held for 15 minutes past reservation time. Parties of 7+ please call us directly.', 'ember-oak' ); ?></p>
	</div>
</section>

<section class="section" id="reserve">
	<div class="container narrow">
		<?php if ( 'success' === $reserve_status ) : ?>
			<div class="notice notice-success" role="status">
				<strong><?php esc_html_e( "You're booked!", 'ember-oak' ); ?></strong>
				<?php esc_html_e( 'Thank you — a confirmation text is on its way. We can’t wait to host you.', 'ember-oak' ); ?>
			</div>
		<?php elseif ( 'error' === $reserve_status ) : ?>
			<div class="notice notice-error" role="alert">
				<strong><?php esc_html_e( 'Something wasn’t right with that booking.', 'ember-oak' ); ?></strong>
				<?php esc_html_e( 'Please check the form and try again.', 'ember-oak' ); ?>
			</div>
		<?php endif; ?>

		<form class="reserve-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'ember_oak_reserve', 'ember_oak_reserve_nonce' ); ?>
			<input type="hidden" name="action" value="ember_oak_reserve" />
			<input type="hidden" name="ember_oak_redirect" value="<?php echo esc_url( get_permalink() ); ?>" />
			<div class="form-row">
				<div class="form-group">
					<label for="rName"><?php esc_html_e( 'Full name', 'ember-oak' ); ?></label>
					<input type="text" id="rName" name="name" autocomplete="name" placeholder="Jordan Ellis" required minlength="2" />
				</div>
				<div class="form-group">
					<label for="rPhone"><?php esc_html_e( 'Phone', 'ember-oak' ); ?></label>
					<input type="tel" id="rPhone" name="phone" autocomplete="tel" placeholder="(503) 555-0100" required />
				</div>
			</div>
			<div class="form-row cols-3">
				<div class="form-group">
					<label for="rDate"><?php esc_html_e( 'Date', 'ember-oak' ); ?></label>
					<input type="date" id="rDate" name="date" min="<?php echo esc_attr( $today ); ?>" max="<?php echo esc_attr( $max_date ); ?>" required />
				</div>
				<div class="form-group">
					<label for="rTime"><?php esc_html_e( 'Time', 'ember-oak' ); ?></label>
					<select id="rTime" name="time" required>
						<option value=""><?php esc_html_e( 'Select...', 'ember-oak' ); ?></option>
						<?php foreach ( $slots as $slot ) : ?>
							<option value="<?php echo esc_attr( $slot ); ?>"><?php echo esc_html( $slot ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group">
					<label for="rParty"><?php esc_html_e( 'Party size', 'ember-oak' ); ?></label>
					<select id="rParty" name="party" required>
						<option value=""><?php esc_html_e( 'Select...', 'ember-oak' ); ?></option>
						<?php for ( $n = 1; $n <= 12; $n++ ) : ?>
							<option value="<?php echo esc_attr( $n ); ?>"><?php echo esc_html( $n . ( 1 === $n ? ' guest' : ' guests' ) ); ?></option>
						<?php endfor; ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="rNotes"><?php esc_html_e( 'Special requests', 'ember-oak' ); ?> <span class="optional">(<?php esc_html_e( 'optional', 'ember-oak' ); ?>)</span></label>
				<textarea id="rNotes" name="notes" rows="3" placeholder="<?php esc_attr_e( 'Anniversary, high chair, dietary needs...', 'ember-oak' ); ?>"></textarea>
			</div>
			<button type="submit" class="btn btn-primary btn-lg btn-block"><?php esc_html_e( 'Confirm reservation', 'ember-oak' ); ?></button>
		</form>
	</div>
</section>

<section class="section section-alt" id="hours">
	<div class="container hours-grid">
		<div class="reveal">
			<p class="kicker"><?php esc_html_e( 'Visit us', 'ember-oak' ); ?></p>
			<h2><?php esc_html_e( 'Opening hours', 'ember-oak' ); ?></h2>
			<p class="hours-address"><strong>214 Alder Street, Portland, OR</strong><br />(503) 555-0184<br />hello@emberandoak.example</p>
			<p class="text-muted mt-3"><?php esc_html_e( 'Kitchen closes 30 minutes before the dining room. The hearth is lit from noon every day.', 'ember-oak' ); ?></p>
		</div>
		<table class="hours-table reveal reveal-delay-1" id="hoursTable" aria-label="<?php esc_attr_e( 'Opening hours', 'ember-oak' ); ?>">
			<tbody></tbody>
		</table>
	</div>
</section>

<?php
get_footer();
