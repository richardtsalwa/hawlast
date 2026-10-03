<?php
/**
 * Front page: the latest posts.
 *
 * @package Hawlast
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="wrap">
	<header class="page-head">
		<p class="eyebrow"><?php esc_html_e( 'The HAWLAST blog', 'hawlast' ); ?></p>
		<h1><?php esc_html_e( 'Notes on running a Kenyan business on modern software.', 'hawlast' ); ?></h1>
		<p class="lede"><?php echo esc_html( hawlast_meta_description() ); ?></p>
	</header>
</div>

<div class="wrap">
	<?php get_template_part( 'parts/post-card' ); ?>
</div>

<?php
get_footer();