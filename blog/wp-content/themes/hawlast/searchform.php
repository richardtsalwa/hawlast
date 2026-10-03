<?php
/**
 * Search form.
 *
 * GET form, so no nonce is required: it changes no state on the server.
 *
 * @package Hawlast
 */

defined( 'ABSPATH' ) || exit;

$hawlast_search_id = 'hawlast-search-' . wp_rand( 1000, 9999 );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo esc_attr( $hawlast_search_id ); ?>">
		<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'hawlast' ); ?></span>
		<input
			type="search"
			id="<?php echo esc_attr( $hawlast_search_id ); ?>"
			class="search-field"
			placeholder="<?php esc_attr_e( 'Search the blog', 'hawlast' ); ?>"
			value="<?php echo esc_attr( get_search_query() ); ?>"
			name="s"
		>
	</label>
	<button type="submit" class="search-submit"><?php esc_html_e( 'Search', 'hawlast' ); ?></button>
</form>