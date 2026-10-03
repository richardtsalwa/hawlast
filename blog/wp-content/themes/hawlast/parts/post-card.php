<?php
/**
 * Post card loop, shared by the home, archive and search templates.
 *
 * @package Hawlast
 */

defined( 'ABSPATH' ) || exit;
?>
<ul class="post-list">
	<?php
	while ( have_posts() ) :
		the_post();

		$post_id = get_the_ID();
		?>
		<li <?php post_class( 'post-card' ); ?>>
			<?php hawlast_card_thumbnail( $post_id ); ?>

			<div class="card__body">
				<h2 class="card__title">
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</h2>

				<p class="card__meta">
					<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
					<span class="sep" aria-hidden="true">/</span>
					<span><?php echo esc_html( get_the_author() ); ?></span>
					<?php
					$hawlast_categories = get_the_category();

					if ( $hawlast_categories && ! is_wp_error( $hawlast_categories ) ) {
						$hawlast_names = array();

						foreach ( $hawlast_categories as $hawlast_category ) {
							$hawlast_names[] = $hawlast_category->name;
						}

						echo '<span class="sep" aria-hidden="true">/</span> ';
						echo '<span>' . esc_html( implode( ', ', $hawlast_names ) ) . '</span>';
					}
					?>
				</p>

				<p class="card__excerpt"><?php echo hawlast_card_excerpt( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in hawlast_card_excerpt(). ?></p>

				<a class="link-arrow" href="<?php the_permalink(); ?>">
					<?php esc_html_e( 'Read the article', 'hawlast' ); ?>
				</a>
			</div>
		</li>
		<?php
	endwhile;
	?>
</ul>

<?php
the_posts_pagination(
	array(
		'mid_size'           => 1,
		'class'              => 'pagination',
		'prev_text'          => __( 'Previous', 'hawlast' ),
		'next_text'          => __( 'Next', 'hawlast' ),
		'screen_reader_text' => __( 'Posts navigation', 'hawlast' ),
	)
);