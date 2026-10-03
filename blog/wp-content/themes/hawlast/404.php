<?php
/**
 * 404 template.
 *
 * @package Hawlast
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="wrap">
	<div class="notice-404">
		<h1><?php esc_html_e( '404', 'hawlast' ); ?></h1>
		<p><?php esc_html_e( 'That page has moved or never existed. The articles below are the ones people read most.', 'hawlast' ); ?></p>
		<a class="btn btn-fill btn-lg" href="<?php echo esc_url( hawlast_home_url() ); ?>"><?php esc_html_e( 'Back to the blog', 'hawlast' ); ?></a>
	</div>
</div>

<?php
get_footer();