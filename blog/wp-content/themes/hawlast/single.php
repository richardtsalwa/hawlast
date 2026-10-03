<?php
/**
 * Single post template.
 *
 * @package Hawlast
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$hawlast_post_id = get_the_ID();
	?>

	<div class="read">
		<header class="entry-hero">
			<p class="card__meta">
				<a class="link-arrow" href="<?php echo esc_url( hawlast_home_url() ); ?>"><?php esc_html_e( 'Blog', 'hawlast' ); ?></a>
			</p>

			<h1><?php the_title(); ?></h1>

			<p class="entry-meta">
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
				<span aria-hidden="true">/</span>
				<span><?php echo esc_html( get_the_author() ); ?></span>
				<?php if ( get_the_modified_date() !== get_the_date() ) : ?>
					<span aria-hidden="true">/</span>
					<span>
						<?php
						printf(
							/* translators: %s: last updated date. */
							esc_html__( 'Updated %s', 'hawlast' ),
							esc_html( get_the_modified_date() )
						);
						?>
					</span>
				<?php endif; ?>
			</p>
		</header>

		<?php hawlast_hero_image( $hawlast_post_id ); ?>

		<div class="entry-content">
			<?php
			the_content();

			wp_link_pages(
				array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'hawlast' ),
					'after'  => '</div>',
				)
			);
			?>
		</div>

		<?php
		$hawlast_tags = get_the_tag_list( '', ', ' );

		if ( $hawlast_tags && ! is_wp_error( $hawlast_tags ) ) :
			?>
			<footer class="entry-footer">
				<strong><?php esc_html_e( 'Filed under:', 'hawlast' ); ?></strong>
				<span><?php echo wp_kses_post( $hawlast_tags ); ?></span>
			</footer>
		<?php endif; ?>
	</div>

	<?php
	the_post_navigation(
		array(
			'class'              => 'read post-nav',
			'prev_text'          => '<span class="screen-reader-text">' . esc_html__( 'Previous article', 'hawlast' ) . '</span> %title',
			'next_text'          => '<span class="screen-reader-text">' . esc_html__( 'Next article', 'hawlast' ) . '</span> %title',
			'screen_reader_text' => __( 'Article navigation', 'hawlast' ),
		)
	);

	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}

endwhile;

get_footer();