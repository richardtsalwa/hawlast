<?php
/**
 * Search results.
 *
 * @package Hawlast
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="wrap">
	<header class="page-head">
		<p class="eyebrow"><?php esc_html_e( 'Search', 'hawlast' ); ?></p>
		<h1>
			<?php
			/* translators: %s: the search term. */
			printf(
				esc_html__( 'Results for "%s"', 'hawlast' ),
				esc_html( get_search_query() )
			);
			?>
		</h1>
	</header>

	<?php get_search_form(); ?>

	<?php if ( have_posts() ) : ?>
		<?php get_template_part( 'parts/post-card' ); ?>
	<?php else : ?>
		<p class="search-none"><?php esc_html_e( 'Nothing matched that search. Try a shorter phrase.', 'hawlast' ); ?></p>
	<?php endif; ?>
</div>

<?php
get_footer();