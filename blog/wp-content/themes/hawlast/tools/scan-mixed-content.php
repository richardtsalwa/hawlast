<?php
/**
 * Renders the HAWLAST theme through a local PHP server and reports any
 * non-https asset URLs in the output.
 *
 * Usage:
 *   Against a running site:
 *     "d:\xampp\php\php.exe" tools\scan-mixed-content.php https://www.hawlast.com/blog
 *   Against saved HTML (prefix the path with @):
 *     "d:\xampp\php\php.exe" tools\scan-mixed-content.php @C:\temp\rendered.html
 *
 * Read-only: it makes GET requests, or reads a local file, and inspects the
 * HTML. It never writes to the database and never changes the active theme.
 */

$target = isset( $argv[1] ) ? $argv[1] : 'http://127.0.0.1:8899/blog';
$is_file = ( '' !== $target && '@' === substr( $target, 0, 1 ) );

if ( $is_file ) {
	$path = substr( $target, 1 );

	if ( ! file_exists( $path ) ) {
		fwrite( STDERR, "File not found: {$path}\n" );
		exit( 1 );
	}

	$sources = array( 'rendered.html' => (string) file_get_contents( $path ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
} else {
	$base = rtrim( $target, '/' );

	$sources = array(
		'front page' => $base . '/',
		'search'     => $base . '/?s=hosting',
		'404'        => $base . '/no-such-page-here/',
	);
}

$problems = 0;
$checked  = 0;

foreach ( $sources as $label => $url ) {
	if ( $is_file ) {
		$html = $url;
	} else {
		$context = stream_context_create(
			array(
				'http' => array(
					'timeout'       => 30,
					'ignore_errors' => true,
				),
			)
		);

		$html = @file_get_contents( $url, false, $context ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	}

	if ( false === $html || '' === $html ) {
		echo "REQUEST FAILED OR EMPTY: {$label} ({$url})\n";
		++$problems;
		continue;
	}

	echo "\n--- {$label}: {$url} ---\n";

	// Collect every URL the browser would fetch as a subresource.
	preg_match_all( '#(?:src|href|content)=(["\'])(https?:)?//[^"\']+\1#i', $html, $matches );

	$urls = array_values( array_unique( $matches[0] ) );
	sort( $urls );

	$bad = 0;

	foreach ( $urls as $entry ) {
		++$checked;

		// Namespaces and profile identifiers are not requests.
		if ( preg_match( '#xmlns|schema\.org|w3\.org|gmpg\.org|xfn#i', $entry ) ) {
			continue;
		}

		if ( preg_match( '#http://#i', $entry ) ) {
			echo "INSECURE:          {$entry}\n";
			++$bad;
			continue;
		}

		if ( preg_match( '#=["\']//#', $entry ) ) {
			echo "PROTOCOL-RELATIVE: {$entry}\n";
			++$bad;
		}
	}

	$problems += $bad;

	printf( "Checked %d URLs, %d insecure.\n", count( $urls ), $bad );

	// Report the head signals the brief asks about.
	foreach ( array( 'rel="canonical"', 'og:url', 'og:image', 'twitter:card', 'fetchpriority', 'rel="preload"' ) as $needle ) {
		printf( "  %-16s %s\n", $needle, false !== strpos( $html, $needle ) ? 'present' : 'ABSENT' );
	}

	// Emoji and bloat scripts should be gone.
	foreach ( array( 'emoji', 'wp-embed.min.js', 'rsd_link', 'wlwmanifest', '/wp-json/oembed' ) as $needle ) {
		if ( false !== strpos( $html, $needle ) ) {
			echo "  BLOAT STILL PRESENT: {$needle}\n";
			++$problems;
		}
	}
}

printf( "\nTotal URLs checked: %d\nProblems found: %d\n", $checked, $problems );

exit( $problems > 0 ? 1 : 0 );