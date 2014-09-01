<?php
get_header();
the_post();
$slides = reveal_get_slides();
?>

<?php if ( count( $slides ) > 1 ) : ?>
	<section<?php echo reveal_section_attr( get_post_meta( get_the_ID(), 'wrapper', true ) ) ?>>
<?php endif ?>

<?php foreach ( $slides as $slide ) : ?>

		<section<?php echo reveal_section_attr( $slide ) ?>>
			<?php if ( ! empty( $slide['title'] ) ) : ?>
				<h2><?php echo esc_html( $slide['title'] ) ?></h2>
			<?php endif ?>

			<?php
			if ( ! empty( $slide['content'] ) ) {
				echo reveal_process_html_field( $slide['content'] );
			}
			?>

			<?php if ( ! empty( $slide['notes'] ) ) : ?>
				<aside class="notes">
					<?php echo reveal_process_html_field( $slide['notes'] ) ?>
				</aside>
			<?php endif ?>
		</section>

<?php endforeach ?>

<?php if ( count( $slides ) > 1 ) : ?>
	</section>
<?php endif ?>

<?php get_footer() ?>