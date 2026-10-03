<?php
/**
 * Prints the permalink of the newest published post that actually has a
 * featured image, so the hero image output can be tested locally.
 *
 * Read-only: prints one permalink, changes nothing.
 */

define( 'ABSPATH', 'D:/xampp/htdocs/hawlast/blog/' );

require ABSPATH . 'wp-load.php';

$posts = get_posts(
	array(
		'post_type'        => 'post',
		'post_status'      => 'publish',
		'numberposts'      => 15,
		'orderby'          => 'date',
		'order'            => 'DESC',
		'suppress_filters' => false,
	)
);

$found = '';

foreach ( $posts as $post ) {
	if ( has_post_thumbnail( $post->ID ) ) {
		$permalink = get_permalink( $post->ID );
		$path      = wp_parse_url( $permalink, PHP_URL_PATH );
		$query     = wp_parse_url( $permalink, PHP_URL_QUERY );

		$found = ( $path ? $path : '/' ) . ( $query ? '?' . $query : '' );
		break;
	}
}

echo ( $found ? $found : '/' ) . "\n";