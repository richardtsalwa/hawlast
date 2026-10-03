<?php
/**
 * Archive template: categories, tags, dates and authors.
 *
 * @package Hawlast
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="wrap">
	<header class="page-head">
		<p class="eyebrow"><?php esc_html_e( 'Archive', 'hawlast' ); ?></p>
		<h1><?php the_archive_title(); ?></h1>
		<?php
		$hawlast_description = get_the_archive_description();

		if ( $hawlast_description ) {
			echo '<div class="lede">' . wp_kses_post( wpautop( $hawlast_description ) ) . '</div>';
		}
		?>
	</header>
</div>

<div class="wrap">
	<?php get_template_part( 'parts/post-card' ); ?>
</div>

<?php
get_footer();