<?php
/**
 * Static page template.
 *
 * @package Hawlast
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>

	<div class="read">
		<header class="entry-hero">
			<h1><?php the_title(); ?></h1>
		</header>

		<?php hawlast_hero_image( get_the_ID() ); ?>

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
	</div>

	<?php
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}

endwhile;

get_footer();