<?php
/**
 * Fallback post index.
 *
 * @package Hawlast
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="wrap">
	<header class="page-head">
		<h1><?php echo esc_html( wp_get_document_title() ); ?></h1>
	</header>
</div>

<div class="wrap">
	<?php get_template_part( 'parts/post-card' ); ?>
</div>

<?php
get_footer();