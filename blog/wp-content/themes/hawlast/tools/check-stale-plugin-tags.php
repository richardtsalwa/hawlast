<?php
/**
 * Read-only probe: reports whether the retired AddThis / Quantcast tags are still
 * stored in the options table after the plugin files were removed from disk.
 *
 * Prints option names only. No values are read or printed.
 */

define( 'ABSPATH', 'D:/xampp/htdocs/hawlast/blog/' );

require ABSPATH . 'wp-load.php';

global $wpdb;

$rows = $wpdb->get_results(
	"SELECT option_name FROM {$wpdb->options}
	 WHERE option_name LIKE '%addthis%'
	    OR option_name LIKE '%quantserve%'
	    OR option_name LIKE '%qacct%'
	 ORDER BY option_name",
	ARRAY_A
);

if ( $rows ) {
	foreach ( $rows as $row ) {
		echo "STALE OPTION: {$row['option_name']}\n";
	}
} else {
	echo "No AddThis/Quantcast options remain in the database.\n";
}

echo 'Active plugins: ' . implode( ', ', (array) get_option( 'active_plugins', array() ) ) . "\n";